<?php

namespace App\Models;
use App\Models\Candidate;
use App\Models\Job;

use App\Enums\ApplicationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;
    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //
    protected $casts = [
        'status' => ApplicationStatus::class,
    ];


    protected $fillable = [
        'job_id',
        'candidate_id',
        'resume_path',
        'cover_letter',
        'status' // 'pending', 'reviewed', 'accepted', 'rejected'
    ];
    public function candidate() {
        return $this-> belongsTo(Candidate::class);
    }
}
