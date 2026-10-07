<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use VanOns\FilamentAttachmentLibrary\ViewModels\AttachmentViewModel;
use VanOns\LaravelAttachmentLibrary\Models\Attachment;

function renderHeading(Attachment $attachment): string
{
    return view('filament-attachment-library::actions.edit-attachment.heading', [
        'attachment' => new AttachmentViewModel($attachment->fresh()),
    ])->render();
}

beforeEach(function () {
    $this->loadLaravelMigrations();

    Storage::fake('test');
    Config::set('attachment-library.disk', 'test');
});

it('renders without an edited line when created_at is missing', function () {
    $attachment = Attachment::factory()->create(['disk' => 'test', 'mime_type' => 'text/plain']);
    Attachment::whereKey($attachment->id)->update(['created_at' => null, 'updated_at' => now()]);

    expect(renderHeading($attachment))
        ->not->toContain('Uploaded')
        ->not->toContain('Edited');
});

it('renders the uploaded and edited lines when both timestamps are set', function () {
    $attachment = Attachment::factory()->create(['disk' => 'test', 'mime_type' => 'text/plain']);
    Attachment::whereKey($attachment->id)->update([
        'created_at' => Carbon::parse('2026-01-01'),
        'updated_at' => Carbon::parse('2026-02-01'),
    ]);

    expect(renderHeading($attachment))
        ->toContain('Uploaded 1 Jan 2026')
        ->toContain('Edited 1 Feb 2026');
});
