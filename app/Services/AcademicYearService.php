<?php

namespace App\Services;

use App\Models\AcademicYear;
use Carbon\Carbon;

class AcademicYearService
{
    public function getCurrent(): ?AcademicYear
    {
        return AcademicYear::active()->first();
    }

    public function setCurrent(AcademicYear $year): void
    {
        // Disattiva tutti gli altri
        AcademicYear::where('id', '!=', $year->id)->update(['is_active' => false]);
        // Attiva quello selezionato
        $year->update(['is_active' => true]);
    }

    public function createFromDates(Carbon $startDate, Carbon $endDate): AcademicYear
    {
        $startYear = $startDate->year;
        $endYear = $endDate->year;
        
        $name = "{$startYear}-{$endYear}";
        $slug = $startYear . '-' . substr($endYear, -2);

        return AcademicYear::create([
            'name' => $name,
            'slug' => $slug,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'is_active' => false,
        ]);
    }

    public function createForYear(int $year): AcademicYear
    {
        $startDate = Carbon::create($year, 9, 1);
        $endDate = Carbon::create($year + 1, 8, 31);
        
        return $this->createFromDates($startDate, $endDate);
    }
}

