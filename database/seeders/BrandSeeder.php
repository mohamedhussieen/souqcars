<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\CarModel;
use Illuminate\Database\Seeder;

/** Seeds 10 popular car brands with 3–5 models each, all with bilingual names. */
class BrandSeeder extends Seeder
{
    /** Inserts brands and their models idempotently using firstOrCreate. */
    public function run(): void
    {
        $brands = [
            [
                'name_ar' => 'تويوتا',
                'name_en' => 'Toyota',
                'models'  => [
                    ['name_ar' => 'كامري',    'name_en' => 'Camry'],
                    ['name_ar' => 'كورولا',   'name_en' => 'Corolla'],
                    ['name_ar' => 'يارس',     'name_en' => 'Yaris'],
                    ['name_ar' => 'راف فور',  'name_en' => 'RAV4'],
                    ['name_ar' => 'هايلكس',   'name_en' => 'Hilux'],
                ],
            ],
            [
                'name_ar' => 'هيونداي',
                'name_en' => 'Hyundai',
                'models'  => [
                    ['name_ar' => 'إيلانترا',  'name_en' => 'Elantra'],
                    ['name_ar' => 'توسان',     'name_en' => 'Tucson'],
                    ['name_ar' => 'سانتافي',   'name_en' => 'Santa Fe'],
                    ['name_ar' => 'أكسنت',    'name_en' => 'Accent'],
                ],
            ],
            [
                'name_ar' => 'كيا',
                'name_en' => 'Kia',
                'models'  => [
                    ['name_ar' => 'سيراتو',   'name_en' => 'Cerato'],
                    ['name_ar' => 'سبورتاج',  'name_en' => 'Sportage'],
                    ['name_ar' => 'سورينتو',  'name_en' => 'Sorento'],
                    ['name_ar' => 'بيكانتو',  'name_en' => 'Picanto'],
                    ['name_ar' => 'ريو',      'name_en' => 'Rio'],
                ],
            ],
            [
                'name_ar' => 'نيسان',
                'name_en' => 'Nissan',
                'models'  => [
                    ['name_ar' => 'صني',       'name_en' => 'Sunny'],
                    ['name_ar' => 'التيما',    'name_en' => 'Altima'],
                    ['name_ar' => 'إكس تريل', 'name_en' => 'X-Trail'],
                    ['name_ar' => 'جوك',      'name_en' => 'Juke'],
                ],
            ],
            [
                'name_ar' => 'هوندا',
                'name_en' => 'Honda',
                'models'  => [
                    ['name_ar' => 'سيفيك',    'name_en' => 'Civic'],
                    ['name_ar' => 'أكورد',    'name_en' => 'Accord'],
                    ['name_ar' => 'سي آر في', 'name_en' => 'CR-V'],
                    ['name_ar' => 'فيت',      'name_en' => 'Fit'],
                ],
            ],
            [
                'name_ar' => 'فولكسواجن',
                'name_en' => 'Volkswagen',
                'models'  => [
                    ['name_ar' => 'جولف',     'name_en' => 'Golf'],
                    ['name_ar' => 'باسات',    'name_en' => 'Passat'],
                    ['name_ar' => 'تيجوان',   'name_en' => 'Tiguan'],
                ],
            ],
            [
                'name_ar' => 'بي إم دبليو',
                'name_en' => 'BMW',
                'models'  => [
                    ['name_ar' => 'الفئة الثالثة', 'name_en' => '3 Series'],
                    ['name_ar' => 'الفئة الخامسة', 'name_en' => '5 Series'],
                    ['name_ar' => 'إكس فايف',      'name_en' => 'X5'],
                    ['name_ar' => 'إكس ثري',       'name_en' => 'X3'],
                ],
            ],
            [
                'name_ar' => 'مرسيدس بنز',
                'name_en' => 'Mercedes-Benz',
                'models'  => [
                    ['name_ar' => 'سي كلاس',  'name_en' => 'C-Class'],
                    ['name_ar' => 'إي كلاس',  'name_en' => 'E-Class'],
                    ['name_ar' => 'جي إل إي', 'name_en' => 'GLE'],
                    ['name_ar' => 'إس كلاس',  'name_en' => 'S-Class'],
                    ['name_ar' => 'جي إل سي', 'name_en' => 'GLC'],
                ],
            ],
            [
                'name_ar' => 'شيفروليه',
                'name_en' => 'Chevrolet',
                'models'  => [
                    ['name_ar' => 'ماليبو',   'name_en' => 'Malibu'],
                    ['name_ar' => 'كروز',     'name_en' => 'Cruze'],
                    ['name_ar' => 'سبارك',    'name_en' => 'Spark'],
                    ['name_ar' => 'ترافيرس',  'name_en' => 'Traverse'],
                ],
            ],
            [
                'name_ar' => 'ميتسوبيشي',
                'name_en' => 'Mitsubishi',
                'models'  => [
                    ['name_ar' => 'لانسر',    'name_en' => 'Lancer'],
                    ['name_ar' => 'أوتلاندر', 'name_en' => 'Outlander'],
                    ['name_ar' => 'إكليبس',   'name_en' => 'Eclipse Cross'],
                ],
            ],
        ];

        foreach ($brands as $brandData) {
            $models = $brandData['models'];
            unset($brandData['models']);

            $brand = Brand::firstOrCreate(['name_en' => $brandData['name_en']], $brandData);

            foreach ($models as $model) {
                CarModel::firstOrCreate(
                    ['brand_id' => $brand->id, 'name_en' => $model['name_en']],
                    array_merge($model, ['brand_id' => $brand->id])
                );
            }
        }
    }
}
