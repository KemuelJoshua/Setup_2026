<?php

namespace App\Enums;

enum AcademicTermStructureType: string
{
    case Semester = 'semester';
    case Quarterly = 'quarterly';
    case Trisem = 'trisem';
    case Term = 'term';

    public function allowsChildPeriods(): bool
    {
        return $this !== self::Quarterly;
    }

    public function maximumChildPeriods(): int
    {
        return $this->allowsChildPeriods() ? 4 : 0;
    }
}
