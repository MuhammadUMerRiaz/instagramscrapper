<?php

namespace App\Services;

use Illuminate\Http\Request;
use Phpfastcache\Config\Config;
use InstagramScraper\Instagram;
use App\Followers;
use App\Followings;
use DB;

class FollowersFollowingService
{
protected $user;
protected $execution_id;
protected $instagram_details;
protected $followers_global_id_list=[];
protected $following_global_id_list=[];
public function __construct($instagram_details,$user,$execution_id) 
  {        
    $this->instagram_details=$instagram_details;
    $this->user=$user;
    $this->execution_id=$execution_id;
  }

public function AccountFollowers($id)
  {
    $followers_id_list = [];
    $followers_details_list = [];
    $following_details_list = [];
    $following_id_list=[];
    $followers_count=FollowersFollowingService::FollowersCount($id);
    $following_count=FollowersFollowingService::FollowingCount($id);
    
    sleep(2); 
    $followers_details_list = $this->instagram_details->getFollowers($id, $followers_count, $followers_count, true);
    sleep(2);
    $following_details_list = $this->instagram_details->getFollowing($id, $following_count, $following_count, true);
    if($following_details_list!=NULL)
       {
           for ($i = 0; $i < count($following_details_list['accounts']); $i++){
            array_push($following_id_list,$following_details_list['accounts'][$i]['id']);}
            $this->following_global_id_list=$following_id_list;
       }
    for ($i = 0; $i < count($followers_details_list); $i++)
    {
          array_push($followers_id_list,$followers_details_list[$i]['id']);
          $following_back=0;
          if (in_array($followers_details_list[$i]['id'],$following_id_list)){
               $following_back=1;}
  
          Followers::create
          ([
              'instagram_id'=>$followers_details_list[$i]['id'],
              'execution_id'=>$this->execution_id,
              'username'=>$followers_details_list[$i]['username'],
              'name'=>$followers_details_list[$i]['full_name'],
              'user_id'=>$this->user->_id,
              'following_back'=>$following_back,
              'is_newfollowers'=>0,
              'is_topfollowers'=>0,
              'is_bestfollowers'=>0,
              'is_worstfollowers'=>0,
              'is_ghostfollowers'=>0, 
            ]);
  
    }
  
     return $this->followers_global_id_list=$followers_id_list;
     
     echo "<p>Account Followers is being Stored in table! <b>Followers Count</b>:{$followers_count}</p>";
  
}


public function AccountFollowing($id)
 {

  $following_details_list = [];
  $following_count=FollowersFollowingService::FollowingCount($id);
  sleep(2); 
  $following_details_list = $this->instagram_details->getFollowing($id, $following_count, $following_count, true); 
  
    if($following_details_list!=NULL)
    {  
        for ($i = 0; $i < count($following_details_list['accounts']); $i++)
        {
           $follow_back=0;
           if (in_array($following_details_list['accounts'][$i]['id'], $this->followers_global_id_list)){
            $follow_back=1;}
           
            Followings::create
            ([
              'instagram_id'=>$following_details_list['accounts'][$i]['id'],
              'execution_id'=>$this->execution_id,
              'username'=>$following_details_list['accounts'][$i]['username'],
              'name'=>$following_details_list['accounts'][$i]['full_name'],
              'user_id'=>$this->user->_id,
              'follow_back'=>$follow_back,
            ]);

        }  
    }
    return $this->following_global_id_list;
    echo "<p>Account Following is being Stored in table!<b> Followers Count:</b>{$count}</p>";
 }


private function FollowersCount($id)
{
    $account = $this->instagram_details->getAccountById($id);
    return $account->getFollowedByCount();
}

     
 private function FollowingCount($id)
 {
    $account = $this->instagram_details->getAccountById($id);
    return $account->getFollowsCount();
 }



}