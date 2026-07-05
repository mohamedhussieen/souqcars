<?php

namespace App\Enums;

/** Defines the three user roles available in the car marketplace. */
enum UserRole: string
{
    case Admin = 'admin';
    case ShowroomOwner = 'showroom_owner';
    case User = 'user';

    /** Returns the display label for this role. */
    public function label(): string
    {
        return match($this) {
            self::Admin => 'Administrator',
            self::ShowroomOwner => 'Showroom Owner',
            self::User => 'User',
        };
    }
}
