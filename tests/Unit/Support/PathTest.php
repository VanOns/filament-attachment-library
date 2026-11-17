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

it('reads a backslash as a separator, so it cannot hide a parent-directory segment', function () {
    expect(Path::sanitize('folder\\..\\other'))->toBe('folder/other')
        ->and(Path::sanitize('..\\other'))->toBe('other');
});

it('tells whether a path is the base path or below it', function () {
    expect(Path::isWithin('tenants/a', 'tenants/a'))->toBeTrue()
        ->and(Path::isWithin('tenants/a/logos', 'tenants/a'))->toBeTrue()
        ->and(Path::isWithin('tenants/ab', 'tenants/a'))->toBeFalse()
        ->and(Path::isWithin('tenants/b', 'tenants/a'))->toBeFalse()
        ->and(Path::isWithin(null, 'tenants/a'))->toBeFalse()
        ->and(Path::isWithin(null, null))->toBeTrue();
});

it('tells whether a path is below the base path, not the base path itself', function () {
    expect(Path::isBelow('tenants/a/logos', 'tenants/a'))->toBeTrue()
        ->and(Path::isBelow('tenants/a', 'tenants/a'))->toBeFalse()
        ->and(Path::isBelow('logos', null))->toBeTrue()
        ->and(Path::isBelow(null, null))->toBeFalse();
});

it('gives the parent and the name of a path', function () {
    expect(Path::parent('logos/partners/2026'))->toBe('logos/partners')
        ->and(Path::parent('logos'))->toBeNull()
        ->and(Path::name('logos/partners/2026'))->toBe('2026')
        ->and(Path::name('logos'))->toBe('logos');
});

it('gives a path as seen from the base path', function () {
    expect(Path::relativeTo('tenants/a/logos', 'tenants/a'))->toBe('logos')
        ->and(Path::relativeTo('tenants/a', 'tenants/a'))->toBeNull()
        ->and(Path::relativeTo('tenants/ab/logos', 'tenants/a'))->toBe('tenants/ab/logos')
        ->and(Path::relativeTo('logos', null))->toBe('logos')
        ->and(Path::relativeTo(null, 'tenants/a'))->toBeNull();
});

it('joins the parts of a path, leaving out the root', function () {
    expect(Path::join('tenants/a', 'logos'))->toBe('tenants/a/logos')
        ->and(Path::join(null, 'logos'))->toBe('logos')
        ->and(Path::join('tenants/a', null))->toBe('tenants/a')
        ->and(Path::join('0', '0'))->toBe('0/0')
        ->and(Path::join(null, null))->toBeNull();
});
