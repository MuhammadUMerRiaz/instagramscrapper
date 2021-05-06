<?php

namespace App\Services;

use Illuminate\Http\Request;
use Phpfastcache\Config\Config;
use Phpfastcache\Helper\Psr16Adapter;
use InstagramScraper\Model\Media;
use InstagramScraper\Instagram;
use App\Followers;
use DB;
use App\Medias;
use App\Comment;
use App\Likes;

class MediaService
{
protected $user;
protected $execution_id;
protected $instagram_details;
protected $followers_global_id_list=[];
protected $return_array=[];

public function __construct($instagram_details,$user,$execution_id,$follower_list) 
  {        
    $this->instagram_details=$instagram_details;
    $this->user=$user;
    $this->execution_id=$execution_id;
    $this->followers_global_id_list=$follower_list;
  }

 public function AccountMediaByUsername($username)
 {
      $instagram = $this->instagram_details->getAccount($username);
      $count=MediaService::MediaCount($instagram->getId());
      $medias = $instagram->getMedias($instagram, $count);
      $comment_code_list=[];
      foreach($medias as $media)
      {
         $d=date("Y-m-d H:i:s", $media->getCreatedTime());
         $view=0;
         $v=0;
         $i=1;

        if($media->getType()=='video')
        {
            $view=$media->getVideoViews();
            $v=1;
            $i=0;
        }
         $m=Medias::create
         ([
            'instagram_id'=>$instagram->getId(),
            'execution_id'=>$this->execution_id,
            'username'=>$instagram->getUsername(),
            'name'=>$instagram->getFullName(),
            'user_id'=>$this->user->_id,
            'postdate'=>$d,
            'likes'=>$media->getLikesCount(),
            'comments'=>$media->getCommentsCount(),
            'mediaurl'=>$media->getLink(),
            'views'=>$view,
            'is_video'=>$v,
            'is_image'=>$i,
            'mediahdurl'=>$media->getImageHighResolutionUrl(),
            'shortcode'=>$media->getShortCode(),
            'media_id'=>$media->getId(),
            'caption'=>$media->getCaption(),
         ]);

        echo "<p>All media is being Stored in table!</b><b> Total Media:</b>{$count}</p>";
       $comments = $this->instagram_details->getMediaCommentsByCode( $media->getShortCode(), 2, $maxId = null);

        foreach($comments as $comment)
        {
          $account = $comment->getOwner();
          $is_followers=0;
          if (in_array($account->getId(), $this->followers_global_id_list))
          {
              $is_followers=1;
          }
          array_push($comment_code_list,$comment->getId());
          Comment::create
          ([
              'username'=>$account->getUsername(),
              'follower_id'=>$is_followers,
              'execution_id'=>$this->execution_id,
              'media_id'=>$m->_id,
              'comment_code'=>$comment->getId(),
              'comments'=>$comment->getText(),
              'is_deleted'=>0,
          ]);
        }

      MediaService::MediaLikes($media,$m);

  }
     echo "<p>All <b>Comments and likes</b> of all media is being Stored in table!</p>";
    return $comment_code_list;
 }







    public function MediaLikes($media,$m)
    {
        $likes = $this->instagram_details->getMediaLikesByCode($media->getShortCode(), $media->getLikesCount(), $maxId = null);

        foreach ($likes as $like) {

            $is_followers = 0;
            if (in_array($like->getId(), $this->followers_global_id_list)) {
                $is_followers = 1;
            }

            Likes::create([
                'username' => $like->getUsername(),
                'follower_id' => $is_followers,
                'execution_id'=>$this->execution_id,
                'media_id' => $m->_id,
                'comment_code' => $like->getId(),
                'is_unlike' => 0,
            ]);

        }
    }








    public function MediaCount($id)
    {
     $account = $this->instagram_details->getAccountById($id);
     return $account->getMediaCount();
    }

    }