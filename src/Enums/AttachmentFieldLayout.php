<?php

namespace VanOns\FilamentAttachmentLibrary\Enums;

/**
 * How an AttachmentField displays its selected attachments.
 */
enum AttachmentFieldLayout: string
{
    case GRID = 'grid';
    case LIST = 'list';
    case INPUT = 'input';
}
