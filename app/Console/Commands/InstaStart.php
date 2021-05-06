<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\UserRecord;
class InstaStart extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'insta:start';



    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Instagram Account scrapping are now in process';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        // $pass = $this->argument('password');
        // $username = $this->argument('username');

        $users=UserRecord::all();

       foreach( $users as $user)
       {
        $this->dispatch(new \App\Jobs\InstaStart($user->username,$user->password));
       }
    }
}
