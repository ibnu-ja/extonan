<?php

// TODO exception handling

namespace App\Services;

use App\Models\Album;


class AlbumService
{
    public function createArtistRolesForAlbum(Album $album, array $roles): Album
    {
        foreach ($roles as $role => $artists_ids) {
            foreach ($artists_ids as $artists_id) {
                $album->artists()->attach($artists_id, ['role' => $role]);
            }
        }
        return $album;
    }

    public function createOrgRolesForAlbum(Album $album, array $roles): Album
    {
        foreach ($roles as $role => $artists_ids) {
            foreach ($artists_ids as $artists_id) {
                $album->organizations()->attach($artists_id, ['role' => $role]);
            }
        }
        return $album;
    }

    public function storeImagesToGallery(Album $album, array $images, array $label): Album
    {
        foreach ($images as $key => $image) {
            $album->addMedia($image)
                ->withCustomProperties(['label' => $label[$key]])
                ->toMediaCollection('gallery');
        }
        return $album;
    }
}
