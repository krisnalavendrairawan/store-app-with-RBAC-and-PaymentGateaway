<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;
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
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'required|string|max:15|unique:users,phone',
            'password' => 'nullable|string|min:8',
            'password_confirmation' => 'nullable|same:password',
        ];

        if ($this->method() == 'PUT') {
            $rules['email'] = 'required|string|email|max:255|unique:users,email,' . $this->user->id;
            $rules['phone'] = 'required|string|max:15|unique:users,phone,' . $this->user->id;
        }

        return $rules;
    }

    public function attributes(){
        return [
            'name' => __('label.name'),
            'email' => __('label.email'),
            'phone' => __('label.phone_number'),
            'password' => __('label.password'),
        ];
    }
}
