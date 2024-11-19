<?php

namespace App\Http\Services;

use App\Models\LateCause;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LateCauseService
{
    /**
     * Create or update a employee_late_cause record and its details.
     *
     * @param array $data
     * @param LateCause|null $employee_late_cause
     * @return array
     */
    public static function createOrUpdateLateCause(array $data, LateCause $employee_late_cause = null): array
    {
        DB::beginTransaction();

        try {
            if (is_null($employee_late_cause)) {
                // Create new employee_late_cause record
                $employee_late_cause = LateCause::create($data);
                $message = "Late Cause added successfully!";
            } else {
                // Update existing employee_late_cause record
                $employee_late_cause->update($data);
                $message = "Late updated successfully!";
            }

            DB::commit();
            return ["msg" => $message, "status" => true];
        } catch (\Exception $exception) {
            DB::rollback();
            Log::error("Failed to process employee_late_cause data", [
                'error' => $exception->getMessage(),
                'data' => $data,
            ]);
            return ["msg" => 'Failed to process employee_late_cause: ' . $exception->getMessage(), "status" => false];
        }
    }
}
