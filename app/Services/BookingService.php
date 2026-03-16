<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\Event;
use App\Models\RestaurantTable;
use Carbon\Carbon;

class BookingService
{
    /**
     * Check if a table is available at a given time.
     */
    public function isTableAvailable($tableId, $time, $durationMinutes = 120)
    {
        $startTime = Carbon::parse($time);
        $endTime = (clone $startTime)->addMinutes($durationMinutes);

        // Check if there are any confirmed reservations in this time window
        $conflicts = Reservation::where('table_id', $tableId)
            ->where('status', 1) // Confirmed
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('reservation_time', [$startTime, $endTime])
                    ->orWhereRaw('? BETWEEN reservation_time AND DATE_ADD(reservation_time, INTERVAL 2 HOUR)', [$startTime]);
            })
            ->exists();

        if ($conflicts) return false;

        // Check if there is an approved event that blocks the whole branch
        $table = RestaurantTable::find($tableId);
        if ($this->isBranchFullyBooked($table->branch_id, $startTime)) {
            return false;
        }

        return true;
    }

    /**
     * Check if a branch is fully booked due to an approved event.
     */
    public function isBranchFullyBooked($branchId, $time)
    {
        $checkTime = Carbon::parse($time);

        return Event::where('branch_id', $branchId)
            ->where('admin_approval', 1) // Approved
            ->where('start_time', '<=', $checkTime)
            ->where('end_time', '>=', $checkTime)
            ->exists();
    }
}
