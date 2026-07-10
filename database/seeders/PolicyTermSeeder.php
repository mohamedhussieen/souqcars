<?php

namespace Database\Seeders;

use App\Models\PolicyTerm;
use Illuminate\Database\Seeder;

/** Seeds the app's bilingual terms & conditions clauses idempotently, keyed by order. */
class PolicyTermSeeder extends Seeder
{
    public function run(): void
    {
        $terms = [
            [
                'order'    => 1,
                'title_ar' => 'استخدام التطبيق',
                'title_en' => 'App Usage',
                'body_ar'  => 'التطبيق منصة وسيطة لعرض سيارات المعارض والأفراد. يوافق المستخدم على استخدام التطبيق لأغراض البيع والشراء المشروعة فقط.',
                'body_en'  => 'The app is an intermediary platform for listing cars from showrooms and individuals. The user agrees to use the app only for legitimate buying and selling purposes.',
            ],
            [
                'order'    => 2,
                'title_ar' => 'دقة البيانات',
                'title_en' => 'Data Accuracy',
                'body_ar'  => 'المعرض أو البائع مسؤول عن صحة بيانات وصور العربية المعروضة، وأي وصف مخالف للواقع يعرض الإعلان للإزالة.',
                'body_en'  => 'The showroom or seller is responsible for the accuracy of the listed car\'s data and photos; any misleading description subjects the listing to removal.',
            ],
            [
                'order'    => 3,
                'title_ar' => 'التواصل بين الأطراف',
                'title_en' => 'Communication Between Parties',
                'body_ar'  => 'المنصة وسيطة فقط بين البائع والمشتري، ولا تتحمل مسؤولية أي اتفاق أو تعامل مالي يتم خارج التطبيق.',
                'body_en'  => 'The platform is only an intermediary between the seller and the buyer, and bears no responsibility for any agreement or financial dealing made outside the app.',
            ],
            [
                'order'    => 4,
                'title_ar' => 'الخصوصية',
                'title_en' => 'Privacy',
                'body_ar'  => 'بياناتك الشخصية تُستخدم فقط لتشغيل الحساب والتواصل بخصوص طلباتك، ولا تتم مشاركتها مع أي جهة خارجية دون إذنك.',
                'body_en'  => 'Your personal data is used only to operate your account and communicate about your requests, and is not shared with any third party without your consent.',
            ],
            [
                'order'    => 5,
                'title_ar' => 'إيقاف الحساب',
                'title_en' => 'Account Suspension',
                'body_ar'  => 'تحتفظ المنصة بحق إيقاف أي حساب يخالف هذه الشروط أو يسيء استخدام التطبيق.',
                'body_en'  => 'The platform reserves the right to suspend any account that violates these terms or misuses the app.',
            ],
        ];

        foreach ($terms as $term) {
            PolicyTerm::updateOrCreate(['order' => $term['order']], $term);
        }
    }
}
