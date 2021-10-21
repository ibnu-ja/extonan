<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Album extends Model
//  implements HasMedia
{
    use HasSlug;
    // use InteractsWithMedia;

    protected $fillable = [
        'name',
        'name_real',
        'name_trans',
        'catalog',
        'barcode',
        'release_date',
        'discs',
        'media_format',
        'desc',
        'event_id'
    ];
    protected $casts = ['discs' => 'array'];

    // protected $appends = ['link', 'cover'];

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    // protected $hidden = ['slug', 'media'];

    // public function getLinkAttribute()
    // {
    //     return url('/api/album/' . $this->slug);
    // }

    // public function artists()
    // {
    //     return $this->belongsToMany(Artist::class);
    // }

    // //waduhhhh gabisa custom relation nya, harus di define dulu
    // public function composers()
    // {
    //     return $this->artists()->wherePivot('role', '=', 'composer');
    // }
    // public function performers()
    // {
    //     return $this->artists()->wherePivot('role', '=', 'performer');
    // }
    // public function lyricists()
    // {
    //     return $this->artists()->wherePivot('role', '=', 'lyricist');
    // }
    // public function arrangers()
    // {
    //     return $this->artists()->wherePivot('role', '=', 'arranger');
    // }

    // public function orgs()
    // {
    //     return $this->belongsToMany(Organization::class);
    // }
    // public function label()
    // {
    //     return $this->orgs()->wherePivot('role', '=', 'label');
    // }
    // public function publisher()
    // {
    //     return $this->orgs()->wherePivot('role', '=', 'publisher');
    // }
    // public function distributor()
    // {
    //     return $this->orgs()->wherePivot('role', '=', 'distributor');
    // }

    // public function products()
    // {
    //     return $this->belongsToMany(Product::class);
    // }
    // public function event()
    // {
    //     return $this->belongsTo(Event::class);
    // }

    // public function getCoverAttribute()
    // {
    //     $data = $this->getMedia('cover');

    //     if (count($data) > 0) {
    //         $tes = [];
    //         foreach ($data[0]->generated_conversions as $key => $co) {
    //             if ($co = true)
    //                 $tes[$key] =  $data[0]->getUrl($key);
    //         }
    //         $tes['original'] = $data[0]->getUrl();
    //         $data[0]->urls = $tes;
    //         return $data;
    //     } else return null;
    // }

    // public function registerMediaConversions(Media $media = null): void
    // {
    //     $this->addMediaConversion('thumb')
    //         ->height(150);

    //     $this->addMediaConversion('medium')
    //         ->height(300);
    // }
}
