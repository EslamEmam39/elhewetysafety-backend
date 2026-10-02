<?php
namespace Database\Seeders;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentGateway;
use Illuminate\Database\Seeder;
class PaymentSeeder extends Seeder
{
    public function run(): void { if(!app()->environment('local'))return; $order=Order::where('code','ORD-DEMO-PENDING')->firstOrFail(); $gateway=PaymentGateway::where('code','paymob')->firstOrFail(); Payment::updateOrCreate(['code'=>'PAY-DEMO-PENDING'],['order_id'=>$order->id,'payment_gateway_id'=>$gateway->id,'amount'=>$order->amount,'currency'=>$order->currency,'status'=>PaymentStatus::Pending,'idempotency_key'=>'demo-payment-attempt']); }
}
