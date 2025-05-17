<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    /** @use HasFactory<\Database\Factories\CandidateFactory> */
    use HasFactory;

    // [ams]
    protected $fillable = [
        'id', // Same as users.id
        'headline',
        'skills',
        'resume_path',
        'experience_years',
        'linkedin_url'
    ];
    // Disable auto-incrementing as this uses user_id as primary key
    public $incrementing = false;

    // Define the primary key (which is also a foreign key)
    protected $primaryKey = 'id';

    public function user  (){
        // remember that id in candidate table is not auto generated and it  is foreign key to user table
        // so we need to set it as primary key

    // This is correct - a candidate belongs to a user
    return $this->belongsTo(User::class, 'id', 'id');
        }

}
