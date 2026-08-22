<?php

use VanOns\FilamentAttachmentLibrary\Support\Path;

it('returns null for a null path', function () {
    expect(Path::sanitize(null))->toBeNull();
});

it('returns null for an empty string', function () {
    expect(Path::sanitize(''))->toBeNull();
});

it('leaves a normal path untouched', function () {
    expect(Path::sanitize('folder/subfolder'))->toBe('folder/subfolder');
});

it('drops dot segments', function () {
    expect(Path::sanitize('folder/./subfolder'))->toBe('folder/subfolder');
});

it('drops parent-directory segments', function () {
    expect(Path::sanitize('folder/../../etc/passwd'))->toBe('folder/etc/passwd');
});

it('drops empty segments from doubled slashes', function () {
    expect(Path::sanitize('folder//subfolder'))->toBe('folder/subfolder');
});

it('returns null when every segment is stripped', function () {
    expect(Path::sanitize('../..'))->toBeNull();
});
