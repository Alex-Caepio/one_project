<?php

namespace App\Services\UrlGeneration;

use App\Models\Service;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Generates unique slugs by title and slug for services.
 */
class ServiceSlugGenerator
{
    /**
     * Returns the given slug if it is unique or modifies it. If the slug doesn't exist
     * then creates a new unique slug by the given title.
     */
    public function getOrCreateSlug(string $title, ?string $slug = null, ?int $excludeServiceId = null): string
    {
        return $slug === null || mb_strlen(trim($slug)) === 0
            ? $this->createSlug($title)
            : $this->getOrModifySlug($slug, $excludeServiceId)
        ;
    }

    /**
     * Creates an unique slug by the given title.
     */
    public function createSlug(string $title): string
    {
        $slug = Str::slug($title);

        return $this->getOrModifySlug($slug);
    }

    /**
     * Returns an unmodified slug or modifies the given slug if it is necessary.
     *
     * @param int|null $excludeServiceId A service ID to exclude service's slug from the search.
     */
    public function getOrModifySlug(string $slug, ?int $excludeServiceId = null): string
    {
        $query = $this->createQueryBySlug($slug, true, $excludeServiceId);

        return $query->exists() ? $this->modifySlug($slug, $excludeServiceId) : $slug;
    }

    /**
     * Modifies the given slug by adding the next number.
     */
    public function modifySlug(string $slug, ?int $excludeServiceId = null): string
    {
        $slugs = $this->getSimilarSlugs($slug, $excludeServiceId);
        $numbers = $this->getNumbersOfSlugs($slug, $slugs);
        $nextNumber = $numbers->count() ? $numbers->max() + 1 : 1;

        return sprintf('%s-%d', $slug, $nextNumber);
    }

    private function getSimilarSlugs(string $slug, ?int $excludeServiceId): Collection
    {
        $query = $this->createQueryBySlug($slug, false, $excludeServiceId);

        return $query
            ->select('slug')
            ->get()
            ->pluck('slug')
            ->filter(static function (string $value) use ($slug): bool {
                return preg_match("/^$slug-([\d]+)$/", $value);
            })
        ;
    }

    private function getNumbersOfSlugs(string $slug, Collection $slugs): Collection
    {
        return $slugs
            ->map(static function (string $value) use ($slug): int {
                $matches = [];
                preg_match("/^$slug-([\d]+)$/", $value, $matches);

                return (int) $matches[1];
            })
        ;
    }

    protected function createQuery(): Builder
    {
        return Service::query();
    }

    protected function createQueryBySlug(string $slug, bool $strict = true, ?int $excludeServiceId = null): Builder
    {
        $query = $this->createQuery();

        if ($strict) {
            $query->where('slug', $slug);
        } else {
            $query->where('slug', 'like', $slug.'%');
        }

        if ($excludeServiceId) {
            $query->where('id', '!=', $excludeServiceId);
        }

        return $query;
    }
}
