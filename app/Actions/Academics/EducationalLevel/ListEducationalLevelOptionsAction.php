<?php

namespace App\Actions\Academics\EducationalLevel;

use App\Models\Academics\EducationalLevel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ListEducationalLevelOptionsAction
{
    private const CACHE_KEY = 'academics.educational-levels.options.v2';

    /**
     * @return Collection<int, array{id: int, name: string}>
     */
    public function execute(): Collection
    {
        /** @var array<int, array{id: int, name: string}> $options */
        $options = Cache::remember(
            self::CACHE_KEY,
            now()->addHours(24),
            fn (): array => EducationalLevel::query()
                ->orderBy('name')
                ->get(['id', 'name'])
                ->map(
                    fn (EducationalLevel $educationalLevel): array => [
                        'id' => (int) $educationalLevel->getKey(),
                        'name' => $educationalLevel->name,
                    ],
                )
                ->all(),
        );

        return collect($options);
    }

    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}