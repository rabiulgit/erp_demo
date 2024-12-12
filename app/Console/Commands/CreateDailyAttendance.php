<?php

namespace App\Console\Commands;

use App\Models\AttendanceLog;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Log;

class CreateDailyAttendance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
   public function handle()
   {
    $employees = Employee::active()->all();

    foreach ($employees as $employee) {
        try {
            // Log the employee being processed
            Log::info("Processing attendance for employee ID: {$employee->employee_id}");

            // Check if attendance log already exists for today
            $existingLog = AttendanceLog::where('employee_id', $employee->employee_id)
                ->whereDate('date', Carbon::today())
                ->first();

            if (!$existingLog) {
                // Create new log if not found
                $employeeAttendance = new AttendanceLog();
                $employeeAttendance->employee_id = $employee->employee_id;
                $employeeAttendance->date = date("Y-m-d");
                $employeeAttendance->status = 'Absent';
                $employeeAttendance->clock_in = '00:00:00';
                $employeeAttendance->clock_out = '00:00:00';
                $employeeAttendance->late = '00:00:00';
                $employeeAttendance->early_leaving = '00:00:00';
                $employeeAttendance->overtime = '00:00:00';
                $employeeAttendance->total_rest = '00:00:00';
                $employeeAttendance->save();

                // Log success
                Log::info("Attendance record created for employee ID: {$employee->employee_id}");
            } else {
                Log::info("Attendance record already exists for employee ID: {$employee->employee_id}");
            }

        } catch (\Exception $e) {
            Log::error("Error creating attendance for employee ID: {$employee->employee_id}. Error: {$e->getMessage()}");
        }
    }

    $this->info('Attendance records processed.');
}
}
