<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DoctorRequest extends FormRequest
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
        $id = $this->route('doctor');
        return [
            'name' => 'required|string|unique:doctors,name,' . $id,
            'photo' => $this->isMethod('post') ? 'required|image|max:2048' : 'sometimes|image|max:2048',
            'about' => 'required|string',
            'yoe' => 'required|integer|min:0',
            'specialist_id' => 'required|exists:specialists,id',
            'hospital_id' => 'required|exists:hospitals,id',
            'gender' => 'required|in:Male,Female',

        ];
    }
}
