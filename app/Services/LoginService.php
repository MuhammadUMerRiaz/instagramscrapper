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

use App\UserRecord;

class LoginService
{

    
    public function login($username,$pass,$auth=0)
    {
        $u=0;
        sleep(1);
      $instagram=LoginService::login2($username.$pass);
      
       if($auth==1)
       {
           if($instagram[1]=NULL)
           {
               return $instagram;
           }
           else
           {
               return false;
           }
        }
     
       
    }

    public function login2($username,$pass)
    {   
        // try{
        // dd($username);
        sleep(3);
        $i=1000;
        $instagram_details=[];
        echo "Start1S";

        $this->user_name=$username;
        $this->user_password=$pass;
        $instagram=NULL;
        $instagram_login=NULL;
   
        $instagram = \InstagramScraper\Instagram::withCredentials(new \GuzzleHttp\Client(['proxy' => 'http://169.57.1.85:80']), $this->user_name, $this->user_password, new Psr16Adapter(SingletonScrapperManager::instance()));
        // $instagram->setUserAgent('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:78.0) Gecko/20100101 Firefox/78.0');
        $instagram->setUserAgent('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.85 Safari/537.36');
        $instagram_login=$instagram->login();  // will use cached session if you want to force login $instagram->login(true)
        // dd($instagram);
        $instagram_session=$instagram->saveSession();
        sleep($i++);
        
          if ($instagram_login==NULL )
          {
              echo "Again login";
              $instagram_details= LoginService::login2($username,$pass);
              return $instagram_details;   
          }
        // dd($instagram_details);
        array_push($instagram_details,$instagram);
        array_push($instagram_details,$instagram_login);
        // dd( $instagram_details); 
        return $instagram_details; 
        // }
        // catch (\Exception $e) {
        //    echo $e->getMessage();
        // }

    }

    public function UserRecordSave($username,$pass,$instagram_details)
    {
 
        $user_record=UserRecord::where('username',$username)->get()->first();
           if($user_record==NULL)
           {
              $user_record= UserRecord::create
               ([
                   "username"=>$username,
                   "password"=>$pass,
                   "instagram_detail"=>$instagram_details[0],
                   "session_id"=>$instagram_details[1]['sessionid'],
                   "user_session_details"=>$instagram_details[1]
               ]);
               
           }
           else
           {
               $user_record->password=$pass;
               $user_record->instagram_detail=$instagram_details[0];
               $user_record->session_id=$instagram_details[1]['sessionid'];
               $user_record->user_session_details=$instagram_details[1];
               $user_record->save();
            }
        return $user_record;

    }



}



// 159.65.116.90	3128