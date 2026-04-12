<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    public function run()
    {
        $employees = [
            [
                'name' => 'Tori Firmansyah',
                'email' => 'tori@example.com',
                'position' => 'Kasir',
                'is_active' => true
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@example.com',
                'position' => 'Barista',
                'is_active' => true
            ],
            [
                'name' => 'Siti Aminah',
                'email' => 'siti@example.com',
                'position' => 'Waitress',
                'is_active' => true
            ],
            [
                'name' => 'Ahmad Hidayat',
                'email' => 'ahmad@example.com',
                'position' => 'Chef',
                'is_active' => true
            ],
        ];

        foreach ($employees as $employee) {
            Employee::create($employee);
        }
    }
}