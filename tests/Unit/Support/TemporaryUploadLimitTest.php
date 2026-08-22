<?php

use Illuminate\Support\Facades\Config;
use VanOns\FilamentAttachmentLibrary\Support\TemporaryUploadLimit;

it('reads the default livewire upload limit when none is configured', function () {
    expect(TemporaryUploadLimit::bytes())->toBe(12288 * 1024);
    expect(TemporaryUploadLimit::label())->toBe('12 MB');
});

it('parses a max rule from a pipe-delimited rule string', function () {
    Config::set('livewire.temporary_file_upload.rules', 'required|max:5000');

    expect(TemporaryUploadLimit::bytes())->toBe(5000 * 1024);
    expect(TemporaryUploadLimit::label())->toBe('5 MB');
});

it('parses a max rule from an array of rules', function () {
    Config::set('livewire.temporary_file_upload.rules', ['required', 'file', 'max:2048']);

    expect(TemporaryUploadLimit::bytes())->toBe(2048 * 1024);
    expect(TemporaryUploadLimit::label())->toBe('2 MB');
});

it('returns null when no max rule is configured', function () {
    Config::set('livewire.temporary_file_upload.rules', ['required', 'file']);

    expect(TemporaryUploadLimit::bytes())->toBeNull();
    expect(TemporaryUploadLimit::label())->toBeNull();
});
