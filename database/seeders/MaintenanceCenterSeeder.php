<?php

namespace Database\Seeders;

use App\Models\MaintenanceCenter;
use App\Models\MaintenanceService;
use Illuminate\Database\Seeder;

/** Seeds a handful of maintenance centers, each with several bookable services. */
class MaintenanceCenterSeeder extends Seeder
{
    /** Inserts maintenance centers and their services idempotently. */
    public function run(): void
    {
        $centers = [
            [
                'name_ar' => 'مركز الخدمة السريعة - مدينة نصر',
                'name_en' => 'Quick Service Center - Nasr City',
                'phone'   => '01001234567',
                'services' => [
                    ['name_ar' => 'تغيير زيت المحرك', 'name_en' => 'Engine Oil Change', 'price' => 250],
                    ['name_ar' => 'فحص شامل', 'name_en' => 'Full Inspection', 'price' => 400],
                    ['name_ar' => 'تغيير الإطارات', 'name_en' => 'Tire Replacement', 'price' => 1200],
                ],
            ],
            [
                'name_ar' => 'مركز النخبة للصيانة - المعادي',
                'name_en' => 'Elite Auto Care - Maadi',
                'phone'   => '01112345678',
                'services' => [
                    ['name_ar' => 'صيانة الفرامل', 'name_en' => 'Brake Service', 'price' => 600],
                    ['name_ar' => 'صيانة التكييف', 'name_en' => 'AC Service', 'price' => 350],
                    ['name_ar' => 'فحص الكهرباء', 'name_en' => 'Electrical Diagnostics', 'price' => 300],
                ],
            ],
            [
                'name_ar' => 'مركز الجيزة لصيانة السيارات',
                'name_en' => 'Giza Auto Maintenance Center',
                'phone'   => '01223456789',
                'services' => [
                    ['name_ar' => 'غسيل وتلميع', 'name_en' => 'Wash & Polish', 'price' => 150],
                    ['name_ar' => 'ضبط الزوايا', 'name_en' => 'Wheel Alignment', 'price' => 300],
                ],
            ],
        ];

        foreach ($centers as $centerData) {
            $services = $centerData['services'];
            unset($centerData['services']);

            $center = MaintenanceCenter::firstOrCreate(
                ['name_en' => $centerData['name_en']],
                array_merge($centerData, ['is_active' => true, 'rating' => fake()->randomFloat(2, 3.5, 5)])
            );

            foreach ($services as $index => $service) {
                MaintenanceService::firstOrCreate(
                    ['maintenance_center_id' => $center->id, 'name_en' => $service['name_en']],
                    array_merge($service, ['sort_order' => $index, 'is_active' => true])
                );
            }
        }
    }
}
