<?php


namespace App;


use Jenssegers\Mongodb\Eloquent\Model as Moloquent;


class Medias extends Moloquent

{
	protected $connection = 'mongodb';
	protected $collection = 'media';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id','execution_id', 'postdate', 'postnumber', 'is_story', 'is_post', 'likes', 'comments', 'mediaurl', 'views', 'is_video', 'is_image'
    ];
}
