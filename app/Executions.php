<?php


namespace App;


use Jenssegers\Mongodb\Eloquent\Model as Moloquent;


class Executions extends Moloquent

{
    protected $connection = 'mongodb';
    protected $collection = 'execution';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id','execution_id' ,'followers_list','following_list','username'
    ];
}
