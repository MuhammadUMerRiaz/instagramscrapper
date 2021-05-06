<?php


namespace App;


use Jenssegers\Mongodb\Eloquent\Model as Moloquent;


class SecretAdmirer extends Moloquent

{
	protected $connection = 'mongodb';
	protected $collection = 'secretadmirer';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'username', 'user_id', 'media_id', 'is_like', 'comment_count','execution_id'
    ];
}
