<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class RequestRegister extends FormRequest
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
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors(); // Get the list of validation errors
        $firstErrorMessage = $errors->all()[0];
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => $firstErrorMessage,
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
    }
}
