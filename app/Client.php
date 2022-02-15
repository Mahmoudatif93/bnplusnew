<?php

namespace App;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laratrust\Traits\LaratrustUserTrait;
class Client extends Authenticatable implements JWTSubject
{
    protected $guarded = [];

    protected $fillable = ['id','name', 'phone','email','password'];

 
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
    public function getNameAttribute($value)
    {
        return ucfirst($value);

    }//end of get name attribute
    


}//end of model
