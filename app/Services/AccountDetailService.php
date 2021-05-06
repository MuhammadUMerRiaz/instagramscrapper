<?php

namespace App\Services;

use Illuminate\Http\Request;
use Phpfastcache\Config\Config;
use App\Users;
use DB;

class AccountDetailService
{
  
    public function AccountById($id,$instagram_credentails,$username,$password,$execution_id)
    {
       $account = $instagram_credentails->getAccountById($id);
       $user=  Users::create([
       'username'=>$username,
       'password'=>$password,
       'execution_id'=>$execution_id,
       'name'=>$account->getFullName(),
       'biography'=>$account->getBiography(),
       'posts'=>$account->getMediaCount(),
       'followers'=>$account->getFollowedByCount(),
       'following'=>$account->getFollowsCount(),
       'is_private'=>$account->isPrivate(),
       'is_verified'=>$account->isVerified(),
       'media_url'=>$account->getProfilePicUrl(),
       'instagram_id'=>$account->getId(),
       'exteranl_link'=>$account->getExternalUrl()
   
       ]);
       return $user; 
    }




}