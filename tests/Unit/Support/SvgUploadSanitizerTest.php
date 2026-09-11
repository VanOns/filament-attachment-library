<?php

use Illuminate\Http\UploadedFile;
use VanOns\FilamentAttachmentLibrary\Support\SvgUploadSanitizer;

function fakeSvg(string $content, string $name = 'test.svg'): UploadedFile
{
    return UploadedFile::fake()->createWithContent($name, $content)->mimeType('image/svg+xml');
}

it('leaves non-svg files untouched', function () {
    $file = UploadedFile::fake()->image('test.png');
    $original = file_get_contents($file->getRealPath());

    $sanitized = SvgUploadSanitizer::sanitize($file);

    expect($sanitized)->toBe($file);
    expect($sanitized->get())->toBe($original);
});

it('strips script tags from an svg', function () {
    $file = fakeSvg(<<<'SVG'
<?xml version="1.0" encoding="UTF-8"?>
<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10">
  <script>alert('xss')</script>
  <rect width="10" height="10" fill="red"/>
</svg>
SVG);

    $sanitized = SvgUploadSanitizer::sanitize($file);

    expect($sanitized)->not->toBeNull();
    expect($sanitized->get())->not->toContain('<script');
});

it('strips event handler attributes from an svg', function () {
    $file = fakeSvg(<<<'SVG'
<?xml version="1.0" encoding="UTF-8"?>
<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" onload="alert('xss')">
  <rect width="10" height="10" fill="red"/>
</svg>
SVG);

    $sanitized = SvgUploadSanitizer::sanitize($file);

    expect($sanitized)->not->toBeNull();
    expect($sanitized->get())->not->toContain('onload');
});

it('fails when the svg cannot be parsed', function () {
    $file = fakeSvg('not valid xml at all <<<');

    expect(SvgUploadSanitizer::sanitize($file))->toBeNull();
});

it('detects an svg by extension even with a generic mime type', function () {
    $file = UploadedFile::fake()->createWithContent('test.svg', <<<'SVG'
<?xml version="1.0" encoding="UTF-8"?>
<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10">
  <script>alert('xss')</script>
</svg>
SVG)->mimeType('application/octet-stream');

    $sanitized = SvgUploadSanitizer::sanitize($file);

    expect($sanitized)->not->toBeNull();
    expect($sanitized->get())->not->toContain('<script');
});
