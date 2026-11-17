<?php

use VanOns\FilamentAttachmentLibrary\Support\DirectoryTree;

it('nests directories below their parents', function () {
    $tree = DirectoryTree::build(['logos', 'logos/partners', 'team']);

    expect(array_column($tree, 'path'))->toBe(['logos', 'team'])
        ->and(array_column($tree[0]['children'], 'path'))->toBe(['logos/partners'])
        ->and($tree[1]['children'])->toBe([]);
});

it('sorts every level from A to Z, whatever the case', function () {
    $tree = DirectoryTree::build(['bredazuid', 'Blog', 'amsterdam', 'CO Homepage', 'blog 10', 'blog 2']);

    expect(array_column($tree, 'name'))->toBe(['amsterdam', 'Blog', 'blog 2', 'blog 10', 'bredazuid', 'CO Homepage']);
});

it('gives paths relative to the base path and leaves out everything outside it', function () {
    $tree = DirectoryTree::build(
        ['tenants/a', 'tenants/a/logos', 'tenants/ab', 'tenants/b/logos', 'shared'],
        'tenants/a',
        ['tenants/a/logos' => 2, 'tenants/b/logos' => 5],
    );

    expect($tree)->toHaveCount(1)
        ->and($tree[0]['path'])->toBe('logos')
        ->and($tree[0]['count'])->toBe(2);
});

it('counts the files directly inside a directory', function () {
    $tree = DirectoryTree::build(['logos', 'logos/partners'], null, ['logos' => 3, 'logos/partners' => 1]);

    expect($tree[0]['count'])->toBe(3)
        ->and($tree[0]['children'][0]['count'])->toBe(1);
});

it('keeps a directory whose name is a number', function () {
    $tree = DirectoryTree::build(['2026', '2026/09']);

    expect($tree[0]['path'])->toBe('2026')
        ->and($tree[0]['children'][0]['path'])->toBe('2026/09');
});

it('adds a parent that the listing left out', function () {
    $tree = DirectoryTree::build(['logos/partners']);

    expect($tree[0]['path'])->toBe('logos')
        ->and($tree[0]['children'][0]['path'])->toBe('logos/partners');
});
