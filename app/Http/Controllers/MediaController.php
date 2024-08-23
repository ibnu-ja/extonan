<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Plank\Mediable\Facades\ImageManipulator;
use Plank\Mediable\Facades\MediaUploader;
use Plank\Mediable\Media;

class MediaController extends Controller
{

    public function index(Request $request): \Illuminate\Pagination\LengthAwarePaginator
    {
        $validated = $request->validate([
            'dir' => 'sometimes|nullable|date_format:Y-m',
        ]);

        // $dir = $validated['dir'] ?? date('Y-m', strtotime("-1 month"));
        // $dir = $validated['dir'] ?? date('Y-m');
        $dir = $validated['dir'] ?? null;

        $media = Media::when($dir, function (Builder $query, string $dir) {
            return $query->where('directory', $dir);
        })
            ->whereIn('aggregate_type', [Media::TYPE_IMAGE, Media::TYPE_IMAGE_VECTOR])
            ->whereNull('variant_name')
            ->latest('created_at')
            ->paginate();

        return $media;
    }

    public function store(Request $request): void
    {
        $validated = $request->validate([
            'media' => 'array|required_without:url|nullable',
            'media.*' => 'file|image|max:10000|',
            'url' => 'array|required_without:media|nullable',
            'url.*' => 'string',
        ]);

        $files = $validated['media'] ?? $validated['url'];

        foreach ($files as $file) {
            $media = MediaUploader::fromSource($file)
                ->toDirectory(date('Y-m'))
                // ->toDirectory(date('Y-m', strtotime("-1 month")))
                ->upload();

            $variant = ['medium', 'large'];

            foreach ($variant as $v) {
                ImageManipulator::createImageVariant($media, $v);
            }
            // using jobs, php artisan queue:work
            // CreateImageVariants::dispatch($media, ['thumb', 'medium', 'large']);
        }
    }

    public function getMonths(): \Illuminate\Support\Collection
    {
        $months = Media::select('directory')->distinct()->get()->pluck('directory');
        return $months;
    }

    public function destroy(Media $media): bool
    {
        return $media->delete();
    }
}
