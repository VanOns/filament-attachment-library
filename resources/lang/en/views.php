<?php

return [
    'browser' => [
        'drop' => [
            'prompt' => 'Drop files here to upload',
            'uploading' => 'Uploading…',
        ],
        'empty' => [
            'title' => 'No files or folders found',
            'title_files' => 'No files in this directory',
            'description' => 'Upload a new file or navigate to a different path.',
            'button' => 'Back to home',
        ],
        'search_results' => 'Search results for:',
        'file_count' => ':count file|:count files',
    ],
    'info' => [
        'empty' => [
            'title' => 'Select a file',
            'description' => 'Select a file to view more information.',
        ],
        'details' => [
            'sections' => [
                'meta' => [
                    'id' => 'ID',
                    'header' => 'File metadata',
                    'alt' => 'Alt-text',
                    'caption' => 'Caption',
                    'title' => 'Title',
                    'description' => 'Description',
                ],
                'image' => [
                    'header' => 'Image metadata',
                    'dimensions' => 'Dimensions',
                    'bits' => 'Bits',
                    'channels' => 'Channels',
                ],
                'video' => [
                    'header' => 'Video metadata',
                    'dimensions' => 'Dimensions',
                    'duration' => 'Duration',
                    'captions' => 'Captions',
                ],
                'more' => 'More details',
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
            'modal_title' => 'File information',
        ],
    ],
    'edit' => [
        'file' => 'File',
        'uploaded' => 'Uploaded :date by :user',
        'edited' => 'Edited :date by :user',
        'uploaded_at' => 'Uploaded :date',
        'edited_at' => 'Edited :date',
        'root' => '/ (root)',
        'preview' => 'Preview',
        'preview_lines' => 'First :count lines',
    ],
    'actions' => [
        'attachment' => [
            'view' => 'View details',
            'open' => 'Open',
            'edit' => 'Edit',
            'delete' => 'Delete',
            'upload' => 'Upload',
            'move' => 'Move',
            'replace' => 'Replace',
            'undo' => 'Undo',
        ],
        'directory' => [
            'rename' => 'Rename directory',
            'delete' => 'Remove directory',
            'create' => 'Create directory',
            'create_sub' => 'New subdirectory',
        ],
    ],
    'header_actions' => [
        'options' => 'Options',
        'sort' => [
            'updated_at_ascending' => 'Upload date ascending',
            'updated_at_descending' => 'Upload date descending',
            'created_at_ascending' => 'Upload date ascending',
            'created_at_descending' => 'Upload date descending',
            'name_ascending' => 'Name ascending',
            'name_descending' => 'Name descending',
        ],
    ],
    'sidebar' => [
        'files_selected' => ':count files selected',
        'filters' => [
            'header' => 'Filters',
            'mime' => 'File type',
        ],
        'mime_type' => [
            'all' => 'All types',
            'image' => 'Image',
            'video' => 'Video',
            'audio' => 'Audio',
            'pdf' => 'PDF',
        ],
    ],
    'tree' => [
        'heading' => 'Directories',
        'search' => 'Search directories...',
        'collapse' => 'Collapse directories',
        'expand' => 'Show directories',
        'toggle' => 'Expand or collapse :directory',
        'options' => 'Actions for :directory',
        'no_results' => 'No directories found',
    ],
    'field' => [
        'pick' => 'Choose files',
        'drag_to_reorder' => 'Drag to reorder',
    ],
    'title' => 'File manager',
    'group' => 'Files',
    'search' => 'Search...',
    'search_files' => 'Search files...',
    'home' => 'Home',
    'close' => 'Close',
    'submit' => 'Submit',
];
