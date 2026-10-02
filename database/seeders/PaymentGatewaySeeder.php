<?php

namespace Database\Seeders;

use App\Models\PaymentGateway;
use Illuminate\Database\Seeder;

class PaymentGatewaySeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['code' => 'paymob', 'name_en' => 'Paymob', 'name_ar' => 'باي موب', 'sort_order' => 1],
            ['code' => 'fawry', 'name_en' => 'Fawry', 'name_ar' => 'فوري', 'sort_order' => 2],
        ] as $gateway) {
            PaymentGateway::query()->updateOrCreate(
                ['code' => $gateway['code']],
                $gateway + ['is_active' => true]
            );
        }
    }
}
