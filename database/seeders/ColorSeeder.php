<?php

namespace Database\Seeders;

use App\Models\Color;
use Illuminate\Database\Seeder;

/** Seeds 15 common car colors with bilingual names. */
class ColorSeeder extends Seeder
{
    /** Inserts colors idempotently using firstOrCreate. */
    public function run(): void
    {
        $colors = [
            ['name_ar' => 'أبيض',      'name_en' => 'White'],
            ['name_ar' => 'أسود',      'name_en' => 'Black'],
            ['name_ar' => 'فضي',       'name_en' => 'Silver'],
            ['name_ar' => 'رمادي',     'name_en' => 'Gray'],
            ['name_ar' => 'أحمر',      'name_en' => 'Red'],
            ['name_ar' => 'أزرق',      'name_en' => 'Blue'],
            ['name_ar' => 'كحلي',      'name_en' => 'Navy Blue'],
            ['name_ar' => 'أخضر',      'name_en' => 'Green'],
            ['name_ar' => 'بني',       'name_en' => 'Brown'],
            ['name_ar' => 'بيج',       'name_en' => 'Beige'],
            ['name_ar' => 'ذهبي',      'name_en' => 'Gold'],
            ['name_ar' => 'برتقالي',   'name_en' => 'Orange'],
            ['name_ar' => 'أصفر',      'name_en' => 'Yellow'],
            ['name_ar' => 'عنابي',     'name_en' => 'Maroon'],
            ['name_ar' => 'تيتانيوم',  'name_en' => 'Titanium'],
        ];

        foreach ($colors as $color) {
            Color::firstOrCreate(['name_en' => $color['name_en']], $color);
        }
    }
}
