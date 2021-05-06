<?php


namespace App;


use Jenssegers\Mongodb\Eloquent\Model as Moloquent;

class Users extends Moloquent

{
	protected $connection = 'mongodb';
	protected $collection = 'users';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username','execution_id', 'name','biography','posts','followers','following','is_private','is_business','media_url'
    ];
}
