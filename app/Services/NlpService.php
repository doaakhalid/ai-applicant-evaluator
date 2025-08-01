<?php
namespace App\Services;

use OpenAI;

class NlpService
{
   
    public function extractStructuredData(string $text): array
    {
        $response = OpenAI::chat()->create([
            'model' => 'gpt-4',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are an AI trained to extract structured resume data in JSON format. Return an object like: 
{
  "skills": [],
  "education": [],
  "experience": [],
  "summary": ""
}'
                ],
                [
                    'role' => 'user',
                    'content' => $text
                ]
            ]
        ]);

        return json_decode($response['choices'][0]['message']['content'], true);
    }

    /**
     * Compare resume data and job description using OpenAI and return a score (0-100)
     */
    public function computeMatchScore(array $resumeData, string $jobDescription): int
    {
        $input = "Resume Data:\n" . json_encode($resumeData, JSON_PRETTY_PRINT) . "\n\nJob Description:\n" . $jobDescription;

        $response = OpenAI::chat()->create([
            'model' => 'gpt-4',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are an AI that scores how well a resume matches a job description. Give a score from 0 to 100. Only output the number.'
                ],
                [
                    'role' => 'user',
                    'content' => $input
                ]
            ]
        ]);

        preg_match('/\d+/', $response['choices'][0]['message']['content'], $matches);
        return (int) ($matches[0] ?? 0);
    }
}
