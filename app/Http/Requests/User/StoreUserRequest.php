<?php

namespace App\Http\Requests\User;

use App\Services\Dto\UserDto;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Gate::allows('user-store');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'login' => 'required|email|unique:users,login',
            'password' => 'required',
            'active' => 'sometimes',
            'employee_id' => 'nullable|exists:employees,id',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'login.required' => 'O Login é obrigatório',
            'login.email' => 'O Login deve ser um endereço de E-mail válido',
            'login.unique' => 'O endereço não pode ser igual a outro já cadastrado',
            'password.required' => 'A Senha é obrigatória',
            'employee_id.exists' => 'O Colaborador deve ser válido',
        ];
    }

    public function toDto(): UserDto
    {
        return new UserDto(
            login: strval($this->validated('login') ?? ''),
            password: strval($this->validated('password') ?? ''),
            active: ($this->validated('active') ?? '') === 'on',
            employeeId: intval($this->validated('employee_id')),
        );
    }
}
