<?php
namespace App\Http\Requests\Payment;
use App\Http\Requests\ApiFormRequest;
class StorePaymentRequest extends ApiFormRequest { public function rules(): array { return ['gateway_code'=>['required','string','max:64','alpha_dash']]; } }
