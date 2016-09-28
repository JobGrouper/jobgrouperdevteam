<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Carbon\Carbon;
use DB;
class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'user_type', 'email', 'password', 'linkid_url', 'fb_url', 'git_url', 'description', 'active', 'paypal_email'
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
     * The attributes that should be casted to native types.
     * The supported cast types are:
     * integer, real, float, double, string, boolean, object, array, collection, date, datetime, timestamp.
     *
     * @var array
     */
    protected $casts = [
        'email_confirmed' => 'bool',
    ];

    /**
     * Accessor to get new messages count
     */
    public function getNewMessagesCountAttribute()
    {
        return 4;
    }

    /**
     * Accessor to get image_url attribute
     */
    public function getImageUrlAttribute()
    {
        return (file_exists('images/users/u_'.$this->id.'.png') ? '/images/users/u_'.$this->id.'.png' : '/img/Profile/user2.png');
    }

    /**
     * Accessor to get full_name attribute
     */
    public function getFullNameAttribute()
    {
        return $this->first_name.' '.$this->last_name;
    }

    /**
     * Get the registrate_at valie.
     *
     * @param  string  $value
     * @return string
     */
    public function getRegistrateAtAttribute()
    {
        return Carbon::parse($this->created_at)->format('d/m/Y');
    }

    public static function createBySocialProvider($providerUser)
    {
        $fullName = preg_replace(array('/\s{2,}/', '/[\t\n]/'), ' ', $providerUser->getName());
        list($first_name, $last_name) = explode(' ', $fullName);
        return self::create([
            'email' => $providerUser->getEmail(),
            'first_name' => $first_name,
            'last_name' => $last_name,
        ]);
    }

    /**
     * Accessor to get new_messages attribute
     */
    public function getNewMessages($recipientID = 0)
    {
        if($recipientID){
            $countNewMessages = $this::messages()->where('sender_id', '=', $recipientID)->where('new', '=', true)->count();
        }
        else{
            $countNewMessages = $this::messages()->where('new', '=', true)->count();
        }

        if($countNewMessages == 0){
            $countNewMessages = false;
        }
        return $countNewMessages;
    }

    /**
     * Accessor to get rate attribute
     */
    public function getRateAttribute()
    {
        $rate = DB::table('rates')
            ->where('rated_id', $this->id)
            ->avg('score');
        return $rate * 100 / 5;
    }


    /**
     * Get user`s inbox messages.
     */
    public function messages()
    {
        return $this->hasMany('App\Message', 'recipient_id');
    }

    /**
     * Get user`s jobs.
     */
    public function jobs()
    {
        return $this->hasMany('App\Job', 'employee_id');
    }

    /**
     * Get user`s  potential jobs.
     */
    public function potential_jobs()
    {
        return $this->hasMany('App\Job', 'potential_employee_id');
    }

    /**
     * Get user`s orders.
     */
    public function orders()
    {
        return $this->hasMany('App\Sale', 'buyer_id');
    }

    /**
     * Get user`s experience.
     */
    public function experience()
    {
        return $this->hasMany('App\Experience', 'user_id');
    }

    /**
     * Get user`s education.
     */
    public function education()
    {
        return $this->hasMany('App\Education', 'user_id');
    }

    /**
     * Get user`s education.
     */
    public function additions()
    {
        return $this->hasMany('App\Addition', 'user_id');
    }


    /**
     * Get user`s education.
     */
    public function skills()
    {
        return $this->hasMany('App\Skill', 'user_id');
    }


    /**
     * Get user`s employee requests.
     */
    public function employee_requests()
    {
        return $this->hasMany('App\EmployeeRequest', 'employee_id');
    }

    /**
     * Get user`s employee requests.
     */
    public function employee_exit_requests()
    {
        return $this->hasMany('App\EmployeeExitRequest', 'employee_id');
    }

    /**
     * Get user`s close order requests
     */
    public function close_order_requests()
    {
        return $this->hasMany('App\CloseOrderRequest', 'originator_id');
    }

    /**
     * Get user`s credit cards
     */
    public function credit_cards()
    {
        return $this->hasMany('App\CreditCard', 'owner_id');
    }

    /**
     * Get user`s payments
     */
    public function payments()
    {
        return $this->hasMany('App\Payment', 'buyer_id');
    }


    /**
     * Get user`s rates
     */
    public function rates()
    {
        return $this->hasMany('App\Rate', 'rated_id');
    }


}
