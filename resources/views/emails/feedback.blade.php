@component('mail::message')
# Application Feedback

Hi {{ $name }},

Thank you for applying to the **{{ $jobTitle }}** position.

### Your Evaluation:
- **Score:** {{ $score }}/100  
- **Summary:**  
{{ $summary }}

We appreciate your time and effort. We encourage you to apply for future openings.

Thanks,  
{{ config('app.name') }} Team
@endcomponent
