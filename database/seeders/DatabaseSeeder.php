<?php

namespace Database\Seeders;

use App\Models\Transaction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $items = [
            ['income', 'Gaji', 'Gaji bulanan', 12500000, now()->startOfMonth()->addDay()],
            ['income', 'Freelance', 'Proyek desain aplikasi', 3500000, now()->startOfMonth()->addDays(4)],
            ['expense', 'Hunian', 'Sewa apartemen', 3200000, now()->startOfMonth()->addDays(2)],
            ['expense', 'Makanan', 'Belanja kebutuhan bulanan', 1850000, now()->startOfMonth()->addDays(6)],
            ['expense', 'Transportasi', 'Bensin dan parkir', 750000, now()->startOfMonth()->addDays(9)],
            ['expense', 'Langganan', 'Internet dan aplikasi', 525000, now()->startOfMonth()->addDays(10)],
        ];

        foreach (range(1, 5) as $offset) {
            $date = now()->subMonths($offset)->startOfMonth();
            $items[] = ['income', 'Gaji', 'Gaji bulanan', 12000000, $date->copy()->addDay()];
            $items[] = ['expense', 'Kebutuhan', 'Pengeluaran rutin', 4800000 + ($offset * 150000), $date->copy()->addDays(8)];
        }

        foreach ($items as [$type, $category, $description, $amount, $date]) {
            Transaction::create(compact('type', 'category', 'description', 'amount') + ['transaction_date' => $date]);
        }
    }
}
