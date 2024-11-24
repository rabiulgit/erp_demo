<?php

namespace App\Http\Requests;

use App\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EmployeeCauseRequest extends FormRequest
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
            'employee_id' => 'required',
            'type'        => 'required',
            'date'        => 'required',
            'time'        => 'required',
            'created_by'  => 'nullable',
            'note'        => 'nullable',
        ];

    }

    public function prepareForValidation()
    {
        if (Auth::user()->type == 'Employee') {
            $current_employee   = Employee::where('user_id', '=', \Auth::user()->id)->first();
            $employee_id        = $current_employee->id;
        }
        // Automatically add `created_by` and `updated_by` to the request
        $this->merge([
            'employee_id' => $employee_id ?? 0,
            'created_by'  => Auth::check()? Auth::id() : null,
        ]);
    }
}
