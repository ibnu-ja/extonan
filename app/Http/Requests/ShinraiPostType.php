<?php

namespace App\Http\Requests;

enum ShinraiPostType: string
{
    case MV = 'mv';
    case ALBUM = 'album';
    case SINGLE = 'single';
}
