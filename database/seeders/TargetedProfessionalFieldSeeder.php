<?php

namespace Database\Seeders;

use App\Models\TargetedProfessionalField;
use Illuminate\Database\Seeder;

class TargetedProfessionalFieldSeeder extends Seeder
{
    public function run(): void
    {
        $fields = [
            ['code' => 'nebosh', 'name_en' => 'Occupational Health and Safety (NEBOSH)', 'name_ar' => 'السلامة والصحة المهنية (NEBOSH)', 'sort_order' => 1],
            ['code' => 'osha', 'name_en' => 'General Industry and Construction Safety (OSHA)', 'name_ar' => 'السلامة العامة في المنشآت والإنشاءات (OSHA)', 'sort_order' => 2],
            ['code' => 'iosh', 'name_en' => 'Managing Safely (IOSH)', 'name_ar' => 'إدارة السلامة بأسلوب آمن (IOSH)', 'sort_order' => 3],
            ['code' => 'hazmat', 'name_en' => 'Hazardous Materials and Chemical Safety (HAZMAT)', 'name_ar' => 'المواد الخطرة والسلامة الكيميائية (HAZMAT)', 'sort_order' => 4],
            ['code' => 'nfpa', 'name_en' => 'Firefighting and Field Evacuation (NFPA)', 'name_ar' => 'مكافحة الحرائق والإخلاء الميداني (NFPA)', 'sort_order' => 5],
            ['code' => 'highfield', 'name_en' => 'Advanced First Aid (Highfield)', 'name_ar' => 'الإسعافات الأولية المتقدمة (Highfield)', 'sort_order' => 6],
        ];

        foreach ($fields as $field) {
            $model = TargetedProfessionalField::query()->firstOrNew([
                'code' => $field['code'],
            ]);

            $model->fill($field + ['is_active' => true])->save();
        }
    }
}
