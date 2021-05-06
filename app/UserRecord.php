<?php

namespace App;


use Jenssegers\Mongodb\Eloquent\Model as Moloquent;

class UserRecord extends Moloquent
{
    //
    protected $connection = 'mongodb';
	protected $collection = 'user_record';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username','password', 'instagram_detail','session_id','user_session_details'
    ];
}
