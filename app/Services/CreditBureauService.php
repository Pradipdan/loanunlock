<?php

namespace App\Services;

use App\Models\LoanApplication;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class CreditBureauService
{
    /**
     * Simulation mode - used if no real API keys are provided.
     */
    protected $useSimulation = true;

    /**
     * Fetch credit score from a bureau.
     * Currently implemented as a "Simulation" that mimics a real API call.
     */
    public function fetchScore(LoanApplication $application)
    {
        $user = $application->user;

        // In a real implementation, you would call Experian/CIBIL API here.
        // Example:
        // $response = Http::withHeaders(['Auth' => '...'])->post('https://api.experian.in/v1/score', [...]);
        
        Log::info("Fetching credit score for Application: {$application->application_id}, PAN: {$user->pan_number}");

        if ($this->useSimulation) {
            return $this->simulateBureauResponse($application);
        }

        // Real API logic would go here
        return null;
    }

    /**
     * This simulates a real bureau API response structure.
     */
    protected function simulateBureauResponse(LoanApplication $application)
    {
        // Add a slight delay to make it feel real
        usleep(800000); // 0.8 seconds

        $pan = $application->user->pan_number;
        
        // Logic: Certain PAN numbers could return specific scores for testing
        // Default: Random score in a "real" looking range
        $score = rand(715, 810);
        
        // If PAN ends in 'X', simulate NTC (No Track)
        if (str_ends_with($pan, 'X')) {
            $score = 0;
        }

        $responseBody = [
            'status' => 'success',
            'bureau' => 'Experian',
            'report_id' => 'EXP-' . strtoupper(uniqid()),
            'timestamp' => now()->toISOString(),
            'data' => [
                'score' => $score,
                'summary' => $score > 750 ? 'Excellent' : ($score > 700 ? 'Good' : 'Average'),
                'accounts' => rand(2, 5),
                'enquiries_last_30_days' => rand(0, 2),
            ]
        ];

        // Update the application
        $application->update([
            'credit_score' => $score,
            'bureau_name' => 'Experian',
            'bureau_raw_response' => json_encode($responseBody),
            'bureau_check_id' => $responseBody['report_id'],
            'score_fetched_at' => now(),
        ]);

        return $responseBody;
    }
}
