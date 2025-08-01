<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use app\Models\Applicant;
use app\Models\Application;
use app\Models\Job;
use App\Jobs\ProcessApplication;


class ApplicationController extends Controller
{
    //

    public function create()
    {
        $applicants = Applicant::all();
        $jobs = Job::all();

        return view('applications.create', compact('applicants', 'jobs'));
    }

    /**
     * Handle resume submission
     */
    public function store(Request $request)
    {
        $request->validate([
            'applicant_id' => 'required|exists:applicants,id',
            'job_id' => 'required|exists:jobs,id',
            'resume' => 'required|file|mimes:pdf|max:2048',
        ]);

        $path = $request->file('resume')->store('resumes');

        $application = Application::create([
            'applicant_id' => $request->applicant_id,
            'job_id' => $request->job_id,
            'resume_path' => $path,
        ]);

        // Dispatch async job
        ProcessApplication::dispatch($application->id);

        return redirect()->back()->with('success', 'Application submitted successfully. You will receive feedback by email.');
    }

    
}
