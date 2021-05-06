<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\ScrapperManager;
use Illuminate\Support\Facades\Log;
class InstaStart implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
protected $username;
protected $pass;

    public function __construct($username,$password)
    {
        //
        $this->username=$username;
        $this->pass=$password;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try
        {
            $username=$this->username;
            $pass=$this->pass;
            $insta=new ScrapperManager($username,$pass);
         // $insta->test();
            $insta->startScrapping();
        }
       catch (\Exception $e) 
       {
           Log::error($e);
       }
    }
}
