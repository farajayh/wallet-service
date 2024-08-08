<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return ($this->isMethod('POST') ? $this->store() : $this->update());
    }

    public function store(): array
    {
        /**
         * 
         * $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone_no');
            $table->string('address');
            $table->string('city');
            $table->string('state');
            $table->string('country');
            $table->string('postal_code')->nullable();
         */
        return [
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'string', 'email', 'max:255'],
            'phone_no'  => ['required', 'phone:US,INTERNATIONAL']
        ];
    }

    public function update(): array
    {
        return [
            'title'       => ['string', 'max:255'],
            'description' => ['string', 'min:10'],
            'due_date'    => ['date', 'date_format:Y-m-d'],
        ];
    }
}
