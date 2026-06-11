<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 *
 * @method bool hasRole(string|array|\Spatie\Permission\Contracts\Role|\Illuminate\Support\Collection $roles)
 * @method bool hasAnyRole(string|array|\Spatie\Permission\Contracts\Role|\Illuminate\Support\Collection ...$roles)
 * @method bool hasAllRoles(string|array|\Spatie\Permission\Contracts\Role|\Illuminate\Support\Collection $roles)
 * @method \Spatie\Permission\Contracts\Role|\Spatie\Permission\Contracts\Role[] assignRole(...$roles)
 * @method int removeRole(string|\Spatie\Permission\Contracts\Role $role)
 * @method \Illuminate\Support\Collection getRoleNames()
 * @method bool can(string|array $permission, string|null $guardName = null)
 * @method bool hasPermissionTo(string|array|\Spatie\Permission\Contracts\Permission $permission, string|null $guardName = null)
 * @method \Spatie\Permission\Contracts\Permission|\Spatie\Permission\Contracts\Permission[] givePermissionTo(...$permissions)
 * @method \Spatie\Permission\Contracts\Permission|\Spatie\Permission\Contracts\Permission[] syncPermissions(...$permissions)
 * @method \Spatie\Permission\Contracts\Permission|\Spatie\Permission\Contracts\Permission[] revokePermissionTo(string|\Spatie\Permission\Contracts\Permission $permission)
 * @method \Illuminate\Support\Collection getPermissionNames()
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles,Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'student_id',
        'password',
        'role',
        'role_id',
        'is_admin',
    ];

    protected $guard_name = 'sanctum';

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [

            'password' => 'hashed',
        ];
    }

    public function getCurrentGuard(): string
    {
        // نأخذ guard الافتراضي من config/auth.php
        return $this->guard_name ?? config('auth.defaults.guard', 'web');
    }

    public function student()
    {
        return $this->hasOne(Student::class, 'user_id');
    }

    public function fcmTokens()
    {
        return $this->hasMany(FcmToken::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'student_id', 'student_id');
    }

    public function examScores()
    {
        return $this->hasMany(ExamScore::class, 'student_id', 'student_id');
    }

    public function routeNotificationForFcm()
    {
        return $this->fcmTokens()->pluck('device_token')->toArray();
    }

    /*public function roles()
       {
           return $this->belongsToMany(Role::class);
       }*/
    public function classroom()
    {
        return $this->belongsToMany(Classroom::class);
    }
}
