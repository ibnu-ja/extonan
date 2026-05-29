<?php

namespace App\Enums;

enum Permission: string
{
    case POST_CREATE = 'post.create';
    case POST_READ_ANY = 'post.read.any';
    case POST_READ_SELF = 'post.read.self';
    case POST_UPDATE_ANY = 'post.update.any';
    case POST_UPDATE_SELF = 'post.update.self';
    case POST_DELETE_ANY = 'post.delete.any';
    case POST_DELETE_SELF = 'post.delete.self';
    case POST_PUBLISH_SELF = 'post.publish.self';
    case POST_PUBLISH_ANY = 'post.publish.any';
    case USER_INVITE = 'user.invite';
    case USER_READ_ANY = 'user.read.any';
    case USER_READ_SELF = 'user.read.self';
    case USER_DELETE_ANY = 'user.delete.any';
    case USER_DELETE_SELF = 'user.delete.self';
    case USER_EDIT_SELF = 'user.edit.self';
    case USER_EDIT_ANY = 'user.edit.any';
}
