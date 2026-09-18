<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            ['SA', 'Saudi Arabia', 'المملكة العربية السعودية', '+966', [['sa-riyadh', 'Riyadh', 'الرياض'], ['sa-jeddah', 'Jeddah', 'جدة']]],
            ['EG', 'Egypt', 'مصر', '+20', [['eg-cairo', 'Cairo', 'القاهرة'], ['eg-giza', 'Giza', 'الجيزة']]],
            ['AE', 'United Arab Emirates', 'الإمارات العربية المتحدة', '+971', [['ae-dubai', 'Dubai', 'دبي'], ['ae-abu-dhabi', 'Abu Dhabi', 'أبوظبي']]],
            ['KW', 'Kuwait', 'الكويت', '+965', [['kw-kuwait-city', 'Kuwait City', 'مدينة الكويت']]],
            ['QA', 'Qatar', 'قطر', '+974', [['qa-doha', 'Doha', 'الدوحة']]],
            ['OM', 'Oman', 'عُمان', '+968', [['om-muscat', 'Muscat', 'مسقط']]],
            ['BH', 'Bahrain', 'البحرين', '+973', [['bh-manama', 'Manama', 'المنامة']]],
            ['JO', 'Jordan', 'الأردن', '+962', [['jo-amman', 'Amman', 'عمّان']]],
        ];

        foreach ($locations as $countryOrder => [$code, $nameEn, $nameAr, $phoneCode, $cities]) {
            $country = Country::query()->updateOrCreate(
                ['code' => $code],
                ['name_en' => $nameEn, 'name_ar' => $nameAr, 'phone_code' => $phoneCode, 'is_active' => true, 'sort_order' => $countryOrder + 1]
            );
            foreach ($cities as $cityOrder => [$cityCode, $cityNameEn, $cityNameAr]) {
                City::query()->updateOrCreate(
                    ['country_id' => $country->id, 'code' => $cityCode],
                    ['name_en' => $cityNameEn, 'name_ar' => $cityNameAr, 'is_active' => true, 'sort_order' => $cityOrder + 1]
                );
            }
        }
    }
}
