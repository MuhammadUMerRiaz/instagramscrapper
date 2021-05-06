<?php


namespace App;


use Jenssegers\Mongodb\Eloquent\Model as Moloquent;


class Followers extends Moloquent

{
	protected $connection = 'mongodb';
	protected $collection = 'followers';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'instagram_id','name', 'username', 'user_id','execution_id', 'name', 'likes', 'comments', 'following_back', 'is_newfollowers', 'is_topfollowers', 'is_bestfollowers', 'is_worstfollowers','is_ghostfollowers'
    ];
}
