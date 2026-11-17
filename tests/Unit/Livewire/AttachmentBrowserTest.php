<?php

use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Livewire\Exceptions\EventHandlerDoesNotExist;
use Livewire\Livewire;
use VanOns\FilamentAttachmentLibrary\Livewire\AttachmentBrowser;
use VanOns\FilamentAttachmentLibrary\Support\Path;
use VanOns\LaravelAttachmentLibrary\Models\Attachment;

function attachment(string $name, ?string $path = null): Attachment
{
    Storage::disk('test')->put(Path::join($path, "{$name}.txt"), 'contents');

    return Attachment::factory()->create([
        'name' => $name,
        'extension' => 'txt',
        'mime_type' => 'text/plain',
        'path' => $path,
    ]);
}

function directories(string ...$paths): void
{
    foreach ($paths as $path) {
        Storage::disk('test')->makeDirectory($path);
    }
}

beforeEach(function () {
    Storage::fake('test');
    Config::set('attachment-library.disk', 'test');
    Config::set('attachment-library.auto_sync', false);

    // The browser looks up who uploaded each attachment.
    $this->loadLaravelMigrations();
});

it('lists directories from A to Z, whatever the case and the sort of the files', function () {
    directories('bredazuid', 'Blog', 'amsterdam');

    Livewire::test(AttachmentBrowser::class)
        ->set('sortBy', 'name_desc')
        ->assertViewHas('directories', fn ($directories) => $directories->pluck('name')->all() === ['amsterdam', 'Blog', 'bredazuid']);
});

it('sorts files by name whatever the case', function () {
    attachment('beta');
    attachment('Alpha');
    attachment('Gamma');

    Livewire::test(AttachmentBrowser::class)
        ->set('sortBy', 'name_asc')
        ->assertViewHas('attachments', fn ($attachments) => collect($attachments->items())->pluck('name')->all() === ['Alpha', 'beta', 'Gamma']);
});

it('shows directory cards and no tree by default', function () {
    directories('logos');

    Livewire::test(AttachmentBrowser::class)
        ->assertViewHas('tree', null)
        ->assertDontSeeHtml('data-directories');
});

