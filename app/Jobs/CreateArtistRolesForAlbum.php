<?php

namespace App\Jobs;

use App\Models\Album;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateArtistRolesForAlbum implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     * @param \App\Models\Album $album 
     * @param array $roles
     *
     * @return void
     */
    public function __construct(public Album $album, public array $roles)
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->roles as $role => $artists_ids) {
            foreach ($artists_ids as $artists_id) {
                $this->album->artists()->attach($artists_id, ['role' => $role]);
            }
        }
    }
}
