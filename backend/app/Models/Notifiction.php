<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifiction extends Model
{
    /** @use HasFactory<\Database\Factories\NotifictionFactory> */
    use HasFactory;
    protected $fillable = [
        'user_id',
        'type', // 'job_match', 'application_update', 'new_message', 'job_alert'
        'title',
        'message',
        'metadata',
        'is_read',
        'read_at'
    ];
}
