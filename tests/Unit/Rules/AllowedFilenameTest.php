<?php

use Illuminate\Http\UploadedFile;
use VanOns\FilamentAttachmentLibrary\Rules\AllowedFilename;

function validateAllowedFilename(mixed $value): bool
{
    return !validator(['name' => $value], ['name' => [new AllowedFilename()]])->fails();
}

it('passes a plain filename', function () {
    expect(validateAllowedFilename('my-file_name.jpg'))->toBeTrue();
});

it('fails a filename that is just a dot', function () {
    expect(validateAllowedFilename('.'))->toBeFalse();
});

it('fails a filename that is just two dots', function () {
    expect(validateAllowedFilename('..'))->toBeFalse();
});

it('fails a filename with disallowed characters', function () {
    expect(validateAllowedFilename('bad/name'))->toBeFalse();
});

it('passes an uploaded file with a valid name', function () {
    $file = UploadedFile::fake()->image('valid-name.png');

    expect(validateAllowedFilename($file))->toBeTrue();
});

it('fails an uploaded file with disallowed characters in its name', function () {
    $file = UploadedFile::fake()->image('bad*name.png');

    expect(validateAllowedFilename($file))->toBeFalse();
});
