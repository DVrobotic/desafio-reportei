<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
            'email' => 'required|email|string|unique:users',
            'cpf' => 'required|cpf|string|unique:users',
            'dateBirth' => 'required',
            'type' => 'required',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required_with:password',
        ];
    }

    public function attributes(){
        return [
            'name' => 'nome',
            'email' => 'e-mail',
            'cpf' => 'cpf',
            'dateBirth' => 'data de nascimento',
            'type' => 'tipo',
            'password' => 'senha',
            'password_confirmation' => 'confirmação de senha'
        ];
    }

    public function messages()
    {
        return [
            'cpf.cpf' => 'CPF inválido.'
        ];
    }
}
