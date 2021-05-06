<?php


namespace App;


use Jenssegers\Mongodb\Eloquent\Model as Moloquent;


class Stalker extends Moloquent

{
	protected $connection = 'mongodb';
	protected $collection = 'stalker';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'username', 'user_id','execution_id'
    ];
}
