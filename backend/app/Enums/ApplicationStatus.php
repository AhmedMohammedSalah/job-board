<?php

namespace App\Enums;

enum ApplicationStatus: string
{
    case PENDING = 'pending';
    case REVIEWED = 'reviewed';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';
    case HIRED = 'hired';
    case SHORTLISTED = 'shortlisted';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending Review',
            self::REVIEWED => 'Reviewed',
            self::ACCEPTED => 'Accepted',
            self::REJECTED => 'Rejected',
            self::SHORTLISTED => 'Shortlisted',
        };
    }
}
