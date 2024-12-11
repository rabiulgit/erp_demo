<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeCause;
use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeviceReportController extends Controller
{
    public function monthlyAttendance(Request $request)
    {
        $user = \Auth::user();

        if ($user->type == 'Employee') {
            if ($user->can('manage attendance')) {

                // Fetching branches and departments associated with the current user
                $branch = Branch::where('created_by', '=', $user->creatorId())->get();
                $department = Department::where('created_by', '=', $user->creatorId())->get();

                $data['branch'] = __('All');
                $data['department'] = __('All');

                // Get the employee details of the logged-in user
                $employee = Employee::with(['leaves', 'meetings'])->where('user_id', $user->id)->where('created_by', $user->creatorId())->first();

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
                $totalPresent = $totalAbsent = $totalLeave = $totalEarlyLeave =  $total_meetings = $earlyLeaveDays = $totalOverTimeDays =  0;
                $ovetimeHours = $overtimeMins = $earlyleaveHours = $earlyleaveMins = $lateHours = $lateMins = 0;
                $lateDays = 0;

                $attendances['name'] = $employee->name;
                $attendances['employee_id'] = $employee->employee_id;
                foreach ($dates as $date) {
                    $dateFormat = $year . '-' . $month . '-' . $date;

                    if ($dateFormat <= date('Y-m-d')) {
                        $employeeAttendance = AttendanceLog::where('employee_id', $employee->employee_id)->where('date', $dateFormat)->first();
                        $dayName = date('l', strtotime($employeeAttendance?->date));
                        $holidays = Holiday::get();
                        $employee_meeting = false;
                        $government_holiday = false;
                        $employee_leave = false;

                        foreach ($holidays ?? [] as $holiday) {
                            $start = Carbon::parse($holiday->date);
                            $end = Carbon::parse($holiday->end_date);
                            $check = Carbon::parse($employeeAttendance?->date);

                            if ($check >= $start && $check <= $end) {
                                $attendanceStatus[$date] = 'GH';

                                $government_holiday = true;
                            }
                        }

                        $check = Carbon::parse($employeeAttendance?->date);

                        if (!empty($employeeAttendance) && !$employee->leaves->isEmpty()) {
                            foreach ($employee->leaves as $leave) {
                                $start = Carbon::parse($leave->start_date);
                                $end = Carbon::parse($leave->end_date);

                                if ($check->between($start, $end)) {
                                    $employee_leave = true;
                                    break; // Exit loop if a leave period is found
                                }
                            }
                        }
                        if ($employeeAttendance &&  !$employee->meetings->isEmpty()) {
                            $meetingDates = $employee->meetings->pluck('date')->toArray();
                            if (in_array($employeeAttendance?->date, $meetingDates)) {
                                $employee_meeting = true;
                                $total_meetings += 1;
                            }
                        }
                        if ($employee_meeting && $employeeAttendance->status == 'Absent') {
                            $attendanceStatus[$date] = 'AM';
                        } else if (!$employee_meeting && $employeeAttendance->status == 'Present' && $employeeAttendance->late === "00:00:00" && $employeeAttendance->early_leaving === "00:00:00") {
                            $attendanceStatus[$date] = 'P';
                            $totalPresent += 1;
                        } else if ($employeeAttendance->status == 'Present' && $employeeAttendance->late !== "00:00:00" && $employeeAttendance->early_leaving === "00:00:00") {
                            $attendanceStatus[$date] = 'PL';
                            $totalPresent += 1;
                        } else if ($employeeAttendance->status == 'Present' && $employeeAttendance->late === "00:00:00" && $employeeAttendance->early_leaving !== "00:00:00") {
                            $attendanceStatus[$date] = 'PEL';
                            $totalPresent += 1;
                        } else if ($employee_meeting && $employeeAttendance->status == 'Present') {
                            $attendanceStatus[$date] = 'PM';
                            $totalPresent += 1;
                        } elseif ($employeeAttendance->status == 'Present' && $employeeAttendance->late !== "00:00:00" && $employeeAttendance->early_leaving != "00:00:00") {
                            $attendanceStatus[$date] = 'PLEL';
                        } elseif ($employee_leave) {
                            $attendanceStatus[$date] = 'A';
                            $totalLeave += 1;
                        } elseif ($government_holiday) {
                            $attendanceStatus[$date] = 'GH';
                        } elseif ($dayName == "Friday" || $dayName == "Saturday") {
                            $attendanceStatus[$date] = 'off';
                        } elseif ($employeeAttendance->status == 'Absent') {
                            $attendanceStatus[$date] = 'AA';
                            $totalAbsent += 1;
                        } else {
                            $attendanceStatus[$date] = '';
                        }

                        if (!empty($employeeAttendance) && $employeeAttendance?->date) {

                            if ($employeeAttendance->status == 'Present') {

                                if ($employeeAttendance->overtime != "00:00:00") {
                                    $overtime = Carbon::parse($employeeAttendance->overtime);
                                    // Add hours and minutes from late time
                                    $ovetimeHours += $overtime->hour; // Get hours
                                    $overtimeMins += $overtime->minute; // Get minutes
                                    $totalOverTimeDays += 1;
                                }

                                if ($employeeAttendance->early_leaving != "00:00:00") {
                                    $early_leaving = Carbon::parse($employeeAttendance->early_leaving);
                                    // Add hours and minutes from late time
                                    $earlyleaveHours += $early_leaving->hour; // Get hours
                                    $earlyleaveMins += $early_leaving->minute; // Get minutes
                                    $earlyLeaveDays += 1;
                                }

                                if ($employeeAttendance->late != "00:00:00") {
                                    $lateTime = Carbon::parse($employeeAttendance->late);
                                    $lateHours += $lateTime->hour; // Get hours
                                    $lateMins += $lateTime->minute; // Get minutes
                                    $lateDays += 1;
                                }
                            }
                        }
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
                $data['totalLateDays'] = $lateDays;
                $data['earlyLeaveDays'] = $earlyLeaveDays;
                $data['totalOverTimeDays'] = $totalOverTimeDays;
                $data['totalMeetings'] = $total_meetings;
                $data['employee_causes'] = EmployeeCause::with('employee')->where('created_by', Auth()->id())->get();

                return view('DeviceReport.monthlyAttendance', compact('employeesAttendance', 'branch', 'department', 'dates', 'data'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {

            // report start from here
            if (\Auth::user()->can('manage attendance')) {
                $branch = Branch::where('created_by', '=', Auth::user()->creatorId())->get();
                $department = Department::where('created_by', '=', Auth::user()->creatorId())->get();

                // initial branch and department
                $data['branch'] = __('All');
                $data['department'] = __('All');
                // Employee base query
                $employees = Employee::active()->with(['leaves', 'meetings']);


                // Employees attendance filter base on employee selection
                if (!empty($request->employee_id) && $request->employee_id[0] != 0) {
                    $employees->whereIn('id', $request->employee_id);
                    $data['employee_causes'] = EmployeeCause::with('employee')->whereIn('employee_id', $request->employee_id)->get();
                } else {
                    $data['employee_causes'] = [];
                }

                // Employee filtered base on creator_id
                $employees = $employees->where('employees.created_by', \Auth::user()->creatorId());

                // Filter base on branch selection
                if (!empty($request->branch)) {
                    $employees->where('branch_id', $request->branch);
                    $data['branch'] = !empty(Branch::find($request->branch)) ? Branch::find($request->branch)->name : '';
                }

                // Filter base on branch selection
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

                // number of days in the month
                $num_of_days = date('t', mktime(0, 0, 0, $month, 1, $year));

                // build all dates in array
                for ($i = 1; $i <= $num_of_days; $i++) {
                    $dates[] = str_pad($i, 2, '0', STR_PAD_LEFT);
                }

                // defined initial data
                $employeesAttendance = [];
                $totalPresent = $totalLeave = $totalEarlyLeave =  $total_meetings = $earlyLeaveDays = $totalOverTimeDays = $totalAbsent =  0;
                $ovetimeHours = $overtimeMins = $earlyleaveHours = $earlyleaveMins = $lateHours = $lateMins = 0;
                $lateDays = 0;



                foreach ($employees as $id => $employee) {

                    $attendances['name'] = $employee->name;

                    foreach ($dates as $date) {

                        $dateFormat = $year . '-' . $month . '-' . $date;


                        if ($dateFormat <= date('Y-m-d')) {

                            $employeeAttendance = AttendanceLog::where('employee_id', $employee->employee_id)->where('date', $dateFormat)->first();
                            $dayName = date('l', strtotime($employeeAttendance?->date));
                            $holidays = Holiday::get();
                            $employee_meeting = false;
                            $government_holiday = false;
                            $employee_leave = false;



                            if (!empty($employeeAttendance)) {
                                foreach ($holidays ?? [] as $holiday) {
                                    $start = Carbon::parse($holiday->date);
                                    $end = Carbon::parse($holiday->end_date);
                                    $check = Carbon::parse($employeeAttendance?->date);

                                    if ($check >= $start && $check <= $end) {
                                        $attendanceStatus[$date] = 'GH';
                                        $government_holiday = true;
                                    }
                                }

                                $check = Carbon::parse($employeeAttendance?->date);

                                if (!$employee->leaves->isEmpty()) {

                                    foreach ($employee->leaves as $leave) {
                                        $start = Carbon::parse($leave->start_date);
                                        $end = Carbon::parse($leave->end_date);

                                        if ($check->between($start, $end)) {
                                            $employee_leave = true;
                                            break; // Exit loop if a leave period is found
                                        }
                                    }
                                }

                                if (!$employee->meetings->isEmpty()) {
                                    $meetingDates = $employee->meetings->pluck('date')->toArray();
                                    if (in_array($employeeAttendance?->date, $meetingDates)) {

                                        $employee_meeting = true;
                                        $total_meetings += 1;
                                    }
                                }
                            }

                            if (!empty($employeeAttendance) && $employeeAttendance?->date) {

                                if ($employeeAttendance->late !== "00:00:00") {

                                    $lateTime = Carbon::parse($employeeAttendance->late);
                                    $lateHours += $lateTime->hour; // Get hours
                                    $lateMins += $lateTime->minute; // Get minutes
                                    $lateDays += 1;
                                }
                                if ($employeeAttendance->early_leaving != "00:00:00") {
                                    $early_leaving = Carbon::parse($employeeAttendance->early_leaving);
                                    // Add hours and minutes from late time
                                    $earlyleaveHours += $early_leaving->hour; // Get hours
                                    $earlyleaveMins += $early_leaving->minute; // Get minutes
                                    $earlyLeaveDays += 1;
                                }



                                // new code start

                                if ($employee_meeting) {

                                    if ($employeeAttendance->status == 'Absent') {
                                        $attendanceStatus[$date] = 'AM';
                                    }

                                    if ($employeeAttendance->status == 'Present' && $employeeAttendance->late === "00:00:00" && $employeeAttendance->early_leaving === "00:00:00") {
                                        $attendanceStatus[$date] = 'PM';
                                        $totalPresent += 1;
                                    } else if ($employeeAttendance->status == 'Present' && $employeeAttendance->late !== "00:00:00" && $employeeAttendance->early_leaving === "00:00:00") {
                                        $attendanceStatus[$date] = 'PLM';
                                        $totalPresent += 1;
                                    } else if ($employeeAttendance->status == 'Present' && $employeeAttendance->late === "00:00:00" && $employeeAttendance->early_leaving !== "00:00:00") {
                                        $attendanceStatus[$date] = 'PELM';
                                        $totalPresent += 1;
                                    } elseif ($employeeAttendance->status == 'Present' && $employeeAttendance->late !== "00:00:00" && $employeeAttendance->early_leaving != "00:00:00") {
                                        $attendanceStatus[$date] = 'PLELM';
                                        $totalPresent += 1;
                                    }
                                    else if ($employee_leave) {
                                        $attendanceStatus[$date] = 'A';
                                        $totalLeave += 1;
                                    } elseif ($government_holiday) {
                                        $attendanceStatus[$date] = 'GH';
                                    } elseif ($dayName == "Friday" || $dayName == "Saturday") {
                                        $attendanceStatus[$date] = 'off';
                                    }
                                      elseif ($employeeAttendance->status == 'Absent') {
                                        $attendanceStatus[$date] = 'AA';
                                        $totalAbsent += 1;
                                    }
                                    else {
                                        $attendanceStatus[$date] = '';
                                    }
                                } else {

                                    if ($employeeAttendance->status == 'Absent') {
                                        $attendanceStatus[$date] = 'A';
                                        $totalAbsent += 1;
                                    }

                                    if ($employeeAttendance->status == 'Present' && $employeeAttendance->late === "00:00:00" && $employeeAttendance->early_leaving === "00:00:00") {
                                        $attendanceStatus[$date] = 'P';
                                        $totalPresent += 1;
                                    } else if ($employeeAttendance->status == 'Present' && $employeeAttendance->late !== "00:00:00" && $employeeAttendance->early_leaving === "00:00:00") {
                                        $attendanceStatus[$date] = 'PLM';
                                        $totalPresent += 1;
                                    } else if ($employeeAttendance->status == 'Present' && $employeeAttendance->late === "00:00:00" && $employeeAttendance->early_leaving !== "00:00:00") {
                                        $attendanceStatus[$date] = 'PEL';
                                        $totalPresent += 1;
                                    } elseif ($employeeAttendance->status == 'Present' && $employeeAttendance->late !== "00:00:00" && $employeeAttendance->early_leaving != "00:00:00") {
                                        $attendanceStatus[$date] = 'PLEL';
                                        $totalPresent += 1;
                                    }
                                    else if ($employee_leave) {
                                        $attendanceStatus[$date] = 'A';
                                        $totalLeave += 1;
                                    } elseif ($government_holiday) {
                                        $attendanceStatus[$date] = 'GH';
                                    } elseif ($dayName == "Friday" || $dayName == "Saturday") {
                                        $attendanceStatus[$date] = 'off';
                                    }
                                      elseif ($employeeAttendance->status == 'Absent') {
                                        $attendanceStatus[$date] = 'AA';
                                        $totalAbsent += 1;
                                    }
                                    else {
                                        $attendanceStatus[$date] = '';
                                    }
                                }















                                // if (!$employee_meeting && $employeeAttendance->status == 'Present' && $employeeAttendance->late === "00:00:00" && $employeeAttendance->early_leaving === "00:00:00") {
                                //     $attendanceStatus[$date] = 'P';
                                //     $totalPresent += 1;
                                // } else if ($employee_meeting && $employeeAttendance->status == 'Present' && $employeeAttendance->late === "00:00:00" && $employeeAttendance->early_leaving === "00:00:00") {
                                //     $attendanceStatus[$date] = 'PM';
                                // } else if ($employeeAttendance->status == 'Present' && $employeeAttendance->late !== "00:00:00" && $employeeAttendance->early_leaving === "00:00:00") {
                                //     $attendanceStatus[$date] = 'PL';
                                //     $totalPresent += 1;
                                // } else if (!$employee_meeting && $employeeAttendance->status == 'Present' && $employeeAttendance->late !== "00:00:00" && $employeeAttendance->early_leaving === "00:00:00") {
                                //     $attendanceStatus[$date] = 'PL';
                                //     $totalPresent += 1;
                                // } else if ($employee_meeting && $employeeAttendance->status == 'Present' && $employeeAttendance->late !== "00:00:00" && $employeeAttendance->early_leaving === "00:00:00") {
                                //     $attendanceStatus[$date] = 'PLM';
                                //     $totalPresent += 1;
                                // } else if ($employeeAttendance->status == 'Present' && $employeeAttendance->late === "00:00:00" && $employeeAttendance->early_leaving !== "00:00:00") {
                                //     $attendanceStatus[$date] = 'PEL';
                                //     $totalPresent += 1;
                                // } else if ($employee_meeting && $employeeAttendance->status == 'Present') {
                                //     $attendanceStatus[$date] = 'PM';
                                //     $totalPresent += 1;
                                // } elseif ($employeeAttendance->status == 'Present' && $employeeAttendance->late !== "00:00:00" && $employeeAttendance->early_leaving != "00:00:00") {
                                //     $attendanceStatus[$date] = 'PLEL';
                                // } elseif ($employee_leave) {
                                //     $attendanceStatus[$date] = 'A';
                                //     $totalLeave += 1;
                                // } elseif ($government_holiday) {
                                //     $attendanceStatus[$date] = 'GH';
                                // } elseif ($dayName == "Friday" || $dayName == "Saturday") {
                                //     $attendanceStatus[$date] = 'off';
                                // } elseif ($employeeAttendance->status == 'Absent') {
                                //     $attendanceStatus[$date] = 'AA';
                                //     $totalAbsent += 1;
                                // } else {
                                //     $attendanceStatus[$date] = '';
                                // }
                            // } else {
                            //     $attendanceStatus[$date] = '';
                            // }

                            //  new code end




















                            //     if ($employee_meeting && $employeeAttendance->status == 'Absent') {
                            //         $attendanceStatus[$date] = 'AM';
                            //     }
                            //     else if (!$employee_meeting && $employeeAttendance->status == 'Present' && $employeeAttendance->late === "00:00:00" && $employeeAttendance->early_leaving === "00:00:00") {
                            //         $attendanceStatus[$date] = 'P';
                            //         $totalPresent += 1;

                            //     }

                            //     else if ($employee_meeting && $employeeAttendance->status == 'Present' && $employeeAttendance->late === "00:00:00" && $employeeAttendance->early_leaving === "00:00:00") {
                            //         $attendanceStatus[$date] = 'PM';
                            //     }


                            //     else if ($employeeAttendance->status == 'Present' && $employeeAttendance->late !== "00:00:00" && $employeeAttendance->early_leaving === "00:00:00") {
                            //         $attendanceStatus[$date] = 'PL';
                            //         $totalPresent += 1;
                            //     }

                            //     else if (!$employee_meeting && $employeeAttendance->status == 'Present' && $employeeAttendance->late !== "00:00:00" && $employeeAttendance->early_leaving === "00:00:00") {
                            //         $attendanceStatus[$date] = 'PL';
                            //         $totalPresent += 1;
                            //     }
                            //     else if ($employee_meeting && $employeeAttendance->status == 'Present' && $employeeAttendance->late !== "00:00:00" && $employeeAttendance->early_leaving === "00:00:00") {
                            //         $attendanceStatus[$date] = 'PLM';
                            //         $totalPresent += 1;
                            //     }
                            //     else if ($employeeAttendance->status == 'Present' && $employeeAttendance->late === "00:00:00" && $employeeAttendance->early_leaving !== "00:00:00") {
                            //         $attendanceStatus[$date] = 'PEL';
                            //         $totalPresent += 1;

                            //     }
                            //      else if ($employee_meeting && $employeeAttendance->status == 'Present') {
                            //         $attendanceStatus[$date] = 'PM';
                            //         $totalPresent += 1;

                            //     }

                            //     elseif ($employeeAttendance->status == 'Present' && $employeeAttendance->late !== "00:00:00" && $employeeAttendance->early_leaving != "00:00:00") {
                            //         $attendanceStatus[$date] = 'PLEL';

                            //     }
                            //     elseif ($employee_leave) {
                            //         $attendanceStatus[$date] = 'A';
                            //         $totalLeave += 1;
                            //     } elseif ($government_holiday) {
                            //         $attendanceStatus[$date] = 'GH';
                            //     } elseif ($dayName == "Friday" || $dayName == "Saturday") {
                            //         $attendanceStatus[$date] = 'off';
                            //     }
                            //  elseif ($employeeAttendance->status == 'Absent') {
                            //     $attendanceStatus[$date] = 'AA';
                            //     $totalAbsent += 1;
                            //     } else {
                            //         $attendanceStatus[$date] = '';
                            //     }
                            // } else {
                            //     $attendanceStatus[$date] = '';
                            // }
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
                $data['totalMeetings'] = $total_meetings;
                $data['totalLateDays'] = $lateDays;
                $data['earlyLeaveDays'] = $earlyLeaveDays;
                $data['totalAbsentDays'] = $totalAbsent;
                $data['totalOverTimeDays'] = $totalOverTimeDays;
                $data['curMonth'] = $curMonth;

                // dd($employeesAttendance);

                return view('DeviceReport.monthlyAttendance', compact('employeesAttendance', 'branch', 'department', 'dates', 'data'));
            }
        }
             else {
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
