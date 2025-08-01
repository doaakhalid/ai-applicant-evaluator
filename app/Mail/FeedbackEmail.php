<?php 

namespace App\Mail;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class FeedbackEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Application $application;

    /**
     * Create a new message instance.
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Your Application Feedback')
            ->markdown('emails.feedback')
            ->with([
                'name' => $this->application->applicant->name,
                'jobTitle' => $this->application->job->title,
                'score' => $this->application->score,
                'summary' => $this->application->summary,
            ]);
    }
}
