<?php

return [
    'browser' => [
        'empty' => [
            'title' => 'No files or folders found',
            'description' => 'Upload a new file or navigate to a different path.',
            'button' => 'Back to home',
        ],
    ],
    'info' => [
        'empty' => [
            'title' => 'Select a file',
            'description' => 'Select a file to view more information.',
        ],
        'details' => [
            'sections' => [
                'meta' => [
                    'header' => 'File metadata',
                    'alt' => 'Alt-text',
                    'caption' => 'Caption',
                    'title' => 'Title',
                    'description' => 'Description'
                ],
                'image' => [
                    'header' => 'Image metadata',
                    'dimensions' => 'Dimensions',
                    'bits' => 'Bits',
                    'channels' => 'Channels',
                ],
                'date' => [
                    'header' => 'Dates',
                    'created_by' => 'Uploaded by',
                    'created_at' => 'Uploaded at',
                    'updated_by' => 'Updated by',
                    'updated_at' => 'Updated at',
                ],
            ],
            'size' => 'File size',
            'mime_type' => 'MIME-type',
            'path' => 'Path',
            'url' => 'Link',
        ],
    ],
    'actions' => [
        'attachment' => [
            'open' => 'Open file',
            'edit' => 'Modify file',
            'delete' => 'Remove file',
            'upload' => 'Upload file',
        ],
        'directory' => [
            'rename' => 'Rename directory',
            'delete' => 'Remove directory',
            'create' => 'Create directory',
        ],
    ],
    'header-actions' => [
        'created_at_ascending' => 'Upload date ascending',
        'created_at_descending' => 'Upload date descending',
        'name_ascending' => 'Name ascending',
        'name_descending' => 'Name descending',
        'options' => 'Options',
    ],
    'title' => 'File manager',
    'group' => 'Files',
    'search' => 'Search...',
    'home' => 'Home',
    'close' => 'Close',
];
