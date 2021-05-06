<?php


namespace App;


use Jenssegers\Mongodb\Eloquent\Model as Moloquent;


class Likes extends Moloquent

{
    protected $connection = 'mongodb';
    protected $collection = 'likes';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'media_id','execution_id','follower_id','username','is_unlike'
    ];
}
