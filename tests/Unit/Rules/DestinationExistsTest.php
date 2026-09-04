<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use VanOns\FilamentAttachmentLibrary\Rules\DestinationExists;
use VanOns\LaravelAttachmentLibrary\Models\Attachment;

function validateDestination(?string $path, ?int $attachmentId, mixed $value): bool
{
    return !validator(['name' => $value], ['name' => [new DestinationExists($path, $attachmentId)]])->fails();
}

beforeEach(function () {
    Storage::fake('test');
    Config::set('attachment-library.disk', 'test');
});

it('passes when no file exists at the destination', function () {
    $file = UploadedFile::fake()->image('new-file.png');

    expect(validateDestination('folder', null, $file))->toBeTrue();
});

it('fails when an uploaded file would overwrite an existing file', function () {
    Storage::disk('test')->put('folder/existing.png', 'contents');

    $file = UploadedFile::fake()->image('existing.png');

    expect(validateDestination('folder', null, $file))->toBeFalse();
});

it('passes a rename when no file exists with the new name', function () {
    $attachment = Attachment::create([
        'name' => 'original',
        'extension' => 'png',
        'disk' => 'test',
        'mime_type' => 'image/png',
        'path' => 'folder',
        'size' => 10,
    ]);

    expect(validateDestination('folder', $attachment->id, 'renamed'))->toBeTrue();
});

it('fails a rename when a file already exists with the new name', function () {
    Storage::disk('test')->put('folder/taken.png', 'contents');

    $attachment = Attachment::create([
        'name' => 'original',
        'extension' => 'png',
        'disk' => 'test',
        'mime_type' => 'image/png',
        'path' => 'folder',
        'size' => 10,
    ]);

    expect(validateDestination('folder', $attachment->id, 'taken'))->toBeFalse();
});
