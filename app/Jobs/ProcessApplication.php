<?php 
namespace App\Jobs;

use App\Models\Application;
use App\Models\EvaluationLog;
use App\Services\ResumeParserService;
use App\Services\NlpService;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Mail\FeedbackEmail;
use Exception;

class ProcessApplication implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $applicationId;

    /**
     * Create a new job instance.
     */
    public function __construct($applicationId)
    {
        $this->applicationId = $applicationId;
    }

    /**
     * Execute the job.
     */
    public function handle(ResumeParserService $parser, NlpService $nlp): void
    {
        $application = Application::with('job', 'applicant')->findOrFail($this->applicationId);

        try {
            // Step 1: Parse resume
            $this->logStep($application->id, 'pdf_parse', 'processing', 'Starting PDF parsing...');
            $text = $parser->parse(storage_path('app/' . $application->resume_path));
            $this->logStep($application->id, 'pdf_parse', 'success', 'Parsed resume content.');

            // Step 2: Extract structured data
            $this->logStep($application->id, 'nlp_extraction', 'processing', 'Sending to NLP for structured extraction...');
            $structured = $nlp->extractStructuredData($text);
            $this->logStep($application->id, 'nlp_extraction', 'success', 'NLP extracted data.');

            // Step 3: Compute match score
            $this->logStep($application->id, 'score', 'processing', 'Calculating score...');
            $score = $nlp->computeMatchScore($structured, $application->job->description);
            $this->logStep($application->id, 'score', 'success', 'Score computed: ' . $score);

            // Step 4: Update DB
            $application->update([
                'score' => $score,
                'summary' => $structured['summary'] ?? 'No summary returned.',
            ]);

            // Step 5: Send feedback email
            Mail::to($application->applicant->email)->send(new FeedbackEmail($application));
            $this->logStep($application->id, 'email_sent', 'success', 'Feedback email sent.');
        } catch (Exception $e) {
            $this->logStep($this->applicationId, 'failed', 'error', $e->getMessage());
            report($e);
        }
    }

 
    protected function logStep($applicationId, $step, $status, $message): void
    {
        EvaluationLog::create([
            'application_id' => $applicationId,
            'step' => $step,
            'status' => $status,
            'message' => $message,
        ]);
    }
}
