<?php

namespace App\Http\Controllers;

use App\Http\Requests\SingleMeetingRequest;
use App\Http\Services\SingleMeetingService;
use App\Models\Branch;
use App\Models\Meeting;
use App\Models\SingleMeeting;
use Illuminate\Support\Facades\Auth;

class SingleMeetingController extends Controller
{
    public function index()
    {
        // if (Auth::user()->can('manage meeting')) {

            if (@Auth::user()?->type == 'Employee') {
                $meetings = SingleMeeting::with(['branch','employee'])->where('created_by', '=', Auth::id())->get();
            } else {
                $meetings = SingleMeeting::with(['branch','employee'])->orderBy('id', 'desc')->get();
            }

            // dd($meetings);

            return view('single-meeting.index', compact('meetings'));
        
        // else {
        //     return redirect()->back()->with('error', __('Permission denied.'));
        // }
    }

    public function create()
    {
        // Check if the user has permission to create meetings
        if (!\Auth::user()->can('create meeting')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
        // Allow only employees to access this endpoint
        if (\Auth::user()->type !== 'Employee') {
            return response()->json(['error' => __('Only for Employees.')], 401);
        }
        $branch = Branch::get();

        // Return the create view with required data
        return view('single-meeting.create', compact('branch'));
    }


    public function store(SingleMeetingRequest $request)
    {
        if (\Auth::user()->can('create meeting')) {

            // Call the service to create or update the machine
            $response = SingleMeetingService::createOrUpdateMeeting($request->validated());
            // Redirect back with appropriate message
            return redirect()
                ->route('employee-meetings.index')
                ->with($response['status'] ? 'success' : 'error', $response['msg']);
        }
    }

    public function edit($meeting)
    {
        // Check if the user has permission to create a meeting
        if (!\Auth::user()->can('create meeting')) {
            return response()->json(['error' => __('Permission denied.')], 401);
        }

        // Fetch the single meeting with its related branch
        $singleMeeting = SingleMeeting::with('branch')->findOrFail($meeting);

        // Allow only employees to access this endpoint
        if (Auth::user()->type !== 'Employee') {
            return response()->json(['error' => __('Only for Employees.')], 401);
        }

        // Fetch branches created by the current user's creator ID and settings
        $branch = Branch::where('created_by', '=', \Auth::user()->creatorId())->get();

        // Return the edit view with the required data
        return view('single-meeting.edit', compact('branch', 'singleMeeting'));
    }

    public function update(SingleMeetingRequest $request, SingleMeeting $employee_meeting)
    {
        if (\Auth::user()->can('create meeting')) {
            if (Auth::user()->type == 'Employee') {
                // Call the service to create or update the machine
                $response = SingleMeetingService::createOrUpdateMeeting($request->validated(), $employee_meeting);
                // Redirect back with appropriate message
                return redirect()
                    ->route('employee-meetings.index')
                    ->with($response['status'] ? 'success' : 'error', $response['msg']);
            }
        }
    }

    public function destroy(SingleMeeting $employee_meeting)
    {

        // Check if the user has permission to delete meetings
        if (!\Auth::user()->can('delete meeting')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        // Check if the authenticated user is the creator of the meeting
        if ($employee_meeting->created_by !== \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        // Delete the employee_meeting and redirect with a success message
        $employee_meeting->delete();

        return redirect()->route('employee-meetings.index')->with('success', __('Meeting successfully deleted.'));
    }
}
