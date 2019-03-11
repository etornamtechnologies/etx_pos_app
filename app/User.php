<?php

namespace App;

use Illuminate\Database\Eloquent\Model;



class User extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'username', 'phone','password', 'email', 'api_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function roles()
    {
        return $this->belongsToMany('App\Role', 'user_role', 'user_id', 'role_id');
    }

    public function hasAnyRole($roles)
    {
        
        if(is_array($roles)) {
            foreach($roles as $role) {
                if($this->hasRole($role)){
                    return true;
                }
            }
        }
        return false;
    }

    public function hasRole($role) 
    {
        if($this->roles()->where('label', $role)->first()) {
            return true;
        }
        return false;
    }

}
