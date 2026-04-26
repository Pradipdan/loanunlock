<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $names = [
            'Rahul Sharma', 'Priya Patel', 'Amit Kumar', 'Sneha Reddy', 'Vikram Singh',
            'Anjali Mehta', 'Rohit Verma', 'Pooja Iyer', 'Karan Gupta', 'Neha Desai',
            'Suresh Nair', 'Kavita Joshi', 'Arjun Rao', 'Ritu Bansal', 'Manoj Tiwari',
            'Divya Pillai', 'Sandeep Yadav', 'Aishwarya Menon', 'Vivek Chauhan', 'Meera Krishnan',
            'Rajesh Khanna', 'Shweta Agarwal', 'Nikhil Bhat', 'Sunita Rao', 'Deepak Malhotra',
        ];

        $states = ['Maharashtra', 'Karnataka', 'Delhi', 'Tamil Nadu', 'Gujarat', 'Rajasthan', 'Telangana', 'West Bengal'];
        $cities = ['Mumbai', 'Bengaluru', 'Delhi', 'Chennai', 'Ahmedabad', 'Jaipur', 'Hyderabad', 'Kolkata'];
        $companies = ['Infosys', 'TCS', 'Wipro', 'Reliance', 'HDFC Bank', 'ICICI Bank', 'Self-Employed', 'Tata Motors', 'Flipkart'];
        $banks = ['HDFC Bank', 'ICICI Bank', 'SBI', 'Axis Bank', 'Kotak Mahindra Bank'];

        foreach ($names as $i => $name) {
            $mobile  = '9' . str_pad((string) (100000000 + $i * 12345), 9, '0', STR_PAD_LEFT);
            $first   = strtolower(strtok($name, ' '));
            $email   = $first . ($i + 1) . '@example.com';
            $pan     = strtoupper(substr(str_replace(' ', '', $name), 0, 5)) . rand(1000, 9999) . chr(rand(65, 90));

            User::updateOrCreate(
                ['mobile' => $mobile],
                [
                    'name'                => $name,
                    'email'               => $email,
                    'pan_number'          => $pan,
                    'date_of_birth'       => now()->subYears(rand(22, 50))->subDays(rand(0, 365))->toDateString(),
                    'state'               => $states[array_rand($states)],
                    'city'                => $cities[array_rand($cities)],
                    'pincode'             => (string) rand(110001, 560100),
                    'preferred_language'  => 'ENGLISH',
                    'employment_type'     => rand(0, 1) ? 'salaried' : 'business',
                    'monthly_income'      => (string) rand(20000, 200000),
                    'company_name'        => $companies[array_rand($companies)],
                    'is_verified'         => true,
                    'is_blocked'          => false,
                    'permissions_granted' => true,
                    'address'             => rand(1, 999) . ', ' . ['MG Road', 'Park Street', 'Linking Road', 'Brigade Road'][array_rand(['MG Road', 'Park Street', 'Linking Road', 'Brigade Road'])],
                    'aadhar_number'       => (string) rand(100000000000, 999999999999),
                    'bank_account'        => (string) rand(10000000000, 99999999999),
                    'bank_ifsc'           => 'HDFC0' . str_pad((string) rand(0, 9999), 6, '0', STR_PAD_LEFT),
                    'bank_name'           => $banks[array_rand($banks)],
                ]
            );
        }

        $this->command->info('✅ Seeded ' . count($names) . ' demo users');
    }
}
