<?php

namespace App\Http\Controllers;

use App\Exports\AccountStatementExport;
use App\Exports\BalanceSheetExport;
use App\Exports\PayrollExport;
use App\Exports\ProductStockExport;
use App\Exports\ProfitLossExport;
use App\Exports\ReceivableExport;
use App\Exports\SalesReportExport;
use App\Exports\TrialBalancExport;
use App\Models\AttendanceEmployee;
use App\Models\AttendanceLog;
use App\Models\BankAccount;
use App\Models\Bill;
use App\Models\BillProduct;
use App\Models\Branch;
use App\Models\ChartOfAccount;
use App\Models\ChartOfAccountSubType;
use App\Models\CreditNote;
use App\Models\DebitNote;
use App\Models\ChartOfAccountType;
use App\Models\ClientDeal;
use App\Models\Customer;
use App\Models\Deal;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Holiday;
use App\Models\Invoice;
use App\Models\InvoiceProduct;
use App\Models\Lead;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\Payment;
use App\Models\PaySlip;
use App\Models\Pipeline;
use App\Models\Pos;
use App\Models\ProductServiceCategory;
use App\Models\Purchase;
use App\Models\Revenue;
use App\Models\Source;
use App\Models\StockReport;
use App\Models\Tax;
use App\Models\User;
use App\Models\UserDeal;
use App\Models\Utility;
use App\Models\Vender;
use App\Models\warehouse;
use App\Models\WarehouseProduct;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DeviceReportController extends Controller
{
    public function monthlyAttendance(Request $request)
    {
        if (\Auth::user()->can('manage report')) {

            $branch = Branch::where('created_by', '=', \Auth::user()->creatorId())->get();
            $department = Department::where('created_by', '=', \Auth::user()->creatorId())->get();

            $data['branch'] = __('All');
            $data['department'] = __('All');

            $employees = Employee::select('id', 'name', 'employee_id');
            if (!empty($request->employee_id) && $request->employee_id[0] != 0) {
                $employees->whereIn('id', $request->employee_id);
            }
            $employees = $employees->where('created_by', \Auth::user()->creatorId());

            if (!empty($request->branch)) {
                $employees->where('branch_id', $request->branch);
                $data['branch'] = !empty(Branch::find($request->branch)) ? Branch::find($request->branch)->name : '';
            }

            if (!empty($request->department)) {
                $employees->where('department_id', $request->department);
                $data['department'] = !empty(Department::find($request->department)) ? Department::find($request->department)->name : '';
            }

            $employees = $employees->get()->pluck('name', 'employee_id');

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
                $attendances['name'] = $employee;

                foreach ($dates as $date) {
                    $dateFormat = $year . '-' . $month . '-' . $date;

                    if ($dateFormat <= date('Y-m-d')) {
                        $employeeAttendance = AttendanceLog::where('employee_id', $id)->where('date', $dateFormat)->first();
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
                            if ($employeeAttendance->overtime > 0) {
                                $overtime = Carbon::parse($employeeAttendance->overtime);
                                // Add hours and minutes from late time
                                $ovetimeHours += $overtime->hour;  // Get hours
                                $overtimeMins += $overtime->minute; // Get minutes
                            }

                            if ($employeeAttendance->early_leaving > 0) {
                                $early_leaving = Carbon::parse($employeeAttendance->early_leaving);
                                // Add hours and minutes from late time
                                $earlyleaveHours += $early_leaving->hour;  // Get hours
                                $earlyleaveMins += $early_leaving->minute; // Get minutes
                            }

                            if ($employeeAttendance->late > 0) {
                                $lateTime = Carbon::parse($employeeAttendance->late);
                                $lateHours += $lateTime->hour;  // Get hours
                                $lateMins += $lateTime->minute; // Get minutes
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
