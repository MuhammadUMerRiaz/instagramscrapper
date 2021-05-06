<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\InstaStart;
use App\Services\ScrapperManager;

class InstaStartController extends Controller
{
        
    public function index(Request $request)
    {
        $username=$request->username;
        $pass=$request->password;
        $job=new InstaStart($username,$pass);

        // if($username=="hitechbuddies")
        // {
        // $this->dispatch($job->delay(1)->onQueue('queue1'));
        // }
        // else{
        $this->dispatch($job);
            
        // }
    
    }
    
    public function index2()
    {
        
        $username='hitechbuddies';
        $password='HiTech123#';
        $this->dispatch(new InstaStart($username,$password));
    
    }
}
