<?php

namespace App\Models;

class Event extends BaseModel
{
    protected $fillable = [
        'title',
        'branch_id',
        'start_time',
        'end_time',
        'estimated_guests',
        'description',
        'admin_approval',
        'status'
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function isFullyBooked()
    {
        // Logic to check if the branch is fully booked for this event's duration
        // This is a simplified version: if an event is approved, we mark it as "special handling"
        return $this->admin_approval === 1;
    }
}
