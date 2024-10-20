<?php

namespace App\Services;

use Rats\Zkteco\Lib\ZKTeco;

class AttendanceService
{
    protected $zk;

    public function __construct($ip, $port = 4370)
    {
      
        $this->zk = new ZKTeco($ip, $port);
    }

    public function connect()
    {
        return $this->zk->connect();
    }

    public function getAttendanceLogs()
    {
        if ($this->connect()) {
            // Fetch attendance logs
            $attendanceLogs = $this->zk->getAttendance();
            $this->zk->disconnect();

            return $attendanceLogs;
        }

        return null;
    }
}
