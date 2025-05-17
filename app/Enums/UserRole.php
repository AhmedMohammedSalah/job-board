<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case EMPLOYER = 'employer';
    case CANDIDATE = 'candidate';

    public function label(): string
    {
        return match($this) {
            self::ADMIN => 'Admin',
            self::EMPLOYER => 'Employer',
            self::CANDIDATE => 'Candidate',
        };
    }
}

?>
