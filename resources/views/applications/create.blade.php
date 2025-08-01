@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Submit Your Resume</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('applications.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="applicant_id">Applicant</label>
            <select name="applicant_id" class="form-control" required>
                @foreach($applicants as $applicant)
                    <option value="{{ $applicant->id }}">{{ $applicant->name }} ({{ $applicant->email }})</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="job_id">Select Job</label>
            <select name="job_id" class="form-control" required>
                @foreach($jobs as $job)
                    <option value="{{ $job->id }}">{{ $job->title }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="resume">Upload Resume (PDF)</label>
            <input type="file" name="resume" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Submit Application</button>
    </form>
</div>
@endsection
