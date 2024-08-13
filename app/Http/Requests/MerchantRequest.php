<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

use Illuminate\Http\JsonResponse;

class MerchantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
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
        return [
            'name'                  => ['required', 'string', 'min:3', 'max:100'],
            'email'                 => ['required', 'string', 'email', 'max:100', 'unique:merchants'],
            'phone_no'              => ['required', 'phone:INTERNATIONAL'],
            'brand_name'            => ['required', 'string', 'min:3', 'max:100'],
            'brand_description'     => ['required', 'string', 'min:3', 'max:1000'],
            'address'               => ['required', 'string', 'max:255'],
            'city'                  => ['required', 'string', 'max:100'],
            'state'                 => ['required', 'string', 'max:100'],
            'country'               => ['required', 'string', 'max:100'],
            'postal_code'           => ['required', 'digits:6'],
        ];
    }

    public function update(): array
    {
        return [
            'name'                  => ['string', 'min:3', 'max:100'],
            'email'                 => ['string', 'email', 'max:100', 'unique:merchants'],
            'phone_no'              => ['phone:INTERNATIONAL'],
            'brand_name'            => ['string', 'min:3', 'max:100'],
            'brand_description'     => ['string', 'min:3', 'max:1000'],
            'address'               => ['string', 'max:255'],
            'city'                  => ['string', 'max:100'],
            'state'                 => ['string', 'max:100'],
            'country'               => ['string', 'max:100'],
            'postal_code'           => ['digits:6'],
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();

        throw new HttpResponseException(
            response()->json([
                'status'  => false,
                'message' => "Request Failed",
                'errors' => $errors
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
        );
    }

    public function failedAuthorization()
    {
        throw new HttpResponseException(
            response()->json([
                'status'  => false,
                'message' => "Request Aborted",
                'errors' => "Not Authorized"
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
