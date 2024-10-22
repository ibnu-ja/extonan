<?php

namespace App\Http\Requests;

enum PostType: string
{
    case TV = 'tv';
    case BD = 'bd';
    case MOVIE = 'movie';
}
