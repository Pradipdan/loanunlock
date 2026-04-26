<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Database\Seeder;

class LoanApplicationSeeder extends Seeder
{
    public function run(): void
    {
        $users  = User::all();
        $admin  = Admin::where('role', 'super_admin')->first();
        $purposes = ['Medical', 'Wedding', 'Education', 'Home Renovation', 'Travel', 'Debt Consolidation', 'Business Expansion'];
        $statuses = [
            'draft', 'personal_filled', 'eligibility_checked', 'payment_pending',
            'payment_done', 'under_review', 'approved', 'rejected', 'disbursed',
        ];

        $count = 0;
        foreach ($users as $user) {
            $apps = rand(1, 2);
            for ($i = 0; $i < $apps; $i++) {
                $status         = $statuses[array_rand($statuses)];
                $requested      = rand(5, 50) * 10000;
                $approved       = $requested * (rand(80, 110) / 100);
                $tenure         = [3, 6, 12, 18, 24, 36][array_rand([3, 6, 12, 18, 24, 36])];
                $rate           = rand(120, 240) / 10;
                $monthlyRate    = ($rate / 100) / 12;
                $emi            = round(($approved * $monthlyRate * pow(1 + $monthlyRate, $tenure)) / (pow(1 + $monthlyRate, $tenure) - 1), 2);
                $createdAt      = now()->subDays(rand(1, 60))->subHours(rand(0, 23));

                $isReviewed = in_array($status, ['approved', 'rejected', 'disbursed']);

                LoanApplication::create([
                    'application_id'   => 'LU' . strtoupper(uniqid()),
                    'user_id'          => $user->id,
                    'requested_amount' => $requested,
                    'approved_amount'  => $isReviewed ? $approved : null,
                    'tenure_months'    => $tenure,
                    'interest_rate'    => $isReviewed ? $rate : null,
                    'emi_amount'       => $isReviewed ? $emi : null,
                    'status'           => $status,
                    'employment_type'  => $user->employment_type ?? 'salaried',
                    'loan_purpose'     => $purposes[array_rand($purposes)],
                    'rejection_reason' => $status === 'rejected' ? 'Insufficient credit score for the requested loan amount.' : null,
                    'reviewed_by'      => $isReviewed ? $admin?->id : null,
                    'reviewed_at'      => $isReviewed ? $createdAt->copy()->addHours(rand(2, 48)) : null,
                    'disbursed_at'     => $status === 'disbursed' ? $createdAt->copy()->addDays(rand(1, 5)) : null,
                    'credit_score'     => (string) rand(580, 820),
                    'is_eligible'      => $status !== 'rejected',
                    'bureau_name'      => 'CIBIL',
                    'score_fetched_at' => $createdAt,
                    'created_at'       => $createdAt,
                    'updated_at'       => $createdAt,
                ]);
                $count++;
            }
        }

        $this->command->info("✅ Seeded {$count} demo loan applications");
    }
}
