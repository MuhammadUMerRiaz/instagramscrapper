<?php

namespace App\Services;

use App\Executions;
use Illuminate\Http\Request;
use Phpfastcache\Helper\Psr16Adapter;
use InstagramScraper\Model\Media;
use InstagramScraper\Instagram;
use Phpfastcache\Config\Config;
use App\Services\ProcessingService;
use App\Services\AccountDetailService;
use App\Services\FollowersFollowingService;
use App\Services\MediaService;
use App\Services\ExecutionService;
use Illuminate\Support\Facades\Redirect;


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


class ScrapperManager
{
    protected $instagram_details;
    protected $session_id;
    protected $user_id;
    protected $user_name;
    protected $user_password;
    protected $user;
    protected $followers_list=[];
    protected $following_list=[];
    protected $execution_id;
    protected $engagement=[];
    protected $commentCode=[];
  
    public function __construct($username,$pass) 
    {        
       $this->user_name=$username;
       $this->user_password=$pass;
    }

    public function startScrapping()
    {   $username=$this->user_name;
        $pass=$this->user_password;
        $instagram_temp=new LoginService();
        $instagram=$instagram_temp->login2($username,$pass); 
        
        $this->instagram_details=$instagram[0];
        $this->session_id=$instagram[1]['sessionid'];
        $this->user_id=$instagram[1]['ds_user_id'];
        $instagram_temp->UserRecordSave($username,$pass,$instagram);
        
        sleep(1);
        $execution=new ExecutionService();
        $this->execution_id= $execution->generateExecution();
        sleep(2);
        $userDetail=new AccountDetailService();
        $this->user=$userDetail->AccountById($this->user_id,$this->instagram_details,$this->user_name,$this->user_password,$this->execution_id);
    
        $followfollowing=new FollowersFollowingService($this->instagram_details,$this->user,$this->execution_id);
        $this->followers_list=$followfollowing->AccountFollowers($this->user_id);   //execute AccountFollowers function before AccountFollowing function, some variable are depend upon AccountFollowers function
        $this->following_list=$followfollowing->AccountFollowing($this->user_id);
   
        $media=new MediaService($this->instagram_details,$this->user,$this->execution_id,$this->followers_list);
        $media->AccountMediaByUsername($this->user_name);

        $processing=new ProcessingService($this->user,$this->execution_id,$this->followers_list,$this->following_list);
        $processing->getSecretAdmirer();
        $processing->getUnfollowfollowers();
        $processing->getUnfollowfollowing();
        $processing->FollowerAttributes();

        $execution->SaveExecutionTable($this->execution_id,$this->followers_list,$this->following_list,$this->user);
      

    }


}
