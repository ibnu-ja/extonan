<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Plank\Mediable\Media;

class MediaService
{
    /** @return array<int, string> */
    public function getMonths(): array
    {
        return Media::select('directory')
            ->distinct()
            ->orderBy('directory', 'desc')
            ->get()
            ->pluck('directory')
            ->toArray();
    }

    /**
     * Get months with media counts for sidebar display.
     *
     * @return array<int, array{month: string, count: int}>
     */
    public function getMonthsWithCounts(): array
    {
        return Media::select('directory as month', DB::raw('COUNT(*) as count'))
            ->whereIn('aggregate_type', [Media::TYPE_IMAGE, Media::TYPE_IMAGE_VECTOR])
            ->whereNull('variant_name')
            ->groupBy('directory')
            ->orderBy('directory', 'desc')
            ->get()
            ->toArray();
    }
}
