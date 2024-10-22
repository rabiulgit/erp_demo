<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Holiday;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeviceReportController extends Controller
{
    public function monthlyAttendance(Request $request)
    {
        $user = \Auth::user();

        if ($user->type == 'Employee') {
            if (\Auth::user()->can('manage attendance')) {

                // Fetching branches and departments associated with the current user
                $branch = Branch::where('created_by', '=', \Auth::user()->creatorId())->get();
                $department = Department::where('created_by', '=', \Auth::user()->creatorId())->get();

                $data['branch'] = __('All');
                $data['department'] = __('All');

                // Get the employee details of the logged-in user
                $employee = Employee::where('user_id', $user->id)->where('created_by', \Auth::user()->creatorId())->first();

                if (!$employee) {
                    return redirect()->back()->with('error', __('Employee data not found.'));
                }

                // Process month and year from the request or default to current
                if (!empty($request->month)) {
                    $currentdate = strtotime($request->month);
                    $month = date('m', $currentdate);
                    $year = date('Y', $currentdate);
                    $curMonth = date('M-Y', strtotime($request->month));
                } else {
                    $month = date('m');
                    $year = date('Y');
                    $curMonth = date('M-Y', strtotime($year . '-' . $month));
                }

                // Calculate number of days in the given month
                $num_of_days = date('t', mktime(0, 0, 0, $month, 1, $year));

                for ($i = 1; $i <= $num_of_days; $i++) {
                    $dates[] = str_pad($i, 2, '0', STR_PAD_LEFT);
                }

                // Initialize variables for attendance summary
                $employeesAttendance = [];
                $totalPresent = $totalLeave = $totalEarlyLeave = 0;
                $ovetimeHours = $overtimeMins = $earlyleaveHours = $earlyleaveMins = $lateHours = $lateMins = 0;

                $attendances['name'] = $employee->name;
                $attendances['employee_id'] = $employee->employee_id;

                foreach ($dates as $date) {
                    $dateFormat = $year . '-' . $month . '-' . $date;

                    if ($dateFormat <= date('Y-m-d')) {
                        $employeeAttendance = AttendanceLog::where('employee_id', $employee->employee_id)->where('date', $dateFormat)->first();
                        $dayName = date('l', strtotime($employeeAttendance?->date));
                        $holidays = Holiday::get();
                        $government_holiday = false;

                        foreach ($holidays ?? [] as $holiday) {
                            $start = Carbon::parse($holiday->date);
                            $end = Carbon::parse($holiday->end_date);
                            $check = Carbon::parse($employeeAttendance?->date);

                            if ($check >= $start && $check <= $end) {
                                $attendanceStatus[$date] = 'GH';
                                $government_holiday = true;
                            }
                        }

                        if (!empty($employeeAttendance) && $employeeAttendance->status == 'Present') {
                            $attendanceStatus[$date] = 'P';
                            $totalPresent += 1;

                            // Calculate overtime, early leave, and late hours
                            if ($employeeAttendance->overtime > 0) {
                                $overtime = Carbon::parse($employeeAttendance->overtime);
                                $ovetimeHours += $overtime->hour;
                                $overtimeMins += $overtime->minute;
                            }

                            if ($employeeAttendance->early_leaving > 0) {
                                $early_leaving = Carbon::parse($employeeAttendance->early_leaving);
                                $earlyleaveHours += $early_leaving->hour;
                                $earlyleaveMins += $early_leaving->minute;
                            }

                            if ($employeeAttendance->late > 0) {
                                $lateTime = Carbon::parse($employeeAttendance->late);
                                $lateHours += $lateTime->hour;
                                $lateMins += $lateTime->minute;
                            }
                        } elseif (!empty($employeeAttendance) && $employeeAttendance->status == 'Leave') {
                            $attendanceStatus[$date] = 'A';
                            $totalLeave += 1;
                        } elseif ($dayName == "Friday" || $dayName == "Saturday") {
                            $attendanceStatus[$date] = 'off';
                        } elseif ($government_holiday) {
                            $attendanceStatus[$date] = 'GH';
                        } else {
                            $attendanceStatus[$date] = '';
                        }
                    } else {
                        $attendanceStatus[$date] = '';
                    }
                }

                // Assign attendance status
                $attendances['status'] = $attendanceStatus;
                $employeesAttendance[] = $attendances;

                // Calculate overall totals
                $totalOverTime = $ovetimeHours + ($overtimeMins / 60);
                $totalEarlyleave = $earlyleaveHours + ($earlyleaveMins / 60);
                $totalLate = $lateHours + ($lateMins / 60);

                // Populate data array for view
                $data['totalOvertime'] = $totalOverTime;
                $data['totalEarlyLeave'] = $totalEarlyleave;
                $data['totalLate'] = $totalLate;
                $data['totalPresent'] = $totalPresent;
                $data['totalLeave'] = $totalLeave;
                $data['curMonth'] = $curMonth;

                // dd($employeesAttendance);

                return view('DeviceReport.monthlyAttendance', compact('employeesAttendance', 'branch', 'department', 'dates', 'data'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            if (\Auth::user()->can('manage attendance')) {

                $branch = Branch::where('created_by', '=', \Auth::user()->creatorId())->get();
                $department = Department::where('created_by', '=', \Auth::user()->creatorId())->get();

                $data['branch'] = __('All');
                $data['department'] = __('All');

                $employees = DB::table('employees')->leftJoin('leaves', function ($join) {
                    $join->on('employees.id', '=', 'leaves.employee_id')
                        ->where('leaves.status', '=', 'approved');
                })->leftJoin('meetings', function ($join) {
                    $join->on(DB::raw('JSON_CONTAINS(meetings.employee_id, JSON_QUOTE(CAST(employees.id AS CHAR)))'), '=', DB::raw('1'));
                })
                    ->select('employees.name', 'employees.id', 'employees.created_by', 'employees.employee_id', 'leaves.start_date as l_start_date', 'leaves.end_date as l_end_date', 'meetings.employee_id as emp_ids', 'meetings.date as m_date');


                if (!empty($request->employee_id) && $request->employee_id[0] != 0) {
                    $employees->whereIn('id', $request->employee_id);
                }
                $employees = $employees->where('employees.created_by', \Auth::user()->creatorId());

                if (!empty($request->branch)) {
                    $employees->where('branch_id', $request->branch);
                    $data['branch'] = !empty(Branch::find($request->branch)) ? Branch::find($request->branch)->name : '';
                }

                if (!empty($request->department)) {
                    $employees->where('department_id', $request->department);
                    $data['department'] = !empty(Department::find($request->department)) ? Department::find($request->department)->name : '';
                }

                $employees = $employees->get();

                if (!empty($request->month)) {
                    $currentdate = strtotime($request->month);
                    $month = date('m', $currentdate);
                    $year = date('Y', $currentdate);
                    $curMonth = date('M-Y', strtotime($request->month));
                } else {
                    $month = date('m');
                    $year = date('Y');
                    $curMonth = date('M-Y', strtotime($year . '-' . $month));
                }

                $num_of_days = date('t', mktime(0, 0, 0, $month, 1, $year));

                for ($i = 1; $i <= $num_of_days; $i++) {
                    $dates[] = str_pad($i, 2, '0', STR_PAD_LEFT);
                }

                $employeesAttendance = [];
                $totalPresent = $totalLeave = $totalEarlyLeave = 0;
                $ovetimeHours = $overtimeMins = $earlyleaveHours = $earlyleaveMins = $lateHours = $lateMins = 0;
                foreach ($employees as $id => $employee) {

                    // dd($employee);
                    $attendances['name'] = $employee->name;

                    foreach ($dates as $date) {
                        $dateFormat = $year . '-' . $month . '-' . $date;

                        if ($dateFormat <= date('Y-m-d')) {
                            $employeeAttendance = AttendanceLog::where('employee_id', $employee->employee_id)->where('date', $dateFormat)->first();
                            $dayName = date('l', strtotime($employeeAttendance?->date));
                            $holidays = Holiday::get();
                            $government_holiday = false;
                            $employee_leave = false;
                            $employee_meeting = false;

                            foreach ($holidays ?? [] as $holiday) {
                                $start = Carbon::parse($holiday->date);
                                $end = Carbon::parse($holiday->end_date);
                                $check = Carbon::parse($employeeAttendance?->date);

                                if ($check >= $start && $check <= $end) {
                                    $attendanceStatus[$date] = 'GH';

                                    $government_holiday = true;
                                }
                            }

                            if (!empty($employeeAttendance) && $employee->l_start_date != null) {

                                $start = Carbon::parse($employee->l_start_date);
                                $end = Carbon::parse($employee->l_end_date);
                                $check = Carbon::parse($employeeAttendance?->date);

                                if ($check >= $start && $check <= $end) {
                                    $employee_leave = true;
                                }
                            }

                            if ($employee->emp_ids != null && $employeeAttendance?->date == $employee->m_date) {
                                $employee_meeting = true;
                            }

                            if (!empty($employeeAttendance) && $employee->emp_ids != null && $employee_meeting) {
                                $attendanceStatus[$date] = 'M';
                            } else if (!empty($employeeAttendance) && $employeeAttendance->status == 'Present') {

                                $attendanceStatus[$date] = 'P';
                                $totalPresent += 1;
                                if ($employeeAttendance->overtime > 0) {
                                    $overtime = Carbon::parse($employeeAttendance->overtime);
                                    // Add hours and minutes from late time
                                    $ovetimeHours += $overtime->hour; // Get hours
                                    $overtimeMins += $overtime->minute; // Get minutes
                                }

                                if ($employeeAttendance->early_leaving > 0) {
                                    $early_leaving = Carbon::parse($employeeAttendance->early_leaving);
                                    // Add hours and minutes from late time
                                    $earlyleaveHours += $early_leaving->hour; // Get hours
                                    $earlyleaveMins += $early_leaving->minute; // Get minutes
                                }

                                if ($employeeAttendance->late > 0) {
                                    $lateTime = Carbon::parse($employeeAttendance->late);
                                    $lateHours += $lateTime->hour; // Get hours
                                    $lateMins += $lateTime->minute; // Get minutes
                                }
                            } elseif (!empty($employeeAttendance) && $employee_leave) {
                                $attendanceStatus[$date] = 'A';
                                $totalLeave += 1;
                            } elseif ($dayName == "Friday" || $dayName == "Saturday") {
                                $attendanceStatus[$date] = 'off';
                            } elseif ($government_holiday) {
                                $attendanceStatus[$date] = 'GH';
                            } else {
                                $attendanceStatus[$date] = '';
                            }
                        } else {
                            $attendanceStatus[$date] = '';
                        }
                    }
                    $attendances['status'] = $attendanceStatus;
                    $employeesAttendance[] = $attendances;
                }
                $totalOverTime = $ovetimeHours + ($overtimeMins / 60);
                $totalEarlyleave = $earlyleaveHours + ($earlyleaveMins / 60);
                $totalLate = $lateHours + ($lateMins / 60);

                $data['totalOvertime'] = $totalOverTime;
                $data['totalEarlyLeave'] = $totalEarlyleave;
                $data['totalLate'] = $totalLate;
                $data['totalPresent'] = $totalPresent;
                $data['totalLeave'] = $totalLeave;
                $data['curMonth'] = $curMonth;

                return view('DeviceReport.monthlyAttendance', compact('employeesAttendance', 'branch', 'department', 'dates', 'data'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
    }

    public function getdepartment(Request $request)
    {
        if ($request->branch_id == 0) {
            $departments = Department::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id')->toArray();
        } else {
            $departments = Department::where('created_by', '=', \Auth::user()->creatorId())->where('branch_id', $request->branch_id)->get()->pluck('name', 'id')->toArray();
        }

        return response()->json($departments);
    }

    public function getemployee(Request $request)
    {
        if (!$request->department_id) {
            $employees = Employee::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id')->toArray();
        } else {
            $employees = Employee::where('created_by', '=', \Auth::user()->creatorId())->where('department_id', $request->department_id)->get()->pluck('name', 'id')->toArray();
        }
        return response()->json($employees);
    }
}
