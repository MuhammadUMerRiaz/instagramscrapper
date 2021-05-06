<?php


namespace App;


use Jenssegers\Mongodb\Eloquent\Model as Moloquent;


class Comment extends Moloquent

{
	protected $connection = 'mongodb';
	protected $collection = 'comments';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'media_id','follower_id','username','comments','is_deleted','execution_id'
    ];
}
