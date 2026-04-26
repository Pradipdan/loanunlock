<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->warn('⚠️  Seeding DEMO data — do NOT run on production with real users.');

        $this->call([
            DatabaseSeeder::class,
            UserSeeder::class,
            LoanApplicationSeeder::class,
            PaymentSeeder::class,
            DocumentSeeder::class,
            LoanNoteSeeder::class,
        ]);

        $this->command->info('🎉 All demo data seeded.');
    }
}
