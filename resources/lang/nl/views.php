<?php

return [
    'browser' => [
        'empty' => [
            'title' => 'Geen bestanden of mappen gevonden',
            'description' => 'Upload een nieuw bestand of navigeer naar een ander pad.',
            'button' => 'Terug naar hoofdmap',
        ],
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
                    'description' => 'Beschrijving'
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
        ],
    ],
    'actions' => [
        'attachment' => [
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
    'header-actions' => [
        'created_at_ascending' => 'Uploaddatum oplopend',
        'created_at_descending' => 'Uploaddatum aflopend',
        'name_ascending' => 'Naam oplopend',
        'name_descending' => 'Naam aflopend',
        'options' => 'Opties',
    ],
    'title' => 'Bestandsbeheer',
    'group' => 'Bestanden',
    'search' => 'Zoeken...',
    'home' => 'Hoofdmap',
    'close' => 'Sluiten',
];
