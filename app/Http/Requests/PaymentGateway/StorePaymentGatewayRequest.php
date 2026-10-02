<?php

namespace App\Http\Requests\PaymentGateway;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StorePaymentGatewayRequest extends ApiFormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->has('code')) {
            $this->merge(['code' => Str::lower((string) $this->input('code'))]);
        }
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:64', 'alpha_dash', Rule::unique('payment_gateways', 'code')],
            'name_en' => ['required', 'string', 'max:255'],
            'name_ar' => ['required', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0', 'max:65535'],
        ];
    }
}
