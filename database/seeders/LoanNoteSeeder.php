<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\LoanApplication;
use App\Models\LoanNote;
use Illuminate\Database\Seeder;

class LoanNoteSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Admin::where('role', 'super_admin')->first();
        if (!$admin) {
            $this->command->warn('No super_admin found, skipping notes.');
            return;
        }

        $approvalNotes = [
            'Loan approved after CIBIL verification.',
            'Documents verified, eligibility confirmed.',
            'Approved at requested amount post-review.',
            'Customer profile meets all criteria.',
        ];

        $rejectionNotes = [
            'Insufficient credit score for the requested loan amount.',
            'Income-to-EMI ratio exceeds permissible limits.',
            'Recent loan defaults reported on credit bureau.',
            'Bank statement does not reflect declared monthly income.',
        ];

        $disburseNotes = [
            'Amount transferred to registered bank account.',
            'Disbursal completed via NEFT.',
            'Loan disbursed successfully to borrower.',
        ];

        $count = 0;
        $reviewed = LoanApplication::whereIn('status', ['approved', 'rejected', 'disbursed'])->get();
        foreach ($reviewed as $app) {
            $note = match ($app->status) {
                'approved'  => $approvalNotes[array_rand($approvalNotes)],
                'rejected'  => $rejectionNotes[array_rand($rejectionNotes)],
                'disbursed' => $disburseNotes[array_rand($disburseNotes)],
            };

            LoanNote::create([
                'loan_application_id' => $app->id,
                'admin_id'            => $admin->id,
                'note'                => $note,
                'type'                => $app->status === 'disbursed' ? 'disbursement' : ($app->status === 'rejected' ? 'rejection' : 'approval'),
                'created_at'          => $app->reviewed_at ?? $app->updated_at,
                'updated_at'          => $app->reviewed_at ?? $app->updated_at,
            ]);
            $count++;
        }

        $this->command->info("✅ Seeded {$count} demo loan notes");
    }
}
