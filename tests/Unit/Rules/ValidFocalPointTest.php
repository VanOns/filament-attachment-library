<?php

use VanOns\FilamentAttachmentLibrary\Rules\ValidFocalPoint;

function validateFocalPoint(mixed $value): bool
{
    return !validator(['focal_point' => $value], ['focal_point' => [new ValidFocalPoint()]])->fails();
}

it('passes a null focal point', function () {
    expect(validateFocalPoint(null))->toBeTrue();
});

it('passes valid x/y coordinates', function () {
    expect(validateFocalPoint(['x' => 25, 'y' => 75]))->toBeTrue();
});

it('passes boundary coordinates of 0 and 100', function () {
    expect(validateFocalPoint(['x' => 0, 'y' => 100]))->toBeTrue();
});

it('fails a non-array value', function () {
    expect(validateFocalPoint('not-an-array'))->toBeFalse();
});

it('fails when x is missing', function () {
    expect(validateFocalPoint(['y' => 50]))->toBeFalse();
});

it('fails when a coordinate is below 0', function () {
    expect(validateFocalPoint(['x' => -1, 'y' => 50]))->toBeFalse();
});

it('fails when a coordinate is above 100', function () {
    expect(validateFocalPoint(['x' => 50, 'y' => 101]))->toBeFalse();
});

it('fails when a coordinate is not numeric', function () {
    expect(validateFocalPoint(['x' => 'not-a-number', 'y' => 50]))->toBeFalse();
});
