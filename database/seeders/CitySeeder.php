<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

/** Seeds the ten most prominent Egyptian cities with bilingual names. */
class CitySeeder extends Seeder
{
    /** Inserts 10 Egyptian cities, skipping duplicates to allow safe re-runs. */
    public function run(): void
    {
        $cities = [
            ['name_ar' => 'القاهرة',       'name_en' => 'Cairo'],
            ['name_ar' => 'الإسكندرية',    'name_en' => 'Alexandria'],
            ['name_ar' => 'الجيزة',        'name_en' => 'Giza'],
            ['name_ar' => 'شرم الشيخ',     'name_en' => 'Sharm El-Sheikh'],
            ['name_ar' => 'الأقصر',        'name_en' => 'Luxor'],
            ['name_ar' => 'أسوان',         'name_en' => 'Aswan'],
            ['name_ar' => 'الغردقة',       'name_en' => 'Hurghada'],
            ['name_ar' => 'المنصورة',      'name_en' => 'Mansoura'],
            ['name_ar' => 'طنطا',          'name_en' => 'Tanta'],
            ['name_ar' => 'الإسماعيلية',   'name_en' => 'Ismailia'],
        ];

        foreach ($cities as $city) {
            City::firstOrCreate(['name_en' => $city['name_en']], $city);
        }
    }
}