describe('directory tree', function () {
    beforeEach(fn () => Config::set('filament-attachment-library.directory_navigation', 'tree'));

    it('shows every directory with the number of files directly inside it', function () {
        directories('logos/partners', 'team');
        attachment('root');
        attachment('logo', 'logos');
        attachment('partner-a', 'logos/partners');
        attachment('partner-b', 'logos/partners');

        Livewire::test(AttachmentBrowser::class)
            ->assertViewHas('tree', function (array $tree) {
                return $tree['rootCount'] === 1
                    && array_column($tree['nodes'], 'path') === ['logos', 'team']
                    && $tree['nodes'][0]['count'] === 1
                    && $tree['nodes'][0]['children'][0]['count'] === 2;
            })
            ->assertSeeHtml('data-directory="logos/partners"');
    });

    it('starts at the base path and shows nothing above or beside it', function () {
        directories('tenants/a/logos', 'tenants/b/secret', 'shared');
        attachment('mine', 'tenants/a');
        attachment('theirs', 'tenants/b');

        Livewire::test(AttachmentBrowser::class, ['basePath' => 'tenants/a'])
            ->assertViewHas('tree', function (array $tree) {
                return $tree['rootCount'] === 1 && array_column($tree['nodes'], 'path') === ['logos'];
            })
            ->assertDontSeeHtml('secret')
            ->assertDontSeeHtml('shared');
    });

    it('remembers the open directories per base path', function () {
        directories('tenants/a/logos', 'tenants/b/logos');

        Livewire::test(AttachmentBrowser::class, ['basePath' => 'tenants/a'])
            ->set('expandedDirectories', ['logos', '../b', 12])
            ->call('rememberTree')
            ->assertSet('expandedDirectories', ['logos', 'b']);

        Livewire::test(AttachmentBrowser::class, ['basePath' => 'tenants/a'])
            ->assertSet('expandedDirectories', ['logos', 'b']);

        Livewire::test(AttachmentBrowser::class, ['basePath' => 'tenants/b'])
            ->assertSet('expandedDirectories', []);
    });

    it('keeps the tree as it was last changed, whichever browser changed it', function () {
        directories('logos');

        $page = Livewire::test(AttachmentBrowser::class);
        $modal = Livewire::test(AttachmentBrowser::class, ['scope' => 'attachment-modal', 'trackUrl' => false]);

        $page->set('expandedDirectories', ['logos'])->set('treeCollapsed', true);

        // Any request of the modal, which still holds the state it was loaded with.
        $modal->call('highlight', null);

        Livewire::test(AttachmentBrowser::class)
            ->assertSet('expandedDirectories', ['logos'])
            ->assertSet('treeCollapsed', true);

        $modal->call('openModal', modalId: 'attachment-modal')
            ->assertSet('expandedDirectories', ['logos'])
            ->assertSet('treeCollapsed', true);
    });

    it('opens a directory named like the base path as that directory', function () {
        directories('media/media/logos');

        Livewire::test(AttachmentBrowser::class, ['basePath' => 'media'])
            ->call('openDirectory', 'media')
            ->assertSet('currentPath', 'media')
            ->call('openDirectory', 'media/logos')
            ->assertSet('currentPath', 'media/logos');
    });

    it('leaves a search behind when a directory is opened from the tree', function () {
        directories('logos');

        Livewire::test(AttachmentBrowser::class)
            ->set('search', 'logo')
            ->call('openDirectory', 'logos')
            ->assertSet('search', '')
            ->assertSet('currentPath', 'logos');
    });

    it('creates a subdirectory in the directory of the row it was asked from', function () {
        directories('logos');

        Livewire::test(AttachmentBrowser::class)
            ->call('createSubdirectory', 'logos')
            ->assertSet('currentPath', 'logos')
            ->assertActionMounted('createDirectory')
            ->setActionData(['name' => 'partners'])
            ->callMountedAction();

        expect(Storage::disk('test')->directoryExists('logos/partners'))->toBeTrue();
    });

    it('renames a directory that is not in the open one, checking the name beside it', function () {
        directories('logos/old', 'logos/taken', 'free');

        $browser = Livewire::test(AttachmentBrowser::class);

        // "free" exists in the root, but not beside logos/old.
        $browser->callAction('renameDirectory', data: ['name' => 'free'], arguments: ['name' => 'old', 'full_path' => 'logos/old'])
            ->assertHasNoFormErrors();

        expect(Storage::disk('test')->directoryExists('logos/free'))->toBeTrue();

        $browser->callAction('renameDirectory', data: ['name' => 'taken'], arguments: ['name' => 'free', 'full_path' => 'logos/free'])
            ->assertHasFormErrors(['name']);

        expect(Storage::disk('test')->directoryExists('logos/free'))->toBeTrue();
    });

    it('does not rename or delete a directory outside the base path, or the base path itself', function () {
        directories('tenants/a/logos', 'tenants/b/secret');

        $browser = Livewire::test(AttachmentBrowser::class, ['basePath' => 'tenants/a']);

        $browser->callAction('deleteDirectory', arguments: ['full_path' => 'tenants/b/secret']);
        $browser->callAction('deleteDirectory', arguments: ['full_path' => 'tenants/a/..\\b\\secret']);
        $browser->callAction('deleteDirectory', arguments: ['full_path' => 'tenants/a']);
        $browser->callAction('renameDirectory', data: ['name' => 'taken-over'], arguments: ['name' => 'secret', 'full_path' => 'tenants/b/secret']);

        expect(Storage::disk('test')->directoryExists('tenants/b/secret'))->toBeTrue()
            ->and(Storage::disk('test')->directoryExists('tenants/a/logos'))->toBeTrue();

        $browser->callAction('deleteDirectory', arguments: ['full_path' => 'tenants/a/logos']);

        expect(Storage::disk('test')->directoryExists('tenants/a/logos'))->toBeFalse();
    });

    it('keeps what another browser opened when this one deletes a directory', function () {
        directories('logos', 'team', 'old');

        $page = Livewire::test(AttachmentBrowser::class);
        $modal = Livewire::test(AttachmentBrowser::class, ['scope' => 'attachment-modal', 'trackUrl' => false]);

        $modal->set('expandedDirectories', ['logos', 'team', 'old']);

        // The page still holds the empty list it was loaded with.
        $page->callAction('deleteDirectory', arguments: ['full_path' => 'old']);

        Livewire::test(AttachmentBrowser::class)->assertSet('expandedDirectories', ['logos', 'team']);
    });

    it('steps out of a directory that is deleted', function () {
        directories('logos/partners/2026');

        Livewire::test(AttachmentBrowser::class)
            ->set('currentPath', 'logos/partners/2026')
            ->set('expandedDirectories', ['logos', 'logos/partners'])
            ->callAction('deleteDirectory', arguments: ['full_path' => 'logos/partners'])
            ->assertSet('currentPath', 'logos')
            ->assertSet('expandedDirectories', ['logos']);

        expect(Storage::disk('test')->directoryExists('logos/partners'))->toBeFalse();
    });

    it('follows a directory that is renamed', function () {
        directories('logos/partners/2026');

        Livewire::test(AttachmentBrowser::class)
            ->set('currentPath', 'logos/partners/2026')
            ->set('expandedDirectories', ['logos', 'logos/partners'])
            ->callAction('renameDirectory', data: ['name' => 'sponsors'], arguments: ['name' => 'partners', 'full_path' => 'logos/partners'])
            ->assertSet('currentPath', 'logos/sponsors/2026')
            ->assertSet('expandedDirectories', ['logos', 'logos/sponsors']);
    });
});

