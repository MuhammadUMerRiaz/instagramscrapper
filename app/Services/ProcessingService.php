<?php

namespace App\Services;

use App\Executions;
use Illuminate\Http\Request;
use Phpfastcache\Helper\Psr16Adapter;
use InstagramScraper\Model\Media;
use InstagramScraper\Instagram;
use Phpfastcache\Config\Config;
use App\Services\AccountDetailClass;
use App\Users;
use App\Followers;
use App\Followings;
use App\Medias;
use App\Comment;
use App\Likes;
use App\UserRecord;
use App\UnfollowedFollowing;
use App\UnfollowedFollowers;
use App\SecretAdmirer;
use DB;


class ProcessingService
{
    
    protected $user_name;
    protected $user;
    protected $followers_list=[];
    protected $following_list=[];
    protected $execution_id;
    protected $engagement=[];
    protected $commentCode=[];
  
    public function __construct($user,$execution_id,$followers_list,$following_list) 
    {        
       $this->user_name=$user->username;
       $this->user=$user;
       $this->followers_list=$followers_list;
       $this->following_list=$following_list;
       $this->execution_id=$execution_id;

    }


    public function getSecretAdmirer()
    {
       $like=   Likes::where('follower_id',0)->where('execution_id',$this->execution_id)->get()->pluck('username')->toArray();
       $comment=Comment::where('follower_id',0)->where('execution_id',$this->execution_id)->groupBy('username')->get()->pluck('username')->toArray();
       $l=array_merge($like,$comment);
       $username=array_unique($l);
       $medias_id=[];
       foreach($username as $u) 
       {
           $li = Likes::where('follower_id', 0)->where('execution_id', $this->execution_id)->where('username', $u)->get();
           $co = Comment::where('follower_id', 0)->where('execution_id', $this->execution_id)->where('username', $u)->get();
           foreach ($li as $l) 
           {
               array_push($medias_id, $l->media_id);
           }
           foreach ($co as $c) 
           {
               array_push($medias_id, $c->media_id);
           }
           
           $md=array_unique($medias_id);
           
           foreach($md as $m)
           {
               $media_id_like = Likes::where('follower_id', 0)->where('execution_id', $this->execution_id)->where('username', $u)->where('media_id', $m)->get();
               $media_id_comments = Comment::where('follower_id', 0)->where('execution_id', $this->execution_id)->where('username', $u)->where('media_id', $m)->get();
               $is_like=0;
               $comment_count=0;
               $us_id=Medias::where('_id',$m)->get()->first();
               
               if(count($media_id_comments)!=0 && count($media_id_like)!=0)
               {
                   if(count($media_id_like)!=0)
                   {
                       $is_like=1;
                   }
                   
                   $comment_count=count($media_id_comments);
                   $sid=SecretAdmirer::create
                   ([
                        'username'=>$u,
                        'media_id'=>$m,
                        'user_id'=>$us_id,
                        'name'=>$u,
                        '$is_like'=>$is_like,
                        'comment_count'=>$comment_count,
                        'execution_id'=>$this->execution_id,
                        
                    ]);
                }
            }
        }
    }


    public function getUnfollowfollowers()
    { $c=[];
        $executions=  Executions::where('username',$this->user_name)->get();
        if(count($executions)>1) 
        {
            $execution=  Executions::where('username',$this->user_name)->latest()->first();
            $oldFollowers=$execution->followers_list;
            $newFollowers=$this->followers_list;
            $Unfollowers=array_diff($oldFollowers,$newFollowers);
            array_push($c,$oldFollowers);
            array_push($c,$newFollowers);
            array_push($c,$Unfollowers);

            foreach($Unfollowers as $u) 
            {
                $f = Followers::where('instagram_id', $u)->where('execution_id', $execution->execution_id)->get()->first();
                array_push($c,$f);
            }
            // dd($c);

            foreach($Unfollowers as $u) 
            {
                $f = Followers::where('instagram_id', $u)->where('execution_id', $execution->execution_id)->get()->first();
         
               if($f!=NULL)
               { 
                    UnfollowedFollowers::create
                    ([ 
                        'instagram_id'=> $u,
                        'username' => $f->username,
                        'user_id' => $f->user_id,
                        'name' => $f->name,
                        'execution_id'=>$this->execution_id,
                    ]);
               }
            }
            return $Unfollowers;
        }
        else
        {
            return [];
            echo "<p>Result of<b> Unfollow followers</b> will be shown after someday or in next Execution</p>";
        }
    }
    
    public function getUnfollowfollowing()
    {   $c=[];
        $executions=  Executions::where('username',$this->user_name)->get();
        if(count($executions)>1) 
        {
            $execution = Executions::where('username', $this->user_name)->latest()->first();
            $oldFollowing = $execution->following_list;
            $newFollowing = $this->following_list;
            $Unfollowing = array_diff($oldFollowing, $newFollowing);

            array_push($c,$oldFollowing);
            array_push($c,$newFollowing);
            array_push($c,$Unfollowing);
                     
            // dd($c);

            foreach($Unfollowing as $u) 
            {
                $f = Followings::where('instagram_id', $u)->where('execution_id', $execution->execution_id)->get()->first();
            
                if($f!=NULL)
               {
                   UnfollowedFollowing::create
                   ([
                       'instagram_id'=> $u,
                       'username' => $f->username,
                       'user_id' => $f->user_id,
                       'name' => $f->name,
                       'execution_id'=>$this->execution_id,
                    ]);
               }
            }
            echo "<p>Get all the <b>Unfollow Following</b></p>";
        }
        else
        {
            echo "<p>Result of <b>Unfollow following</b> will be shown after someday or in next Execution</p>";
        }
    }

