<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Grade;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GradePublishedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Grade $grade) {}

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $course = $this->grade->enrollment->courseSection->course;
        return (new MailMessage)
            ->subject("Grade published for {$course->code}")
            ->greeting("Hello {$notifiable->first_name},")
            ->line("Your grade for **{$course->title}** has been published.")
            ->line("Letter Grade: **{$this->grade->letter_grade->value}** (Score: {$this->grade->total_score})")
            ->action('View your dashboard', config('app.frontend_url', config('app.url')));
    }

    public function toArray($notifiable): array
    {
        $course = $this->grade->enrollment->courseSection->course;
        return [
            'type' => 'grade_published',
            'course_code' => $course->code,
            'course_title' => $course->title,
            'letter_grade' => $this->grade->letter_grade?->value,
            'score' => $this->grade->total_score,
        ];
    }
}
