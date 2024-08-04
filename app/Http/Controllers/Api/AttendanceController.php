<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    // Checkin
    public function checkin(Request $request)
    {
        // Validate latitude and longitude
        $request->validate([
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        // Save new attendance
        $attendance = new Attendance();
        $attendance->user_id = $request->user()->id;
        $attendance->date = date('Y-m-d');
        $attendance->time_in = date('H:i:s');
        $attendance->latlon_in = $request->latitude . ',' . $request->longitude;
        $attendance->type = 'checkin'; // Indicate that this is a checkin entry
        $attendance->save();

        return response([
            'message' => 'Checkin success',
            'attendance' => $attendance
        ], 200);
    }

    // Checkout
    public function checkout(Request $request)
    {
        // Validate latitude and longitude
        $request->validate([
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        // Get today's latest checkin entry that doesn't have a checkout yet
        $attendance = Attendance::where('user_id', $request->user()->id)
            ->where('date', date('Y-m-d'))
            ->whereNull('time_out') // Ensure there's no existing checkout for this checkin
            ->orderBy('time_in', 'desc')
            ->first();

        // Check if attendance not found
        if (!$attendance) {
            return response(['message' => 'Checkin first'], 400);
        }

        // Save checkout
        $attendance->time_out = date('H:i:s');
        $attendance->latlon_out = $request->latitude . ',' . $request->longitude;
        $attendance->save();

        return response([
            'message' => 'Checkout success',
            'attendance' => $attendance
        ], 200);
    }

    // Check if checked in
    public function isCheckedin(Request $request)
    {
        // Get the latest attendance entry of today
        $attendance = Attendance::where('user_id', $request->user()->id)
            ->where('date', date('Y-m-d'))
            ->orderBy('time_in', 'desc')
            ->first();

        $isCheckedin = $attendance ? $attendance->time_in : false;
        $isCheckedout = $attendance ? $attendance->time_out : false;

        return response([
            'checkedin' => $isCheckedin ? true : false,
            'checkedout' => $isCheckedout ? true : false,
        ], 200);
    }
}
