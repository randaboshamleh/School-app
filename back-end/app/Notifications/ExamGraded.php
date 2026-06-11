<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ExamGraded extends Notification implements ShouldQueue
{
    use Queueable;

    protected int $examId;

    protected string $examTitle;

    protected int $marksObtained;

    protected int $maxMarks;

    protected bool $passed;

    public function __construct(int $examId, string $examTitle, int $marksObtained, int $maxMarks, bool $passed)
    {
        $this->examId = $examId;
        $this->examTitle = $examTitle;
        $this->marksObtained = $marksObtained;
        $this->maxMarks = $maxMarks;
        $this->passed = $passed;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'exam_graded',
            'exam_id' => $this->examId,
            'exam_title' => $this->examTitle,
            'marks_obtained' => $this->marksObtained,
            'max_marks' => $this->maxMarks,
            'passed' => $this->passed,
            'student_id' => $notifiable->id,
        ];
    }
}
