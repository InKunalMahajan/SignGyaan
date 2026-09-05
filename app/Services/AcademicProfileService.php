<?php

namespace App\Services;

use Illuminate\Support\Carbon;

class AcademicProfileService
{
    /**
     * @return array<string, string>
     */
    public function boards(): array
    {
        return [
            'maharashtra' => 'Maharashtra State Board',
            'cbse' => 'CBSE',
            'nios' => 'NIOS',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function standards(): array
    {
        $standards = [];

        for ($standard = 1; $standard <= 12; $standard++) {
            $standards[(string) $standard] = "Standard {$standard}";
        }

        return $standards;
    }

    /**
     * @return array<string, string>
     */
    public function academicYears(): array
    {
        $year = Carbon::now()->year;
        $options = [];

        for ($start = $year - 1; $start <= $year + 2; $start++) {
            $value = sprintf('%d-%02d', $start, ($start + 1) % 100);
            $options[$value] = $value;
        }

        return $options;
    }

    public function boardLabel(?string $board): string
    {
        if (! $board) {
            return 'Not selected';
        }

        return $this->boards()[$board] ?? ucfirst(str_replace('_', ' ', $board));
    }

    public function standardLabel(?string $standard): string
    {
        if (! $standard) {
            return 'Not selected';
        }

        return $this->standards()[$standard] ?? $standard;
    }
}
