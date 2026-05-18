<?php
namespace App\Rules;

class RoleRules {
    public static function getRoleDistribution(int $nbPlayerinRoom): array {
        return match($nbPlayerinRoom) {
            4 => [1 => 1, 2 => 0, 3 => 2, 4 => 1],
            5 => [1 => 1, 2 => 1, 3 => 2, 4 => 1],
            6 => [1 => 1, 2 => 1, 3 => 3, 4 => 1],
            7 => [1 => 1, 2 => 2, 3 => 3, 4 => 1],
            8 => [1 => 1, 2 => 2, 3 => 3, 4 => 2],
            default => []
        };
    }
}