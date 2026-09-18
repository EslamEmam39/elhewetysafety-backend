<?php

namespace Database\Seeders;

use App\Models\CourseCategory;
use Illuminate\Database\Seeder;

class CourseCategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['slug' => 'occupational-safety', 'name_en' => 'Occupational Safety', 'name_ar' => 'السلامة المهنية', 'sort_order' => 1],
            ['slug' => 'fire-safety', 'name_en' => 'Fire Safety', 'name_ar' => 'السلامة من الحرائق', 'sort_order' => 2],
            ['slug' => 'first-aid', 'name_en' => 'First Aid', 'name_ar' => 'الإسعافات الأولية', 'sort_order' => 3],
        ] as $category) {
            CourseCategory::query()->updateOrCreate(['slug' => $category['slug']], $category + ['is_active' => true]);
        }
    }
}