    public function getGainFollowers()
    {
        $executions=  Executions::where('username',$this->user_name)->get();
        if(count($executions)>1) 
        {
            $execution=  Executions::where('username',$this->user_name)->latest()->first();
            $oldFollowers=$execution->followers_list;
            $newFollowers=$this->followers_list;
            $gainfollowers=array_diff($newFollowers,$oldFollowers);
            return $gainfollowers;
            dd($gainfollowers);
        }
        else
        {
            return [];
            echo "Result will be shown after sometime";
        }
    }

    public function growth()
    {
        $loss=count(ScrapperManager::getUnfollowfollowers());
        $gain=count(ScrapperManager::getGainFollowers());

        $growthgain=$gain/count($this->followers_list)*100;
        $growthloss=$loss/count($this->followers_list)*100;


    }

    public function followingNotFollowers()
    {
      $following=  Followings::where('user_id',$this->user->_id)->where('execution_id',$this->execution_id)->where('follow_back',0)->get();
      return $following;
    }

    public function followerNotfollowing()
    {
      $followers=  Followers::where('user_id',$this->user->_id)->where('execution_id',$this->execution_id)->where('following_back',0)->get();
      return $followers;
    }
    public function mutualFollowing()
    {

        $Followers=$this->followers_list;
        $Following=$this->following_list;
        $mutual=array_intersect($Followers,$Following);
        return $mutual;
    }

    public function post()
    {
        $post=Media::where('user_id',$this->user->_id)->where('execution_id',$this->execution_id)->get();
        return $post;
    }

    public function FollowerAttributes()
    {
        $engagement=[];
        $execution=$this->execution_id;
        $followers=Followers::where('execution_id',$execution)->where('user_id',$this->user->_id)->get();

        foreach ($followers as $f) 
        {

            $l = count(Likes::where('follower_id',   1)->where('execution_id',$execution)->where('username', $f->username)->where('is_unlike',0)->get());
            $c = count(Comment::where('follower_id', 1)->where('execution_id',$execution)->where('username', $f->username)->where('is_deleted',0)->get());
            $e_cal=0;

            if($this->user->posts!=0) 
            {
              $e_cal = (($l + $c) / $this->user->posts) * 100;
            }
            $a=array_push($engagement,array($f->_id=>$e_cal));
            $isbest=0;
            $isghost=0;
            // ghost followers
            
            if($e_cal<5)
            {
                $isbest=1;
            }

           //best followers
           if($e_cal>100)
           {
               $isghost=1;
            }
            $f->likes=$l;
            $f->comments=$c;
            $f->is_bestfollowers=$isbest;
            $f->is_ghostfollowers=$isghost;
            $f->save();
        }

        ksort($engagement);

       if(count($engagement)>30)
       {
           for($i=0;$i<count($engagement);$i++)
           {
              $b= array_keys($engagement[$i]);

               if($i<10) 
               {
                   $followers=Followers::where('execution_id',$execution)->where('_id',$b[0])->get()->first();
                   $followers->is_topfollowers=1;
                   $followers->save();
                }
               if($i>count($engagement)-10)
               {
                   $followers=Followers::where('execution_id',$execution)->where('_id',$b[0])->get()->first();
                   $followers->is_worstfollowers=1;
                   $followers->save();
               }
           }
           echo "<p>Get remaining </b>followers Attributes</p>";
       }


       $this->engagement=$engagement;

    }

    public function deletedComment()
    {
        $newExecution=0;
        $execution=  Executions::latest()->first();
//      dd($execution);
        $oldComment=Comment::where('execution_id',$execution->execution_id)->get()->pluck('comment_code');
        $newComment=Comment::where('execution_id',$newExecution)->get()->pluck('comment_code');
        
        $deleted=array_diff($oldComment,$newComment);
        foreach($deleted as $d)
        {
            $comm=Comment::where('comment_code',$d)->where('execution_id',$execution->execution_id)->get()->first();
            Comment::create
            ([
                'username'=>$comm->username,
                'follower_id'=>$comm->follower_id,
                'execution_id'=>$newExecution,
                'media_id'=>$comm->media_id,
                'comment_code'=>$comm->comment_code,
                'comments'=>$comm->comments,
                'is_deleted'=>1,
            ]);
        }

        dd($oldComment);
    }
   public function unlikePost()
   {
       $newExecution=$this->execution_id;
       $execution=  Executions::latest()->first();

       $oldlikes=Likes::where('execution_id',$execution->execution_id)->get()->pluck('media_id');
       $newlikes=Likes::where('execution_id',$newExecution)->get()->pluck('media_id');


       $unlike=array_diff($oldlikes,$newlikes);
       foreach($unlike as $l)
       {
           $lk=Likes::where('media_id',$l)->where('execution_id',$execution->execution_id)->get()->first();
           Likes::create
           ([
               'username'=>$lk->username,
               'follower_id'=>$lk->follower_id,
               'execution_id'=>$newExecution,
               'media_id'=>$lk->media_id,
               'is_unlike'=>1,
           ]);
        }
    }

   public function totalLikes()
   {
       $newExecution=$this->execution_id;
       $totallikes=Likes::where('execution_id',$newExecution)->where('username',$this->user_name)->get();
       return count($totallikes);
   }
    
   public function totalComments()
    {
        $newExecution=$this->execution_id;
        $totalcomments=Comments::where('execution_id',$newExecution)->where('username',$this->user_name)->get();
        return count($totalcomments);
    }

    public function totalViews()
    {
        $newExecution=$this->execution_id;
        $medias=Media::where('execution_id',$newExecution)->where('user_id',$this->user->_id)->get();
        $t_view=0;
        foreach($medias as  $media)
        {
         $t_view=$t_view+$media->view;
        }
        return $t_view;
    }

}