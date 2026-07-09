<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Semester;
use App\Models\Student;
use App\Services\GpaCalculator;
use Illuminate\Console\Command;

class RecomputeGpaCommand extends Command
{
    protected $signature = 'srms:recompute-gpa {--semester=}';
    protected $description = 'Recompute per-semester and cumulative GPA snapshots for all students';

    public function handle(GpaCalculator $gpa): int
    {
        $semQuery = Semester::query();
        if ($this->option('semester')) {
            $semQuery->where('id', $this->option('semester'));
        }
        $semesters = $semQuery->get();
        $students = Student::all();

        $bar = $this->output->createProgressBar($students->count() * $semesters->count());
        $bar->start();

        foreach ($students as $s) {
            foreach ($semesters as $sem) {
                $gpa->snapshot($s, $sem);
                $bar->advance();
            }
        }
        $bar->finish();
        $this->newLine();
        $this->info('GPA recomputed for ' . $students->count() . ' students × ' . $semesters->count() . ' semesters.');
        return self::SUCCESS;
    }
}
