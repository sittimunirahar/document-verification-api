<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentVerificationRequest extends FormRequest
{
    public const SUPPORTED_FILE_FORMAT = 'json';
    public const MAX_FILE_SIZE = 2048;

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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'mimes:' . self::SUPPORTED_FILE_FORMAT, 'filled', 'max:' . self::MAX_FILE_SIZE],
        ];
    }
}
