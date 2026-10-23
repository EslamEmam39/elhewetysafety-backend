<?php

namespace Database\Seeders;

use App\Models\PaymentGateway;
use Illuminate\Database\Seeder;

class PaymentGatewaySeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['code' => 'cash', 'name_en' => 'Cash payment', 'name_ar' => 'دفع نقدي', 'sort_order' => 1],
            ['code' => 'paymob', 'name_en' => 'Paymob', 'name_ar' => 'باي موب', 'sort_order' => 2],
            ['code' => 'fawry', 'name_en' => 'Fawry', 'name_ar' => 'فوري', 'sort_order' => 3],
        ] as $gateway) {
            PaymentGateway::query()->updateOrCreate(
                ['code' => $gateway['code']],
                $gateway + ['is_active' => true]
            );
        }
    }
}
