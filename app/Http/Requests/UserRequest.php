<?php

namespace App\Http\Requests;

use App\Enums\UserTypes;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'type' => 'required|in:1,2,3',
            'bio' => 'required|string',
            'birth_date' => 'required_if:type,' . UserTypes::Admin . '|date',
            'lat' => 'integer|required_if:type,' . UserTypes::Admin,
            'lng' => 'integer|required_if:type,' . UserTypes::Admin,
            'username' => 'required|string',
            'attachment' => 'file|max:2048|mimes:jpeg,png,pdf,required_if:type:' . UserTypes::Arbitrator,
        ];
    }
}
