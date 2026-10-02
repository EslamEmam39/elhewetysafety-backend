<?php

namespace App\Http\Requests\PaymentGateway;

use App\Http\Requests\ApiFormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdatePaymentGatewayRequest extends ApiFormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->has('code')) {
            $this->merge(['code' => Str::lower((string) $this->input('code'))]);
        }
    }

    public function rules(): array
    {
        $gateway = $this->route('payment_gateway');

        return [
            'code' => ['sometimes', 'string', 'max:64', 'alpha_dash', Rule::unique('payment_gateways', 'code')->ignore($gateway?->id)],
            'name_en' => ['sometimes', 'string', 'max:255'],
            'name_ar' => ['sometimes', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0', 'max:65535'],
        ];
    }
}
