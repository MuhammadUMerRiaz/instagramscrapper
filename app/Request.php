<?php


namespace App;


use Jenssegers\Mongodb\Eloquent\Model as Moloquent;


class Request extends Moloquent

{
	protected $connection = 'mongodb';
	protected $collection = 'request';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'username', 'user_id','execution_id'
    ];
}
