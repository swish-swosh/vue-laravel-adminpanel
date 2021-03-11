<?php
namespace App\Models;

use App\Models\Role;
use App\Models\UserProfile;
use App\Models\Organisation;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\CanResetPassword;
use App\Notifications\PasswordResetNotification;
use App\Notifications\EmailVerificationNotification;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'organisation_id',
        'roles'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime'
    ];

    protected $attributes = [
        'organisation_id' => 1
    ];

    public function isSuperAdmin(){
        return $this->roles()->where('name', 'SuperAdmin')->exists();
    }

    public function isAdmin(){
        return $this->roles()->where('name', 'Admin')->exists();
    }

    public function isGuestUser(){
        return $this->roles()->where('name', 'Guest')->exists();
    }

    public function pluckRoles($colums)
    {
        return $this->roles->pluck($colums)->toArray(); 
    }

    public function hasAnyRoles($roles) {
        return $this->roles()->whereIn('name', $roles)->exists();
    }

    public function hasRole($role)
    {
        return $this->roles()->where('name', $role)->exists();
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    public function organisation()
    {
        return $this->belongsTo(Organisation::class, 'organisation_id', 'id');
    }

    public function sendPasswordResetNotification($token)
    {       
        $this->notify(new PasswordResetNotification($token));
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new EmailVerificationNotification());
    }

    // public function userProfile()
    // {
    //     return $this->belongsTo(Resource::class, 'user_profile_id', 'id');
    // }
}
