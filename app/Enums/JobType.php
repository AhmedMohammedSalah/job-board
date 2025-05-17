<?php
namespace App\Enums;

enum JobType: string
{
    case REMOTE = 'remote';
    case ONSITE = 'onsite';
    case HYBRID = 'hybrid';

    public function label(): string
    {
        return match($this) {
            self::REMOTE => 'Remote',
            self::ONSITE => 'On-site',
            self::HYBRID => 'Hybrid',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::REMOTE => '🌍',
            self::ONSITE => '🏢',
            self::HYBRID => '🔀',
        };
    }

    public static function toSelectArray(): array
    {
        return collect(self::cases())->mapWithKeys(fn($case) => [
            $case->value => $case->label()
        ])->toArray();
    }
}
?>
