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

    public function artists()
    {
        return $this->belongsToMany(Artist::class)->withPivot('role');
    }

    //waduhhhh gabisa custom relation nya, harus di define dulu
    public function composers()
    {
        return $this->artists()->wherePivot('role', '=', 'composers');
    }
    public function performers()
    {
        return $this->artists()->wherePivot('role', '=', 'performers');
    }
    public function lyricists()
    {
        return $this->artists()->wherePivot('role', '=', 'lyricists');
    }
    public function arrangers()
    {
        return $this->artists()->wherePivot('role', '=', 'arrangers');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function getRolesAttribute()
    {
        return [
            'composers' => $this->composers()->allRelatedIds()->toArray(),
            'performers' => $this->performers()->allRelatedIds()->toArray(),
            'lyricists' => $this->lyricists()->allRelatedIds()->toArray(),
            'arrangers' => $this->arrangers()->allRelatedIds()->toArray(),
        ];
    }

    public function organizations()
    {
        return $this->belongsToMany(Organization::class)->withPivot('role');
    }
    public function label()
    {
        return $this->organizations()->wherePivot('role', '=', 'label');
    }
    public function publisher()
    {
        return $this->organizations()->wherePivot('role', '=', 'publisher');
    }
    public function distributor()
    {
        return $this->organizations()->wherePivot('role', '=', 'distributor');
    }
    public function manufacturer()
    {
        return $this->organizations()->wherePivot('role', '=', 'manufacturer');
    }

    public function getOrgsAttribute()
    {
        return [
            'label' => $this->label()->allRelatedIds()->toArray(),
            'publisher' => $this->publisher()->allRelatedIds()->toArray(),
            'distributor' => $this->distributor()->allRelatedIds()->toArray(),
            'manufacturer' => $this->manufacturer()->allRelatedIds()->toArray(),
        ];
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

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
