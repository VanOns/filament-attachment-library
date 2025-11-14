<?php

return [
    'browser' => [
        'empty' => [
            'title' => 'Geen bestanden of mappen gevonden',
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
    'actions' => [
        'attachment' => [
            'view' => 'Bekijk details',
            'open' => 'Open bestand',
            'edit' => 'Bewerk bestand',
            'delete' => 'Verwijder bestand',
            'upload' => 'Upload bestand',
        ],
        'directory' => [
            'rename' => 'Hernoem map',
            'delete' => 'Verwijder map',
            'create' => 'Maak map',
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
        'files_selected' => ':count bestanded geselecteerd',
        'filters' => [
            'header' => 'Filters',
            'mime' => 'Bestandstype',
        ],
        'mime_type' => [
            'all' => 'Alle',
            'image' => 'Afbeelding',
            'video' => 'Video',
            'audio' => 'Audio',
            'pdf' => 'PDF',
        ],
    ],
    'field' => [
        'pick' => 'Kies bestand(en)',
    ],
    'title' => 'Bestandsbeheer',
    'group' => 'Bestanden',
    'search' => 'Zoeken...',
    'home' => 'Hoofdmap',
    'close' => 'Sluiten',
    'submit' => 'Opslaan',
];
