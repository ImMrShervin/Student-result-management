<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\GradePublished;
use App\Notifications\GradePublishedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyStudentOfGrade implements ShouldQueue
{
    public function handle(GradePublished $event): void
    {
        $grade = $event->grade->load('enrollment.student.user', 'enrollment.courseSection.course');
        $student = $grade->enrollment->student;
        $student->user->notify(new GradePublishedNotification($grade));
    }
}
