<?php

namespace App\Http\Services;

use App\Models\SingleMeeting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SingleMeetingService
{
    /**
     * Create or update a single_meeting record and its details.
     *
     * @param array $data
     * @param SingleMeeting|null $single_meeting
     * @return array
     */
    public static function createOrUpdateMeeting(array $data, SingleMeeting $single_meeting = null): array
    {
        DB::beginTransaction();

        try {
            if (is_null($single_meeting)) {
                // Create new single_meeting record
                $single_meeting = SingleMeeting::create($data);
                $message = "Meeting added successfully!";
            } else {
                // Update existing single_meeting record
                $single_meeting->update($data);
                $message = "Meeting updated successfully!";
            }

            DB::commit();
            return ["msg" => $message, "status" => true];
        } catch (\Exception $exception) {
            DB::rollback();
            Log::error("Failed to process single_meeting data", [
                'error' => $exception->getMessage(),
                'data' => $data,
            ]);
            return ["msg" => 'Failed to process single_meeting: ' . $exception->getMessage(), "status" => false];
        }
    }
}
