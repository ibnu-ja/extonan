<?php

namespace App\Http\Controllers;

use App\Data\MediaData;
use App\Data\PaginationData;
use App\Services\MediaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Plank\Mediable\Facades\ImageManipulator;
use Plank\Mediable\Facades\MediaUploader;
use Plank\Mediable\Media;
use Spatie\LaravelData\DataCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class MediaController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth'),
        ];
    }

    public function __construct(private readonly MediaService $media) {}

    public function index(): array
    {
        $paginator = QueryBuilder::for(Media::class)
            ->allowedFilters(
                AllowedFilter::exact('month', 'directory'),
            )
            ->allowedSorts('created_at', 'filename')
            ->whereIn('aggregate_type', [Media::TYPE_IMAGE, Media::TYPE_IMAGE_VECTOR])
            ->whereNull('variant_name')
            ->defaultSort('-created_at')
            ->paginate();

        return [
            'items' => new DataCollection(MediaData::class, array_map(fn (Media $media) => MediaData::fromModel($media), $paginator->items())),
            'pagination' => PaginationData::fromPaginator($paginator),
        ];
    }

    public function store(Request $request): DataCollection
    {
        $validated = $request->validate([
            'media' => 'array|required_without:url|nullable',
            'media.*' => 'file|image|max:10000',
            'url' => 'array|required_without:media|nullable',
            'url.*' => 'string',
        ]);

        $uploaded = [];

        foreach ($validated['media'] ?? $validated['url'] ?? [] as $file) {
            $media = MediaUploader::fromSource($file)
                ->toDirectory(date('Y-m'))
                ->upload();

            foreach (['medium', 'large'] as $variant) {
                ImageManipulator::createImageVariant($media, $variant);
            }

            $uploaded[] = $media;
        }

        return new DataCollection(MediaData::class, array_map(fn (Media $media) => MediaData::fromModel($media), $uploaded));
    }

    public function getMonths(): array
    {
        return $this->media->getMonths();
    }

    public function getMonthsWithCounts(): array
    {
        return $this->media->getMonthsWithCounts();
    }

    public function destroy(Media $media): JsonResponse
    {
        $media->delete();

        return response()->json(['message' => 'Media deleted successfully.']);
    }
}
