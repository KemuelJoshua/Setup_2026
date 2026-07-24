<?php

namespace App\Actions\Academics\EducationalLevel;

use App\Models\Academics\EducationalLevel;
use Illuminate\Support\Collection;

class ListEducationalLevelOptionsAction
{
    /**
     * @return Collection<int, array{id: int, name: string}>
     */
    public function execute(): Collection
    {
        return EducationalLevel::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (EducationalLevel $educationalLevel): array => [
                'id' => $educationalLevel->getKey(),
                'name' => $educationalLevel->name,
            ]);
    }
}
