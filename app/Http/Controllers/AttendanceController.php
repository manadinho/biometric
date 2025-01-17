<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function getLastAttId(){
        $attendance = Attendance::latest()->first();
        $attendanceId = $attendance ? $attendance->machine_id : 0;
        return response()->json(['id' => $attendanceId]);
    }

    public function markAttendanceBulk(Request $request){
        $createdCount = 0;
        $updatedCount = 0;
        foreach($request->attendance as $att) {
            $timeStamp = Carbon::createFromTimestamp($att['timestamp'])->subHours(5)->format('Y-m-d H:i:s');
            $attendance = Attendance::updateOrCreate(['machine_id' => $att['id']],[
                'machine_id' => $att['id'],
                'user_id' => $att['empId'],
                'timestamp' => $timeStamp
            ]);

            if ($attendance->wasRecentlyCreated) {
                $createdCount++;
                continue;
            }

            $updatedCount++;
        }

        return response()->json(['created' => $createdCount, 'updated' => $updatedCount]);
    }
}
