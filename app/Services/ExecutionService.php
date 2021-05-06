<?php

namespace App\Services;

use Illuminate\Http\Request;
use Phpfastcache\Config\Config;
use Phpfastcache\Helper\Psr16Adapter;
use InstagramScraper\Model\Media;
use InstagramScraper\Instagram;
use App\Executions;

class ExecutionService
{

    public function generateExecution()
    {

        $first_execution=Executions::latest()->get();
        if(count($first_execution)==0)
        {
           $first_execution=Executions::create(['execution_id'=>0,]);
        }
       
        $first_execution=$first_execution->first();
        $next_execution=$first_execution->execution_id;
        $new_execution=Executions::create(['execution_id'=>$next_execution+1,]);
        return $this->execution_id=$new_execution->execution_id;
    }
    
    
    public function SaveExecutionTable($execution_id,$follower_id_list,$following_id_list,$user)
     { 
       $execution=  Executions::where('execution_id',$execution_id)->get()->first();
       $execution->user_id=$user->_id;
       $execution->followers_list=$follower_id_list;
       $execution->following_list=$following_id_list;
       $execution->username=$user->username;
       $execution->save();
    
    }

}