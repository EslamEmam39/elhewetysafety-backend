<?php
namespace App\Http\Requests\Payment;
use App\Enums\PaymentStatus;
use App\Http\Requests\ApiFormRequest;
use Illuminate\Validation\Rule;
class AdminPaymentIndexRequest extends ApiFormRequest { public function rules(): array { return ['status'=>['sometimes','string',Rule::enum(PaymentStatus::class)],'gateway_code'=>['sometimes','string','max:64'],'per_page'=>['sometimes','integer','min:1','max:100']]; } }
