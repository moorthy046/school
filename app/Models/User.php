<?php

namespace App\Models;

use Carbon\Carbon;
use Efriandika\LaravelSettings\Facades\Settings;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Cartalyst\Sentinel\Users\EloquentUser;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tymon\JWTAuth\Contracts\JWTSubject as JWTSubject;

class User extends EloquentUser implements AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract,
    JWTSubject
{
    use Authenticatable, Authorizable, CanResetPassword;

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = array('password', 'remember_token','created_at','updated_at');

    protected $guarded = array('id');

    protected $fillable = ['email', 'password', 'permissions', 'first_name','last_name','address','picture','mobile'
        ,'phone','gender','birth_date','birth_city'];

    protected $appends = ['full_name','picture'];

	public function date_format()
	{
		return Settings::get('date_format');
	}

	public function setBirthDateAttribute($date)
	{
		$this->attributes['birth_date'] = Carbon::createFromFormat($this->date_format(),$date)->format('Y-m-d');
	}

	public function getBirthDateAttribute($birth_date)
	{
		if ($birth_date == "0000-00-00" || $birth_date == "") {
			return "";
		} else {
			return date($this->date_format(), strtotime($birth_date));
		}
	}

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getPictureAttribute()
    {
        $picture = $this->attributes['picture'];
        $gender = $this->attributes['gender'];
        if (empty($picture))
            return asset('uploads/avatar/avatar') .(($gender==0)?'m':'f'). '.png';

        return asset('uploads/avatar') . '/' . $picture;
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'user_id_receiver');
    }

    public function parents()
    {
        return $this->hasMany(ParentStudent::class, 'user_id_student','id');
    }
    public function books()
    {
        return $this->hasMany(BookUser::class,'user_id','id');
    }

    public function visitor()
    {
        return $this->hasMany(Visitor::class, 'user_id');
    }

    public function student()
    {
        return $this->hasMany(Student::class, 'user_id');
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
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
}
