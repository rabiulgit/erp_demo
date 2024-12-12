<?php

namespace App\Http\Controllers;


use App\Http\Requests\EmployeeCauseRequest;
use App\Http\Services\EmployeeCauseService;
use App\Models\EmployeeCause;
use Illuminate\Support\Facades\Auth;

class EmployeeCauseController extends Controller
{
    public function index()
    {
        if (Auth::user()->type == 'Employee') {
            $EmployeeCauses = EmployeeCause::where('created_by', '=', Auth::id())->get();
        } else {
            $EmployeeCauses = EmployeeCause::orderBy('id', 'desc')->get();
        }

        return view('employee-cause.index', compact('EmployeeCauses'));
    }

    public function create()
    {
        // Allow only employees to access this endpoint
        if (Auth::user()->type == 'Employee' || Auth::user()->type == 'HR') {
            // Allow access for Employees or HR
            return view('employee-cause.create');
        } else {
            // Deny access and return an error
            return response()->json(['error' => __('Only for Employees or HR.')], 401);
        }


    }

    public function store(EmployeeCauseRequest $request)
    {
        // Call the service to create or update the machine
        $response = EmployeeCauseService::createOrUpdateEmployeeCause($request->validated());
        // Redirect back with appropriate message
        return redirect()
            ->route('employee-causes.index')
            ->with($response['status'] ? 'success' : 'error', $response['msg']);
    }

    public function edit($id)
    {
        if (Auth::user()->type == 'Employee' || Auth::user()->type == 'HR') {
            $EmployeeCause = EmployeeCause::findOrFail($id);
            // Return the edit view with the required data
            return view('employee-cause.edit', compact('EmployeeCause'));
        } else {
            // Deny access and return an error
            return response()->json(['error' => __('Only for Employees.')], 401);
        }

    }

    public function update(EmployeeCauseRequest $request, EmployeeCause $employee_cause)
    {
        if (Auth::user()->type == 'Employee' || Auth::user()->type == 'HR') {
            // Call the service to create or update the machine
            $response = EmployeeCauseService::createOrUpdateEmployeeCause($request->validated(), $employee_cause);
            // Redirect back with appropriate message
            return redirect()
                ->route('employee-causes.index')
                ->with($response['status'] ? 'success' : 'error', $response['msg']);
        }
    }

    public function destroy(EmployeeCause $employee_cause)
    {
        // Check if the authenticated user is the creator of the meeting
        if ($employee_cause->created_by !== \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
        // Delete the employee_meeting and redirect with a success message
        $employee_cause->delete();

        return redirect()->route('employee-causes.index')->with('success', __('Employee cause successfully deleted.'));
    }
}
