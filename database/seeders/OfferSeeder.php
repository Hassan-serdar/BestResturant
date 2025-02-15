<?php

namespace Database\Seeders;

use App\Models\Offer;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class OfferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Offer::factory(20)->create();
    }
}
