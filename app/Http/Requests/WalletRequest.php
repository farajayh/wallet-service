<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

use Illuminate\Http\JsonResponse;

use Illuminate\Validation\Rule;
use App\Currency;

class WalletRequest extends FormRequest
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
        return [
            'name'      => ['required','string', 'min:3', 'max:100'],
            'currency'  => [Rule::enum(Currency::class)],
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
