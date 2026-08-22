<?php

use Illuminate\Http\UploadedFile;
use VanOns\FilamentAttachmentLibrary\Rules\HasValidExtension;

function validateHasValidExtension(mixed $value): bool
{
    return !validator(['name' => $value], ['name' => [new HasValidExtension()]])->fails();
}

it('passes a filename with an extension', function () {
    expect(validateHasValidExtension('document.pdf'))->toBeTrue();
});

it('fails a filename without an extension', function () {
    expect(validateHasValidExtension('document'))->toBeFalse();
});

it('passes an uploaded file with an extension', function () {
    $file = UploadedFile::fake()->create('document.pdf');

    expect(validateHasValidExtension($file))->toBeTrue();
});

it('fails an uploaded file without an extension', function () {
    $file = UploadedFile::fake()->create('document');

    expect(validateHasValidExtension($file))->toBeFalse();
});
