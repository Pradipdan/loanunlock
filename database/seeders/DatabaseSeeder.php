<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Super Admin
        Admin::updateOrCreate(
            ['email' => 'admin@loanunlock.com'],
            [
                'name'     => 'Super Admin',
                'email'    => 'admin@loanunlock.com',
                'password' => Hash::make('Admin@123'),
                'role'     => 'super_admin',
                'is_active'=> true,
            ]
        );

        // Create Reviewer
        Admin::updateOrCreate(
            ['email' => 'reviewer@loanunlock.com'],
            [
                'name'     => 'Loan Reviewer',
                'email'    => 'reviewer@loanunlock.com',
                'password' => Hash::make('Review@123'),
                'role'     => 'reviewer',
                'is_active'=> true,
            ]
        );

        $this->command->info('✅ Admin accounts created:');
        $this->command->info('   Super Admin: admin@loanunlock.com / Admin@123');
        $this->command->info('   Reviewer:    reviewer@loanunlock.com / Review@123');
    }
}
