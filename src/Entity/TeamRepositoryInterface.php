<?php

namespace App\Entity;

use App\Enums\DivisionEnum;

interface TeamRepositoryInterface
{
    /**
     * @param DivisionEnum $division
     * @return array<Team>
     */
    public function findByDivision(DivisionEnum $division): iterable;
}
