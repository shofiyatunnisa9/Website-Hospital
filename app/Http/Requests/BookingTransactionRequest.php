<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookingTransactionRequest extends FormRequest
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
            'doctor_id' => ['required', 'exists:doctors,id'],

            'started_at' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $date = Carbon::parse($value)->startOfDay();
                    $min = now()->addDay()->startOfDay();
                    $max = now()->addDays(3)->endOfDay();

                    if ($date->lt($min) || $date->gt($max)) {
                        $fail('Tanggal konsultasi hanya bileh dipilih antara H+1 sampai H+3 dari hari ini.');
                    }
                }
            ],
            'time_at' => [
                'required',
                'date_format:H:i',
                Rule::in(['10:30', '11:30', '13:30', '14:30', '15:30', '16:30'])
            ],

            'proof' => 'required|image|max:2048',
        ];
    }
}
