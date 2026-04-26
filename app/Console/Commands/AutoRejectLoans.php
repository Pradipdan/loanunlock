<?php

namespace App\Console\Commands;

use App\Models\LoanApplication;
use App\Models\LoanNote;
use Illuminate\Console\Command;

class AutoRejectLoans extends Command
{
    protected $signature = 'loans:auto-reject {--hours=2 : Auto-reject applications older than this many hours}';
    protected $description = 'Auto-reject loan applications that are still under review after the configured number of hours.';

    private array $rejectionRemarks = [
        'Insufficient credit score for the requested loan amount.',
        'Income-to-EMI ratio exceeds permissible limits.',
        'Recent loan defaults reported on credit bureau.',
        'Employment tenure below minimum eligibility criteria.',
        'PAN-Aadhaar mismatch detected during verification.',
        'Mobile number not linked to any active bank account.',
        'Multiple active personal loans in the last 6 months.',
        'High credit utilization on existing credit cards.',
        'Address verification could not be completed.',
        'Bank statement does not reflect declared monthly income.',
        'Salary credits irregular over the past 3 months.',
        'Existing EMI obligations exceed 60% of disposable income.',
        'Negative remarks reported by previous lenders.',
        'Customer profile flagged under high-risk category.',
        'Cibil score below the minimum threshold of 650.',
        'Duplicate application detected for the same applicant.',
        'Self-employed business vintage less than 2 years.',
        'Salary slip authenticity could not be verified.',
        'Company not listed in our approved employer list.',
        'Pin code falls under non-serviceable area.',
        'Recent inquiry spike on credit bureau report.',
        'Aadhaar e-KYC verification failed.',
        'Bureau report shows write-off in last 24 months.',
        'Settlement entries reported on credit history.',
        'Insufficient banking activity in primary account.',
        'Cheque bounces reported in the last 6 months.',
        'Overdue amounts on existing credit obligations.',
        'Income proof documents found inconsistent.',
        'Age criteria not met for the selected tenure.',
        'Permanent address could not be physically verified.',
        'Telephonic verification could not be completed.',
        'Reference verification yielded negative feedback.',
        'GST returns not filed for the declared business.',
        'ITR not filed for the last 2 financial years.',
        'EPF contribution history inconsistent with employment claim.',
        'Suspicious transactions flagged in submitted bank statement.',
        'Credit history shows DPD greater than 90 days.',
        'Co-applicant criteria not met for this profile.',
        'Loan purpose not aligned with our lending policy.',
        'Customer category restricted as per current credit policy.',
        'Insufficient relationship with declared employer.',
        'Profile does not meet our minimum risk threshold.',
        'Outstanding tax liabilities reported.',
        'Frequent job changes within the last 12 months.',
        'Income source could not be independently corroborated.',
        'Disbursal blocked due to ongoing legal proceedings.',
        'Customer marked as politically exposed person (PEP).',
        'Internal scorecard rejected the application.',
        'Application withdrawn due to inactivity.',
        'Risk team has declined the proposal post-review.',
    ];

    public function handle(): int
    {
        $hours = (int) $this->option('hours');
        $cutoff = now()->subHours($hours);

        $applications = LoanApplication::where('status', 'under_review')
            ->where('created_at', '<=', $cutoff)
            ->get();

        if ($applications->isEmpty()) {
            $this->info("No applications older than {$hours}h to auto-reject.");
            return self::SUCCESS;
        }

        $count = 0;
        foreach ($applications as $application) {
            $reason = $this->rejectionRemarks[array_rand($this->rejectionRemarks)];

            $application->update([
                'status'           => 'rejected',
                'rejection_reason' => $reason,
                'reviewed_at'      => now(),
            ]);

            LoanNote::create([
                'loan_application_id' => $application->id,
                'admin_id'            => null,
                'note'                => 'Auto-rejected after ' . $hours . 'h: ' . $reason,
                'type'                => 'rejection',
            ]);

            $count++;
            $this->line("Rejected #{$application->application_id}: {$reason}");
        }

        $this->info("Auto-rejected {$count} application(s).");
        return self::SUCCESS;
    }
}