describe('moving attachments by dragging', function () {
    it('moves attachments into a directory', function () {
        directories('logos');
        $first = attachment('first');
        $second = attachment('second');

        Livewire::test(AttachmentBrowser::class)
            ->set('selected', [$first->id, $second->id])
            ->call('moveAttachments', [$first->id, $second->id], 'logos')
            // A field that picked these still has them picked.
            ->assertSet('selected', [$first->id, $second->id]);

        expect($first->refresh()->path)->toBe('logos')
            ->and($second->refresh()->path)->toBe('logos')
            ->and(Storage::disk('test')->exists('logos/first.txt'))->toBeTrue()
            ->and(Storage::disk('test')->exists('first.txt'))->toBeFalse();

        Notification::assertNotified('2 files moved to logos.');
    });

    it('moves attachments to the root of the base path', function () {
        directories('tenants/a/logos');
        $attachment = attachment('logo', 'tenants/a/logos');

        Livewire::test(AttachmentBrowser::class, ['basePath' => 'tenants/a'])
            ->call('moveAttachments', [$attachment->id], null);

        expect($attachment->refresh()->path)->toBe('tenants/a');

        // Named as the user knows it, not after the last part of the base path.
        Notification::assertNotified('1 file moved to Home.');
    });

    it('moves into a directory named 0', function () {
        directories('tenants/a/0');
        $attachment = attachment('logo', 'tenants/a');

        Livewire::test(AttachmentBrowser::class, ['basePath' => 'tenants/a'])
            ->call('moveAttachments', [$attachment->id], '0');

        expect($attachment->refresh()->path)->toBe('tenants/a/0');
    });

    it('does not move into a directory that does not exist', function () {
        $attachment = attachment('first');

        Livewire::test(AttachmentBrowser::class)
            ->call('moveAttachments', [$attachment->id], 'missing');

        expect($attachment->refresh()->path)->toBeNull();
    });

    it('cannot reach outside the base path', function () {
        directories('tenants/a/logos', 'tenants/b');
        $theirs = attachment('theirs', 'tenants/b');
        $mine = attachment('mine', 'tenants/a');

        $browser = Livewire::test(AttachmentBrowser::class, ['basePath' => 'tenants/a']);

        // Somebody else's attachment, by its id.
        $browser->call('moveAttachments', [$theirs->id], 'logos');
        expect($theirs->refresh()->path)->toBe('tenants/b');

        // Somebody else's directory, by climbing out of the base path.
        $browser->call('moveAttachments', [$mine->id], '../b');
        expect($mine->refresh()->path)->toBe('tenants/a');

        // The same with a backslash, which the filesystem reads as a slash.
        $browser->call('moveAttachments', [$mine->id], '..\\b');
        expect($mine->refresh()->path)->toBe('tenants/a')
            ->and(Storage::disk('test')->exists('tenants/b/mine.txt'))->toBeFalse();
    });

    it('leaves an attachment in place when the directory already has a file with that name', function () {
        directories('logos');
        $attachment = attachment('logo');
        attachment('logo', 'logos');

        Livewire::test(AttachmentBrowser::class)
            ->call('moveAttachments', [$attachment->id], 'logos');

        expect($attachment->refresh()->path)->toBeNull();
    });

    it('does nothing while the browser is disabled', function () {
        directories('logos');
        $attachment = attachment('first');

        Livewire::test(AttachmentBrowser::class)
            ->set('disabled', true)
            ->call('moveAttachments', [$attachment->id], 'logos');

        expect($attachment->refresh()->path)->toBeNull();
    });

    it('puts the attachments of the last move back', function () {
        directories('logos');
        $attachment = attachment('first');

        $browser = Livewire::test(AttachmentBrowser::class)
            ->call('moveAttachments', [$attachment->id], 'logos');

        $browser->dispatch('undo-attachment-move.page', token: session('filament-attachment-library.undo-move.token'));

        expect($attachment->refresh()->path)->toBeNull()
            ->and(Storage::disk('test')->exists('first.txt'))->toBeTrue()
            ->and(session('filament-attachment-library.undo-move'))->toBeNull();
    });

    it('says so when a file cannot be put back', function () {
        directories('logos');
        $attachment = attachment('first');

        $browser = Livewire::test(AttachmentBrowser::class)
            ->call('moveAttachments', [$attachment->id], 'logos');

        // Something else takes the old place before the undo.
        Storage::disk('test')->put('first.txt', 'other contents');

        $browser->call('undoMove', session('filament-attachment-library.undo-move.token'));

        expect($attachment->refresh()->path)->toBe('logos');

        Notification::assertNotified('Not every file could be moved back: something else has taken its old place.');
    });

    it('does not undo with a token of an earlier move, or for another browser', function () {
        directories('logos');
        $attachment = attachment('first');

        $browser = Livewire::test(AttachmentBrowser::class)
            ->call('moveAttachments', [$attachment->id], 'logos');

        $token = session('filament-attachment-library.undo-move.token');

        $browser->call('undoMove', 'stale-token');

        expect(fn () => $browser->dispatch('undo-attachment-move.attachment-modal', token: $token))
            ->toThrow(EventHandlerDoesNotExist::class)
            ->and($attachment->refresh()->path)->toBe('logos');
    });
});
