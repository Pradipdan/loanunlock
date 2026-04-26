<?php

namespace Database\Seeders;

use App\Models\LoanApplication;
use App\Models\Payment;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $paidStatuses = ['payment_done', 'under_review', 'approved', 'rejected', 'disbursed'];
        $apps = LoanApplication::whereIn('status', $paidStatuses)->get();

        $count = 0;
        foreach ($apps as $app) {
            Payment::create([
                'user_id'             => $app->user_id,
                'loan_application_id' => $app->id,
                'transaction_id'      => 'TXN' . strtoupper(uniqid()),
                'payment_gateway_id'  => 'pay_' . strtoupper(uniqid()),
                'amount'              => 299.00,
                'method'              => 'razorpay',
                'status'              => 'success',
                'paid_at'             => $app->created_at->copy()->addMinutes(rand(5, 60)),
                'created_at'          => $app->created_at->copy()->addMinutes(rand(5, 60)),
                'updated_at'          => $app->created_at->copy()->addMinutes(rand(5, 60)),
            ]);
            $count++;
        }

        // Add a few pending/failed payments for realism
        $pendingApps = LoanApplication::where('status', 'payment_pending')->limit(3)->get();
        foreach ($pendingApps as $app) {
            Payment::create([
                'user_id'             => $app->user_id,
                'loan_application_id' => $app->id,
                'transaction_id'      => 'TXN' . strtoupper(uniqid()),
                'payment_gateway_id'  => 'order_' . strtoupper(uniqid()),
                'amount'              => 299.00,
                'method'              => 'razorpay',
                'status'              => 'pending',
            ]);
            $count++;
        }

        $this->command->info("✅ Seeded {$count} demo payments");
    }
}
