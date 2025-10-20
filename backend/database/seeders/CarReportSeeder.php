<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CarReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $carReports = [
            [
                'car_model' => 'Toyota Camry 2020',
                'description' => 'Front bumper damage from parking lot incident',
                'damage_type' => 'minor',
                'photo_url' => 'https://example.com/photos/camry-front-damage.jpg',
                'status' => 'pending',
            ],
            [
                'car_model' => 'Honda Civic 2019',
                'description' => 'Side door dent from shopping cart collision',
                'damage_type' => 'minor',
                'photo_url' => 'https://example.com/photos/civic-side-dent.jpg',
                'status' => 'in_progress',
            ],
            [
                'car_model' => 'Ford F-150 2021',
                'description' => 'Rear-end collision with significant tailgate damage',
                'damage_type' => 'moderate',
                'photo_url' => 'https://example.com/photos/f150-rear-damage.jpg',
                'status' => 'completed',
            ],
            [
                'car_model' => 'BMW X5 2018',
                'description' => 'Multiple panel damage from hail storm',
                'damage_type' => 'severe',
                'photo_url' => 'https://example.com/photos/bmx-x5-hail-damage.jpg',
                'status' => 'pending',
            ],
            [
                'car_model' => 'Tesla Model 3 2022',
                'description' => 'Minor scratch on driver side door',
                'damage_type' => 'minor',
                'photo_url' => 'https://example.com/photos/tesla-scratch.jpg',
                'status' => 'completed',
            ],
            [
                'car_model' => 'Chevrolet Malibu 2017',
                'description' => 'Front-end collision with extensive damage',
                'damage_type' => 'severe',
                'photo_url' => 'https://example.com/photos/malibu-front-collision.jpg',
                'status' => 'in_progress',
            ],
            [
                'car_model' => 'Nissan Altima 2020',
                'description' => 'Windshield crack from road debris',
                'damage_type' => 'minor',
                'photo_url' => 'https://example.com/photos/altima-windshield.jpg',
                'status' => 'pending',
            ],
            [
                'car_model' => 'Jeep Wrangler 2019',
                'description' => 'Rollover accident with roof and side damage',
                'damage_type' => 'total_loss',
                'photo_url' => 'https://example.com/photos/wrangler-rollover.jpg',
                'status' => 'rejected',
            ],
            [
                'car_model' => 'Subaru Outback 2021',
                'description' => 'Rear bumper damage from backing into pole',
                'damage_type' => 'moderate',
                'photo_url' => 'https://example.com/photos/outback-rear-damage.jpg',
                'status' => 'completed',
            ],
            [
                'car_model' => 'Audi A4 2020',
                'description' => 'Side mirror replacement needed after parking incident',
                'damage_type' => 'minor',
                'photo_url' => 'https://example.com/photos/audi-mirror.jpg',
                'status' => 'in_progress',
            ],
        ];

        foreach ($carReports as $report) {
            \App\Models\CarReport::create($report);
        }
    }
}
