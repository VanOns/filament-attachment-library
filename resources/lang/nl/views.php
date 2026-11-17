<?php

return [
    'browser' => [
        'drop' => [
            'prompt' => 'Sleep bestanden hierheen om te uploaden',
            'uploading' => 'Uploaden…',
        ],
        'empty' => [
            'title' => 'Geen bestanden of mappen gevonden',
            'title_files' => 'Geen bestanden in deze map',
            'description' => 'Upload een nieuw bestand of navigeer naar een ander pad.',
            'button' => 'Terug naar hoofdmap',
        ],
        'search_results' => 'Zoekresultaten voor:',
        'file_count' => ':count bestand|:count bestanden',
    ],
    'info' => [
        'empty' => [
            'title' => 'Selecteer een map of bestand',
            'description' => 'Selecteer een bestand om de informatie te bekijken.',
        ],
        'details' => [
            'sections' => [
                'meta' => [
                    'id' => 'ID',
                    'header' => 'Metavelden',
                    'alt' => 'Alt-tekst',
                    'caption' => 'Onderschrift',
                    'title' => 'Titel',
                    'description' => 'Beschrijving',
                ],
                'image' => [
                    'header' => 'Afbeelding velden',
                    'dimensions' => 'Afmetingen',
                    'bits' => 'Bits',
                    'channels' => 'Kanalen',
                ],
                'video' => [
                    'header' => 'Videometadata',
                    'dimensions' => 'Afmetingen',
                    'duration' => 'Duur',
                    'captions' => 'Ondertiteling',
                ],
                'more' => 'Meer details',
                'date' => [
                    'header' => 'Data',
                    'created_by' => 'Geüpload door',
                    'created_at' => 'Geüpload op',
                    'updated_by' => 'Gewijzigd door',
                    'updated_at' => 'Gewijzigd op',
                ],
            ],
            'size' => 'Bestandsgrootte',
            'mime_type' => 'MIME-type',
            'path' => 'Pad',
            'url' => 'Link',
            'modal_title' => 'Bestandsinformatie',
        ],
    ],
    'edit' => [
        'file' => 'Bestand',
        'uploaded' => 'Geüpload op :date door :user',
        'edited' => 'Bewerkt op :date door :user',
        'uploaded_at' => 'Geüpload op :date',
        'edited_at' => 'Bewerkt op :date',
        'root' => '/ (hoofdmap)',
        'preview' => 'Voorbeeld',
        'preview_lines' => 'Eerste :count regels',
    ],
    'actions' => [
        'attachment' => [
            'view' => 'Bekijk details',
            'open' => 'Openen',
            'edit' => 'Bewerken',
            'delete' => 'Verwijderen',
            'upload' => 'Uploaden',
            'move' => 'Verplaatsen',
            'replace' => 'Vervangen',
            'undo' => 'Ongedaan maken',
        ],
        'directory' => [
            'rename' => 'Hernoem map',
            'delete' => 'Verwijder map',
            'create' => 'Maak map',
            'create_sub' => 'Nieuwe submap',
        ],
    ],
    'header_actions' => [
        'options' => 'Opties',
        'sort' => [
            'updated_at_ascending' => 'Datum bijgewerkt oplopend',
            'updated_at_descending' => 'Datum bijgewerkt aflopend',
            'created_at_ascending' => 'Uploaddatum oplopend',
            'created_at_descending' => 'Uploaddatum aflopend',
            'name_ascending' => 'Naam oplopend',
            'name_descending' => 'Naam aflopend',
        ],
    ],
    'sidebar' => [
        'files_selected' => ':count bestanden geselecteerd',
        'filters' => [
            'header' => 'Filters',
            'mime' => 'Bestandstype',
        ],
        'mime_type' => [
            'all' => 'Alle types',
            'image' => 'Afbeelding',
            'video' => 'Video',
            'audio' => 'Audio',
            'pdf' => 'PDF',
        ],
    ],
    'tree' => [
        'heading' => 'Mappen',
        'search' => 'Zoek map...',
        'collapse' => 'Mappen inklappen',
        'expand' => 'Mappen tonen',
        'toggle' => ':directory in- of uitklappen',
        'options' => 'Acties voor :directory',
        'no_results' => 'Geen mappen gevonden',
    ],
    'field' => [
        'pick' => 'Kies bestand(en)',
        'drag_to_reorder' => 'Sleep om te herordenen',
    ],
    'title' => 'Bestandsbeheer',
    'group' => 'Bestanden',
    'search' => 'Zoeken...',
    'search_files' => 'Zoek bestanden...',
    'home' => 'Hoofdmap',
    'close' => 'Sluiten',
    'submit' => 'Opslaan',
];
