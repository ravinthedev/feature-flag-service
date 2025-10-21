<?php

namespace Database\Seeders;

use App\Models\CarReport;
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
                'description' => 'Small dent on front bumper from parking. Paint is scratched but no structural damage.',
                'damage_type' => 'minor',
                'photo_url' => null,
                'status' => 'pending',
            ],
            [
                'car_model' => 'Honda Civic 2019',
                'description' => 'Door panel has a noticeable dent. Needs body work and repainting.',
                'damage_type' => 'minor',
                'photo_url' => null,
                'status' => 'in_progress',
            ],
            [
                'car_model' => 'Ford F-150 2021',
                'description' => 'Rear bumper bent after being hit. Taillight also cracked and needs replacement.',
                'damage_type' => 'moderate',
                'photo_url' => null,
                'status' => 'completed',
            ],
            [
                'car_model' => 'BMW X5 2018',
                'description' => 'Hood and roof have multiple dents from hail. Windows intact but paintwork damaged.',
                'damage_type' => 'severe',
                'photo_url' => null,
                'status' => 'pending',
            ],
            [
                'car_model' => 'Tesla Model 3 2022',
                'description' => 'Keyed on driver side. Long scratch down the door panel.',
                'damage_type' => 'minor',
                'photo_url' => null,
                'status' => 'completed',
            ],
            [
                'car_model' => 'Mazda CX-5 2021',
                'description' => 'Windshield has a crack spreading from the lower left corner. Happened after a stone hit.',
                'damage_type' => 'minor',
                'photo_url' => null,
                'status' => 'pending',
            ],
        ];

        foreach ($carReports as $report) {
            CarReport::create($report);
        }
    }
}
