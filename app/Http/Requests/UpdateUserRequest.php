<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class UpdateUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => 'sometimes|unique:users|string|max:255',
            'email' => 'sometimes|unique:users|string|email|max:255',
            'bio' => 'sometimes|string',
        ];
    }

    /**
     * @return array<mixed>
     */
    public function validationData()
    {
        return Arr::wrap($this->input('user'));
    }
}
