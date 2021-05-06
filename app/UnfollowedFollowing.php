<?php


namespace App;


use Jenssegers\Mongodb\Eloquent\Model as Moloquent;


class UnfollowedFollowing extends Moloquent

{
	protected $connection = 'mongodb';
	protected $collection = 'unfollowed_following';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'instagram_id', 'name', 'username', 'user_id','execution_id'
    ];
}
