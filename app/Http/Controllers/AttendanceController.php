<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\Branch;
use App\Models\Holiday;
use App\Models\Meeting;
use App\Services\AttendanceService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Imports\AttendanceImport;
use App\Models\AttendanceEmployee;
use App\Models\Department;
use App\Models\Employee;
use App\Models\IpRestrict;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    protected $attendanceService;

    public function __construct()
    {
        // Initialize AttendanceService with ZKTeco device IP and port
        // $this->attendanceService = new AttendanceService('192.168.68.201', port: 4370);
        $this->attendanceService = new AttendanceService('192.168.68.150');
    }

    public function index(Request $request)
    {
        if (\Auth::user()->can('manage attendance')) {

            $holidays = Holiday::get();
            $user = \Auth::user();

            // $employees = Employee::get()->pluck('name', 'employee_id');
            $employee = Employee::with(['leaves','meetings'])->where('user_id', $user->id)->where('created_by', \Auth::user()->creatorId())->first();


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

                    $start_date = date($year . '-' . $month . '-01');
                    $end_date   = date($year . '-' . $month . '-t');

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

                $employees = Employee::select('id', 'employee_id','name');

                if (!empty($request->branch)) {
                    $employees->where('branch_id', $request->branch);
                }

                if (!empty($request->employee_id)) {

                    $employees->where('employee_id', $request->employee_id);
                }

                if (!empty($request->department)) {
                    $employees->where('department_id', $request->department);
                }

                $employee_ids = $employees->pluck( 'employee_id');


                $attendanceEmployee = AttendanceLog::with(['employee', 'employee.leaves', 'employee.meetings'])->whereIn('employee_id', $employee_ids);

                $holidays = Holiday::get();


                if ($request->type == 'monthly' && !empty($request->month)) {
                    $month = date('m', strtotime($request->month));
                    $year  = date('Y', strtotime($request->month));

                    $start_date = date($year . '-' . $month . '-01');
                    $end_date   = date($year . '-' . $month . '-t');

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
                        'attendance_logs.date',
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


            return view('deviceAttendance.index', compact('attendanceEmployee', 'branch', 'department', 'holidays','employees'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    public function indexDetails(Request $request)
    {
        $employee_reports = DB::table('employees as e')
        ->select(
            'e.employee_id','e.name',
            DB::raw('IFNULL(SUM(DATEDIFF(l.end_date, l.start_date) + 1), 0) AS total_leave_days'),
            DB::raw('IFNULL(SUM(CASE WHEN a.late != "00:00:00" THEN 1 ELSE 0 END), 0) AS total_late_days'),
            DB::raw('IFNULL(SUM(CASE WHEN a.early_leaving != "00:00:00" THEN 1 ELSE 0 END), 0) AS total_early_leave_days'),
            DB::raw('IFNULL(SUM(m.date), 0) AS total_meeting_dates') // Assuming m.date is numeric
        )
        ->leftJoin('leaves as l', 'e.employee_id', '=', 'l.employee_id')
        ->leftJoin('attendance_logs as a', 'e.employee_id', '=', 'a.employee_id')
        ->leftJoin('meetings as m', 'e.id', '=', 'm.employee_id')
        ->groupBy('e.employee_id')
        ->get();


            // dd($employee_reports);


        if (\Auth::user()->can('manage attendance')) {

            $holidays = Holiday::get();
            $user = \Auth::user();

            // $employees = Employee::get()->pluck('name', 'employee_id');
            $employee = Employee::with(['leaves','meetings'])->where('user_id', $user->id)->where('created_by', \Auth::user()->creatorId())->first();


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

                    $start_date = date($year . '-' . $month . '-01');
                    $end_date   = date($year . '-' . $month . '-t');

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

                $employees = Employee::select('id', 'employee_id','name');

                if (!empty($request->branch)) {
                    $employees->where('branch_id', $request->branch);
                }

                if (!empty($request->employee_id)) {

                    $employees->where('employee_id', $request->employee_id);
                }

                if (!empty($request->department)) {
                    $employees->where('department_id', $request->department);
                }

                $employee_ids = $employees->pluck( 'employee_id');


                $attendanceEmployee = AttendanceLog::with(['employee', 'employee.leaves', 'employee.meetings'])->whereIn('employee_id', $employee_ids)
                ->where('late', '!=', "00:00:00")
                ->orWhere('early_leaving', '!=', "00:00:00");

                $holidays = Holiday::get();


                if ($request->type == 'monthly' && !empty($request->month)) {
                    $month = date('m', strtotime($request->month));
                    $year  = date('Y', strtotime($request->month));

                    $start_date = date($year . '-' . $month . '-01');
                    $end_date   = date($year . '-' . $month . '-t');

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
                        'attendance_logs.date',
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


            return view('deviceAttendaceDetails.index', compact('attendanceEmployee', 'branch', 'department', 'holidays','employees','employee_reports'));
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

    public function fetchLogs(Request $request)
    {
        if (\Auth::user()->can('create attendance')) {
            // Fetch logs from the attendance device using the attendance service
            $logs = $this->attendanceService->getAttendanceLogs();

            if ($logs) {
                // Get today's date once
                // $today = Carbon::today();
                $today = Carbon::today();

                // Filter logs for today
                $todayLogs = collect($logs)->filter(function ($log) use ($today) {
                    return Carbon::parse($log['timestamp'])->isSameDay($today);
                });

                // Process and save the logs as necessary
                foreach ($todayLogs ?? [] as $log) {

                    $employee_id = $log['id'];
                    $checkTime = Carbon::parse($log['timestamp']);
                    $eventType = $this->getEventType($checkTime);

                    $startTime = Carbon::parse(Utility::getValByName('company_start_time'));
                    $endTime = Carbon::parse(Utility::getValByName('company_end_time'));
                    $date = $today;
                    $late = $checkTime->gt($startTime) ? gmdate('H:i:s', $checkTime->diffInSeconds($startTime)) : '00:00:00';
                    $earlyLeaving = $checkTime->lt($endTime) ? gmdate('H:i:s', $endTime->diffInSeconds($checkTime)) : '00:00:00';
                    $overtime = $checkTime->gt($endTime) ? gmdate('H:i:s', $checkTime->diffInSeconds($endTime)) : '00:00:00';


                    $existingLog = AttendanceLog::where('employee_id', 1000020)
                        ->whereDate('date', $date)
                        ->first();
                    $eventType = null;


                    if ($existingLog && $existingLog->clock_in === "00:00:00") {
                        // No clock-in yet or first event: treat as clock-in
                        $eventType = 'clock_in';
                    } else if ($checkTime->format('H:i:s') === $existingLog->clock_in) {
                        break;
                    } else if ($existingLog && $existingLog->clock_in != "00:00:00") {
                        // Already clocked in but no clock-out: treat as clock-out
                        $eventType = 'clock_out';
                    }

                    if ($eventType == 'clock_in') {
                        $existingLog->update([
                            'clock_in' => $checkTime,
                            'late' => $late,
                            'status' => 'Present',
                        ]);
                    } else if ($eventType == 'clock_out') {

                        $existingLog->update([
                            'clock_out' => $checkTime,
                            'early_leaving' => $earlyLeaving,
                            'overtime' => $overtime,
                        ]);
                    }
                }
            }

            return back();
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function createDailyAttendance()
    {
        $employees = Employee::all();

        foreach ($employees ?? [] as $employee) {

            // Check if attendance log already exists for today
            $existingLog = AttendanceLog::where('employee_id', $employee->employee_id)
                ->whereDate('date', Carbon::today())
                ->first();

            // If no log exists for today, create a new log
            if (!$existingLog) {

                $employeeAttendance                = new AttendanceLog();
                $employeeAttendance->employee_id   = $employee->employee_id;
                $employeeAttendance->date          = Carbon::today();
                $employeeAttendance->status        = 'Absent'; // Default to Absent
                $employeeAttendance->clock_in      = '00:00:00';
                $employeeAttendance->clock_out     = '00:00:00';
                $employeeAttendance->late          = '00:00:00';
                $employeeAttendance->early_leaving = '00:00:00';
                $employeeAttendance->overtime      = '00:00:00';
                $employeeAttendance->total_rest    = '00:00:00';
                $employeeAttendance->save();
            }
        }
    }


    public function importFile()
    {
        return view('deviceAttendance.import');
    }


    public function getEventType($checkTime)
    {
        if ($this->isMorningTime($checkTime)) {
            return 'check-in';
        } elseif ($this->isEveningTime($checkTime)) {
            return 'check-out';
        }

        return null;
    }


    protected function isMorningTime($time)
    {
        // Define morning hours (e.g., 6 AM to 12 PM)
        return Carbon::parse($time)->between(Carbon::parse('06:00:00'), Carbon::parse('12:00:00'));
    }

    protected function isEveningTime($time)
    {
        // Define evening hours (e.g., 12 PM to 6 PM)
        return Carbon::parse($time)->between(Carbon::parse('12:00:00'), Carbon::parse('18:00:00'));
    }
}
