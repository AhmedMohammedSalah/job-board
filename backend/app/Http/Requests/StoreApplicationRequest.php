<?php

namespace App\Http\Requests;

use App\Enums\ApplicationStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreApplicationRequest extends FormRequest
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
            'job_id' => 'required|exists:jobs,id',
            'candidate_id' => 'required|exists:candidates,id',
            'resume_path' => 'required|file|mimes:pdf,doc,docx|max:2048',
            'cover_letter' => 'required|string|max:255',
            'status' => [new Enum(ApplicationStatus::class)],
        ];
    }
}