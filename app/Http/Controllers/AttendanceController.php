<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\Branch;
use App\Models\Holiday;
use Illuminate\Http\Request;
use App\Models\AttendanceEmployee;
use App\Models\Department;
use App\Models\Employee;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        if (\Auth::user()->can('manage attendance')) {

            $holidays = Holiday::get();
            $user = \Auth::user();

            // $employees = Employee::get()->pluck('name', 'employee_id');
            $employee = Employee::with(['leaves', 'meetings'])->where('user_id', $user->id)->where('created_by', \Auth::user()->creatorId())->first();


            $branch = Branch::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $branch->prepend('Select Branch', '');

            $department = Department::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $department->prepend('Select Department', '');

            if (\Auth::user()->type != 'client' && \Auth::user()->type != 'company') {


                $emp = !empty(\Auth::user()->employee) ? \Auth::user()->employee->id : 0;

                $attendanceEmployee = AttendanceLog::where('employee_id', $employee->employee_id);

                if ($request->type == 'monthly' && !empty($request->month)) {
                    $month = date('m', strtotime($request->month));
                    $year  = date('Y', strtotime($request->month));

                    $start_date = date('Y-m-01', strtotime("$year-$month-01")); // First day of the month
                    $end_date   = date('Y-m-t', strtotime("$year-$month-01"));

                    $attendanceEmployee->whereBetween(
                        'date',
                        [
                            $start_date,
                            $end_date,
                        ]
                    );
                } elseif ($request->type == 'daily' && !empty($request->date)) {
                    $attendanceEmployee->where('date', $request->date);
                } else {

                    $month      = date('m');
                    $year       = date('Y');
                    $start_date = date($year . '-' . $month . '-01');
                    $end_date   = date($year . '-' . $month . '-t');

                    $attendanceEmployee->whereBetween(
                        'date',
                        [
                            $start_date,
                            $end_date,
                        ]
                    );
                }
                $attendanceEmployee = $attendanceEmployee->get();
            } else {


                $employees = Employee::select('id', 'employee_id', 'name');

                if (!empty($request->branch)) {
                    $employees->where('branch_id', $request->branch);
                }

                if (!empty($request->employee_id)) {

                    $employees->where('employee_id', $request->employee_id);
                }

                if (!empty($request->department)) {
                    $employees->where('department_id', $request->department);
                }

                $employee_ids = $employees->pluck('employee_id');


                $attendanceEmployee = AttendanceLog::with(['employee', 'employee.leaves', 'employee.meetings'])->whereIn('employee_id', $employee_ids);

                $holidays = Holiday::get();


                if ($request->type == 'monthly' && !empty($request->month)) {

                    $month = date('m', strtotime($request->month));
                    $year  = date('Y', strtotime($request->month));

                    $start_date = date('Y-m-01', strtotime("$year-$month-01")); // First day of the month
                    $end_date   = date('Y-m-t', strtotime("$year-$month-01"));  // Last day of the month

                    $attendanceEmployee->whereBetween(
                        'date',
                        [
                            $start_date,
                            $end_date,
                        ]
                    );
                } else {

                    $month      = date('m');
                    $year       = date('Y');
                    $start_date = date($year . '-' . $month . '-01');
                    $end_date   = date($year . '-' . $month . '-t');

                    $attendanceEmployee->whereBetween(
                        'date',
                        [
                            $start_date,
                            $end_date,
                        ]
                    );
                }

                if ($request->type == 'daily' && !empty($request->date)) {

                    $attendanceEmployee->where('date', $request->date);
                }

                $attendanceEmployee = $attendanceEmployee
                    ->orderBy('attendance_logs.date', 'desc')
                    ->orderBy('id', 'asc')
                    ->get();
            }

            $employees = Employee::pluck('name', 'employee_id');


            return view('deviceAttendance.index', compact('attendanceEmployee', 'branch', 'department', 'holidays', 'employees'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function edit($id)
    {
        if (\Auth::user()->can('edit attendance')) {
            $attendanceEmployee = AttendanceEmployee::where('id', $id)->first();
            $employees          = Employee::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');

            return view('attendance.edit', compact('attendanceEmployee', 'employees'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function importFile()
    {
        return view('deviceAttendance.import');
    }
}
