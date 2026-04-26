<?php

namespace Database\Seeders;

use App\Models\Document;
use App\Models\User;
use Illuminate\Database\Seeder;

class DocumentSeeder extends Seeder
{
    public function run(): void
    {
        $types = ['pan', 'aadhar_front', 'aadhar_back', 'selfie', 'salary_slip', 'bank_statement'];
        $verifyStatuses = ['pending', 'verified', 'rejected'];

        $count = 0;
        foreach (User::all() as $user) {
            $app = $user->latestApplication;
            $userTypes = array_slice($types, 0, rand(2, 5));

            foreach ($userTypes as $type) {
                Document::create([
                    'user_id'             => $user->id,
                    'loan_application_id' => $app?->id,
                    'type'                => $type,
                    'file_path'           => "documents/{$user->id}/{$type}_" . uniqid() . '.jpg',
                    'original_name'       => "{$type}.jpg",
                    'verification_status' => $verifyStatuses[array_rand($verifyStatuses)],
                ]);
                $count++;
            }
        }

        $this->command->info("✅ Seeded {$count} demo documents");
    }
}
