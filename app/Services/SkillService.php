<?php

namespace App\Services;

use App\Models\Skill;
use App\Traits\PaginatedTrait;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;


class skillService
{
    use PaginatedTrait;

    public function indexSkills(array $filter): LengthAwarePaginator
    {
        $skills = Skill::query();

        $paginated = $this->paginateBuilder($skills, $filter, 10);
        return $paginated;
    }
}