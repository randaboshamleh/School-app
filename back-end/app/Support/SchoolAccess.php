<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class SchoolAccess
{
    public static function canManageSchool(?User $user): bool
    {
        return self::isAdmin($user) || self::supervisorScope($user) !== null;
    }

    public static function isAdmin(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        $role = strtolower((string) $user->role);
        if ($role === 'admin') {
            return true;
        }

        return method_exists($user, 'hasRole') && $user->hasRole('admin');
    }

    /**
     * @return array{stage:string,gender:string}|null
     */
    public static function supervisorScope(?User $user): ?array
    {
        if (! $user) {
            return null;
        }

        $roles = collect([(string) $user->role]);
        if (method_exists($user, 'getRoleNames')) {
            $roles = $roles->merge($user->getRoleNames());
        }

        foreach ($roles->map(fn ($role) => strtolower((string) $role)) as $role) {
            $stage = match (true) {
                str_contains($role, 'primary') || str_contains($role, 'ابتدائي') => 'primary',
                str_contains($role, 'secondary') || str_contains($role, 'اعدادي') || str_contains($role, 'إعدادي') => 'secondary',
                str_contains($role, 'thirdy') || str_contains($role, 'tertiary') || str_contains($role, 'ثانوي') => 'thirdy',
                default => null,
            };

            $gender = match (true) {
                str_contains($role, 'female') || str_contains($role, 'اناث') || str_contains($role, 'إناث') => 'female',
                str_contains($role, 'male') || str_contains($role, 'ذكور') => 'male',
                default => null,
            };

            if ($stage && $gender && str_contains($role, 'supervisor')) {
                return ['stage' => $stage, 'gender' => $gender];
            }
        }

        return null;
    }

    public static function scopeStudents(Builder $query, ?User $user): Builder
    {
        if (self::isAdmin($user)) {
            return $query;
        }

        $scope = self::supervisorScope($user);
        if (! $scope) {
            return $query->whereRaw('1 = 0');
        }

        return $query
            ->where(function (Builder $q) use ($scope) {
                $q->where('stage', $scope['stage'])
                    ->orWhere('stage', self::arabicStage($scope['stage']));
            })
            ->where(function (Builder $q) use ($scope) {
                $q->where('gender', $scope['gender'])
                    ->orWhere('gender', self::arabicGender($scope['gender']));
            });
    }

    public static function arabicStage(string $stage): string
    {
        return match ($stage) {
            'primary' => 'ابتدائي',
            'secondary' => 'اعدادي',
            'thirdy' => 'ثانوي',
            default => $stage,
        };
    }

    public static function arabicGender(string $gender): string
    {
        return match ($gender) {
            'male' => 'ذكر',
            'female' => 'أنثى',
            default => $gender,
        };
    }
}
