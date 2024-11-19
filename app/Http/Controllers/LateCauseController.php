<?php

namespace App\Http\Controllers;


use App\Http\Requests\LateCauseRequest;
use App\Http\Services\LateCauseService;
use App\Models\LateCause;
use Illuminate\Support\Facades\Auth;

class LateCauseController extends Controller
{
    public function index()
    {
        if (@Auth::user()?->type == 'Employee') {
            // $lateCauses = LateCause::where('created_by', '=', Auth::id())->get();
            $lateCauses = LateCause::get();
        } else {
            $lateCauses = LateCause::orderBy('id', 'desc')->get();
        }

        return view('late-cause.index', compact('lateCauses'));
    }

    public function create()
    {
        // Allow only employees to access this endpoint
        if (\Auth::user()->type !== 'Employee') {
            return response()->json(['error' => __('Only for Employees.')], 401);
        }
        // Return the create view with required data
        return view('late-cause.create');
    }

    public function store(LateCauseRequest $request)
    {
        // Call the service to create or update the machine
        $response = LateCauseService::createOrUpdateLateCause($request->validated());
        // Redirect back with appropriate message
        return redirect()
            ->route('employee-late-causes.index')
            ->with($response['status'] ? 'success' : 'error', $response['msg']);
    }

    public function edit($id)
    {
         // Allow only employees to access this endpoint
         if (Auth::user()->type !== 'Employee') {
            return response()->json(['error' => __('Only for Employees.')], 401);
        }
        // Fetch the single meeting with its related branch
        $lateCause = LateCause::findOrFail($id);
        // Return the edit view with the required data
        return view('late-cause.edit', compact('lateCause'));
    }

    public function update(LateCauseRequest $request, lateCause $employee_late_cause)
    {
        // Check if the authenticated user is the creator of the meeting
        if ($employee_late_cause->created_by !== \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
        if (Auth::user()->type == 'Employee') {
            // Call the service to create or update the machine
            $response = LateCauseService::createOrUpdateLateCause($request->validated(), $employee_late_cause);
            // Redirect back with appropriate message
            return redirect()
                ->route('employee-late-causes.index')
                ->with($response['status'] ? 'success' : 'error', $response['msg']);
        }
    }

    public function destroy(lateCause $employee_late_cause)
    {
        // Check if the authenticated user is the creator of the meeting
        if ($employee_late_cause->created_by !== \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
        // Delete the employee_meeting and redirect with a success message
        $employee_late_cause->delete();

        return redirect()->route('employee-lateCauses.index')->with('success', __('Meeting successfully deleted.'));
    }
}
