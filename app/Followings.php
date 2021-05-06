<?php


namespace App;


use Jenssegers\Mongodb\Eloquent\Model as Moloquent;


class Followings extends Moloquent

{
	protected $connection = 'mongodb';
	protected $collection = 'followings';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'instagram_id','username', 'user_id', 'name', 'follow_back','execution_id'
    ];
}
