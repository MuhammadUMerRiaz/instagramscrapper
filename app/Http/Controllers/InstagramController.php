<?php

namespace App\Http\Controllers;

use App\Executions;
use Illuminate\Http\Request;
use Phpfastcache\Helper\Psr16Adapter;
use InstagramScraper\Model\Media;
use InstagramScraper\Instagram;

use Phpfastcache\Config\Config;

use App\Users;
use App\Followers;
use App\Followings;
use App\Medias;
use App\Comment;
use App\Likes;
use DB;
use UnfollowedFollowing;
use UnfollowedFollowers;

use App\SecretAdmirer;
class InstagramController extends Controller
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
   public function index2()
    {

     $u=  Users::create(['username'=>'mer-']);
       dd($u);
    }


    public function index($username,$pass)
    {
$this->user_name="umer_muc";
$this->user_password="090078601Umer";
//dd($username);
//        dd($pass);

$this->dispatch(new InstaStart($username,$pass));
$instagram = \InstagramScraper\Instagram::withCredentials(new \GuzzleHttp\Client(), $this->user_name, $this->user_password, new Psr16Adapter('Files'));
$instagram->setUserAgent('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:78.0) Gecko/20100101 Firefox/78.0');
$in=$instagram->login();  // will use cached session if you want to force login $instagram->login(true)
$instagram->saveSession();  //DO NOT forget this in order to save the session, otherwise have no sense
$this->instagram_details=$instagram;
$this->session_id=$in['sessionid'];
$this->user_id=$in['ds_user_id'];


InstagramController::startscrapping();

}


public function startscrapping()
{
   InstagramController::generateExecution();
//   InstagramController::AccountById($this->user_id);
//   InstagramController::AccountFollowers($this->user_id);
//   InstagramController::AccountFollowing($this->user_id);
//   InstagramController::AccountMediaByUsername('rtech_97');
//
//
//
//    InstagramController::getSecretAdmirer();
//    InstagramController::getUnfollowfollowers();
//    InstagramController::getUnfollowfollowing();
//    InstagramController::FollowerAttributes();
//
//
//    InstagramController::SaveProcessingTable();

    echo "<p></b>Check the database for the whole result </b></p>";

}

public function generateExecution()
{
$execution=  Executions::latest()->get();

if(count($execution)==0 )
{
    $execution=    Executions::create([
        'execution_id'=>0,

    ]);

}

    $execution=$execution->first();
$e=$execution->execution_id;
$this->execution_id=$e+1;
//dd($this->execution_id);
}



public function AccountById($id)
 {
    $account = $this->instagram_details->getAccountById($id);
    $u=  Users::create([
    'username'=>$this->user_name,
    'password'=>$this->user_password,
    'execution_id'=>$this->execution_id,
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
    $this->user=$u;
      //  dd($u->_id);
    echo "<p>Account information is being Stored in table! <b>Username</b>:{$account->getUsername()}</p>";
//    echo "<p>Id: {$account->getId()}\n</p>";
//    echo "<p>Username: {$account->getUsername()}\n</p>";
//    echo "<p>Full name: {$account->getFullName()}\n</p>";
//    echo "<p>Biography: {$account->getBiography()}\n</p>";
//    echo "<p>Profile picture url: {$account->getProfilePicUrl()}\n</p>";
//    echo '<p><img src="'.$account->getProfilePicUrl().'"/></p>';
//    echo "<p>External link: {$account->getExternalUrl()}\n</p>";
//    echo "<p>Number of published posts: {$account->getMediaCount()}\n</p>";
//    echo "<p>Number of followers: {$account->getFollowedByCount()}\n</p>";
//    echo "<p>Number of follows: {$account->getFollowsCount()}\n</p>";
//    echo "<p>Is private: {$account->isPrivate()}\n</p>";
//    echo "<p>Is verified: {$account->isVerified()}\n</p>";

 }
 public function FollowersCount($id)
{
  $account = $this->instagram_details->getAccountById($id);
  return $account->getFollowedByCount();
 }

 public function FollowingCount($id)
  {
   $account = $this->instagram_details->getAccountById($id);
   return $account->getFollowsCount();
  }

 public function AccountFollowers($id)
 {
  $followers_l = [];
  $instagram = $this->instagram_details;
  $count=InstagramController::FollowersCount($id);
  sleep(2); // Delay to mimic user
$followers = [];
//echo $count;
$followers = $instagram->getFollowers($id, $count, $count, true);
//echo '<pre>' . json_encode($followers, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . '</pre>';

  //Make the Following List for check that it is the followers back or not
     $c=InstagramController::FollowingCount($id);
     sleep(2); // Delay to mimic user
     $f = [];
     $F_ing=[];
     $f = $instagram->getFollowing($id, $c, $c, true);
     $i=0;
     if($f!=NULL)
     {
         for ($i = 0; $i < count($f['accounts']); $i++)
         {
//             echo '<p>'. $f['accounts'][$i]['id'];
             array_push($F_ing,$f['accounts'][$i]['id']);
         }
     $this->following_list=$F_ing;
     }


     for ($i = 0; $i < count($followers); $i++)
     {
        array_push($followers_l,$followers[$i]['id']);
        $following_back=0;
        if (in_array($followers[$i]['id'],$F_ing))
         {
             $following_back=1;
         }

Followers::create([
'instagram_id'=>$followers[$i]['id'],
    'execution_id'=>$this->execution_id,
'username'=>$followers[$i]['username'],
'name'=>$followers[$i]['full_name'],
'user_id'=>$this->user->_id,
'following_back'=>$following_back,
    'is_newfollowers'=>0,
    'is_topfollowers'=>0,
    'is_bestfollowers'=>0,
    'is_worstfollowers'=>0,
    'is_ghostfollowers'=>0,

]);

 }
$this->followers_list=$followers_l;
     echo "<p>Account Followers is being Stored in table! <b>Followers Count</b>:{$count}</p>";
}

public function AccountFollowing($id)
 {
  $instagram = $this->instagram_details;
  $count=InstagramController::FollowingCount($id);

  sleep(2); // Delay to mimic user

$followers = [];
$followers = $instagram->getFollowing($id, $count, $count, true); // Get 1000 followings of 'kevin', 100 a time with random delay between requests
//echo '<pre>' . json_encode($followers, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . '</pre>';
    $i=0;
//    var_dump($followers);
//    echo count($followers['accounts']);
    if($followers!=NULL)
    {

    for ($i = 0; $i < count($followers['accounts']); $i++){



        $follow_back=0;
        if (in_array($followers['accounts'][$i]['id'], $this->followers_list))
        {
            $follow_back=1;
        }
Followings::create([
    'instagram_id'=>$followers['accounts'][$i]['id'],
    'execution_id'=>$this->execution_id,
    'username'=>$followers['accounts'][$i]['username'],
    'name'=>$followers['accounts'][$i]['full_name'],
    'user_id'=>$this->user->_id,
     'follow_back'=>$follow_back,

]);

}}

    echo "<p>Account Following is being Stored in table!<b> Followers Count:</b>{$count}</p>";
 }

 public function MediaCount($id)
  {
   $account = $this->instagram_details->getAccountById($id);
   return $account->getMediaCount();
  }


 public function AccountMediaByUsername($username)
 {
  $instagram = $this->instagram_details->getAccount($username);
  $count=InstagramController::MediaCount($instagram->getId());
  $medias = $instagram->getMedias($instagram, $count);
  $comment_code_list=[];
  foreach($medias as $media){
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
      $m=Medias::create([
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
//    echo "<p>Media info:\n</p>";
//    echo "<p>Id: {$media->getId()}\n</p>";
//    echo "<p>Shortcode: {$media->getShortCode()}\n</p>";
//    echo "<p>Created at: {$d	}\n</p>";
//    echo "<p>Caption: {$media->getCaption()}\n</p>";
//    echo "<p>Number of comments: {$media->getCommentsCount()}</p>";
//    echo "<p>Number of likes: {$media->getLikesCount()}</p>";
//    echo '<p><img src="'.$media->getLink().'"/></p>';
//    echo '<p><img src="'.$media->getImageHighResolutionUrl().'"/></p>';
//    echo "<p>Media type (video or image): {$media->getType()}</p>";
//    $account = $media->getOwner();
//    echo "<p>Account info:\n</p>";
//    echo "<p>Id: {$instagram->getId()}\n</p>";
//    echo "<p>Username: {$instagram->getUsername()}\n</p>";
//    echo "<p>Full name: {$instagram->getFullName()}\n</p>";
//    echo "<p>Profile pic url: {$instagram->getProfilePicUrl()}\n</p>";

      $comments = $this->instagram_details->getMediaCommentsByCode( $media->getShortCode(), 2, $maxId = null);

      foreach($comments as $comment){
          $account = $comment->getOwner();
          $is_followers=0;
          if (in_array($account->getId(), $this->followers_list))
          {
              $is_followers=1;
          }
//          echo "<p>Comment info: \n";
//          echo "<p>Id: {$comment->getId()}\n";
//          echo "<p>Created at: {$comment->getCreatedAt()}\n";
//          echo "<p>Comment text: {$comment->getText()}\n";
//
//          echo "<p>Comment owner: \n";
//          echo "<p>Id: {$account->getId()}";
//          echo "<p>Username: {$account->getUsername()}";
//          echo "<p>Profile picture url: {$account->getProfilePicUrl()}\n";
array_push($comment_code_list,$comment->getId());
          Comment::create([
              'username'=>$account->getUsername(),
              'follower_id'=>$is_followers,
              'execution_id'=>$this->execution_id,
              'media_id'=>$m->_id,
              'comment_code'=>$comment->getId(),
              'comments'=>$comment->getText(),
              'is_deleted'=>0,
          ]);


      }

      InstagramController::MediaLikes($media,$m);

  }
     echo "<p>All <b>Comments and likes</b> of all media is being Stored in table!</p>";
    $this->commentCode= $comment_code_list;
 }

    public function MediaLikes($media,$m)
    {
        $likes = $this->instagram_details->getMediaLikesByCode($media->getShortCode(), $media->getLikesCount(), $maxId = null);

        foreach ($likes as $like) {

            $is_followers = 0;
            if (in_array($like->getId(), $this->followers_list)) {
                $is_followers = 1;
            }
//            echo "<p>Like info: \n";
//            echo "<p>Id: {$like->getId()}\n";
//            echo "<p>Username: {$like->getUsername()}";
//            echo "<p>Profile picture url: {$like->getProfilePicUrl()}\n";

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


        public function SaveProcessingTable()
 {
     $execution_id=0;
     Executions::create([
        'user_id'=>$this->user->_id,
         'execution_id'=>$this->execution_id,
        'followers_list'=>json_encode($this->followers_list),
        'following_list'=>json_encode($this->following_list),
         'username'=>$this->user_name,
      ]);

}

public function getSecretAdmirer()
{
 $like=   Likes::where('follower_id',0)->where('execution_id',$this->execution_id)->get()->pluck('username')->toArray();
 $comment=Comment::where('follower_id',0)->where('execution_id',$this->execution_id)->groupBy('username')->get()->pluck('username')->toArray();
 $l=array_merge($like,$comment);
$username=array_unique($l);
$medias_id=[];
foreach($username as $u) {
    $li = Likes::where('follower_id', 0)->where('execution_id', $this->execution_id)->where('username', $u)->get();
    $co = Comment::where('follower_id', 0)->where('execution_id', $this->execution_id)->where('username', $u)->get();
    foreach ($li as $l) {
        array_push($medias_id, $l->media_id);
    }
    foreach ($co as $c) {
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

            $sid=SecretAdmirer::create([
                'username'=>$u,
                'media_id'=>$m,
                'user_id'=>$us_id,
                'name'=>$u,
                '$is_like'=>$is_like,
                'comment_count'=>$comment_count,
                'execution_id'=>$this->execution_id,
]);
//dd($sid);
        }
}
}

    echo "<p>Get all the <b>Secret Admirer</b></p>";
}


public function getUnfollowfollowers()
{

    $executions=  Executions::where('username',$this->user_name)->get();
    if(count($executions)>1) {
$execution=  Executions::where('username',$this->user_name)->latest()->first();


$oldFollowers=explode(" ",$execution->followers_list);
$newFollowers=$this->followers_list;

$Unfollowers=array_diff($oldFollowers,$newFollowers);


foreach($Unfollowers as $u) {
    $f = Followers::where('_id', $u)->get()->first();
    UnfollowedFollowers::create([
        'username' => $f->username,
        'user_id' => $f->user_id,
        'name' => $f->name,
        'execution_id'=>$this->execution_id,
    ]);
}



        echo "<p>Get all the <b>Unfollow Followers</b></p>";
//return $Unfollowers;
//dd($newFollowers);
    }
    else{
        echo "<p>Result of<b> Unfollow followers</b> will be shown after someday or in next Execution</p>";
    }
}

public function getUnfollowfollowing()
{
    $executions=  Executions::where('username',$this->user_name)->get();
    if(count($executions)>1) {
        $execution = Executions::where('username', $this->user_name)->latest()->first();

        $oldFollowing = explode(" ",$execution->following_list);
        $newFollowing = $this->following_list;
        $Unfollowing = array_diff($oldFollowing, $newFollowing);

        foreach($Unfollowing as $u) {
            $f = Followings::where('_id', $u)->get()->first();
            UnfollowedFollowing::create([
                'username' => $f->username,
                'user_id' => $f->user_id,
                'name' => $f->name,
                'execution_id'=>$this->execution_id,
            ]);
        }
        echo "<p>Get all the <b>Unfollow Following</b></p>";
//        return $Unfollowing;
//        dd($Unfollowing);
    }
    else{
        echo "<p>Result of <b>Unfollow following</b> will be shown after someday or in next Execution</p>";
    }

}

    public function getGainFollowers()
    {
        $executions=  Executions::where('username',$this->user_name)->get();
        if(count($executions)>1) {
            $execution=  Executions::where('username',$this->user_name)->latest()->first();

            $oldFollowers=explode(" ",$execution->followers_list);
            $newFollowers=$this->followers_list;

            $gainfollowers=array_diff($newFollowers,$oldFollowers);

            return $gainfollowers;
            dd($gainfollowers);
        }
        else{
            echo "Result will be shown after sometime";
        }

    }

    public function growth()
    {
        $loss=count(InstagramController::getUnfollowfollowers());
        $gain=count(InstagramController::getGainFollowers());

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

        foreach ($followers as $f) {

            $l = count(Likes::where('follower_id',   1)->where('execution_id',$execution)->where('username', 'rtech_97')->where('is_unlike',0)->get());
            $c = count(Comment::where('follower_id', 1)->where('execution_id',$execution)->where('username', 'rtech_97')->where('is_deleted',0)->get());
            $e_cal=0;

            if($this->user->posts!=0) {
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

               if($i<10) {

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
//    dd($execution);
$oldComment=Comment::where('execution_id',$execution->execution_id)->get()->pluck('comment_code');
$newComment=Comment::where('execution_id',$newExecution)->get()->pluck('comment_code');


        $deleted=array_diff($oldComment,$newComment);
        foreach($deleted as $d)
        {
            $comm=Comment::where('comment_code',$d)->where('execution_id',$execution->execution_id)->get()->first();

            Comment::create([
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

           Likes::create([
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


public function getRequests()
{


}

public function getStalker()
{



}







//-----------------------------------------------------------------------------------------------------------------------------------------

//
// public function addDeleteComment()
// {
//    $instagram = \InstagramScraper\Instagram::withCredentials(new \GuzzleHttp\Client(), 'username', 'password', new Psr16Adapter('Files'));
//    $instagram->login();
//
//    try {
//        // add comment to post
//        $mediaId = '1663256735663694497';
//        $comment = $instagram->addComment($mediaId, 'Text 1');
//        // replied to comment
//        $instagram->addComment($mediaId, 'Text 2', $comment);
//
//        $instagram->deleteComment($mediaId, $comment);
//    } catch (InstagramException $ex) {
//        echo $ex->getMessage();
//    }
//
// }
//
// public function convertShortcode()
// {
//
//
//    echo 'Shortcode: ' . Media::getCodeFromId('1270593720437182847_3') . "\n"; // Shortcode: BGiDkHAgBF_
//    echo 'Shortcode: ' . Media::getCodeFromId('1270593720437182847') . "\n"; // Shortcode: BGiDkHAgBF_
//
//    // And you can get link to media: instagram.com/p/BGiDkHAgBF_
//
//    echo 'Media id: ' . Media::getIdFromCode('BGiDkHAgBF_'); // Media id: 1270593720437182847
//
// }
//
// public function followUnfollow()
// {
//    $instagram  = Instagram::withCredentials(new \GuzzleHttp\Client(), 'login', 'password', new Psr16Adapter('Files'));
//    $instagram->login();
//    $instagram->saveSession();
//
//    $account    = $instagram->getAccount("username");
//
//    $instagram->follow($account->getId());
//
//    sleep(10);
//
//    $instagram->unfollow($account->getId());
//
// }
//
//
//
// public function AccountByUsername()
// {
//    $instagram = new \InstagramScraper\Instagram(new \GuzzleHttp\Client());
//
//    // For getting information about account you don't need to auth:
//
//    $account = $instagram->getAccount('kevin');
//
//    // Available fields
//    echo "Account info:\n";
//    echo "Id: {$account->getId()}\n";
//    echo "Username: {$account->getUsername()}\n";
//    echo "Full name: {$account->getFullName()}\n";
//    echo "Biography: {$account->getBiography()}\n";
//    echo "Profile picture url: {$account->getProfilePicUrl()}\n";
//    echo "External link: {$account->getExternalUrl()}\n";
//    echo "Number of published posts: {$account->getMediaCount()}\n";
//    echo "Number of followers: {$account->getFollowsCount()}\n";
//    echo "Number of follows: {$account->getFollowedByCount()}\n";
//    echo "Is private: {$account->isPrivate()}\n";
//    echo "Is verified: {$account->isVerified()}\n";
//
// }
//
//
//
//
//
//  public function CurrentMediaByLocationId()
// {
//
//
//$instagram = \InstagramScraper\Instagram::withCredentials(new \GuzzleHttp\Client(), 'user', 'passwd', new Psr16Adapter('Files'));
//$instagram->login();
//
//$medias = $instagram->getCurrentTopMediasByLocationId('116231');
//$media = $medias[0];
//
//
//$seperator = PHP_SAPI === 'cli' ? "\n" : "<br>\n";
//echo "Media info:$seperator";
//echo "Id: {$media->getId()}$seperator";
//echo "Shortcode: {$media->getShortCode()}$seperator";
//echo "Created at: {$media->getCreatedTime()}$seperator";
//echo "Caption: {$media->getCaption()}$seperator";
//echo "Number of comments: {$media->getCommentsCount()}";
//echo "Number of likes: {$media->getLikesCount()}";
//echo "Get link: {$media->getLink()}";
//echo "High resolution image: {$media->getImageHighResolutionUrl()}";
//echo "Media type (video or image): {$media->getType()}";
//$account = $media->getOwner();
//echo "Account info:$seperator";
//echo "Id: {$account->getId()}$seperator";
//echo "Username: {$account->getUsername()}$seperator";
//echo "Full name: {$account->getFullName()}$seperator";
//echo "Profile pic url: {$account->getProfilePicUrl()}$seperator";
//
//echo "<br>";
//echo "Location Name: {$media->getLocationName()}$seperator";
//echo "Location Slug: {$media->getLocationSlug()}$seperator";
// }
// public function CurrentMediaByTagName()
// {
//    $instagram = \InstagramScraper\Instagram::withCredentials(new \GuzzleHttp\Client(), 'username', 'password', new Psr16Adapter('Files'));
//    $instagram->login();
//
//    $medias = $instagram->getCurrentTopMediasByTagName('youneverknow');
//    $media = $medias[0];
//    echo "Media info:\n";
//    echo "Id: {$media->getId()}\n";
//    echo "Shortcode: {$media->getShortCode()}\n";
//    echo "Created at: {$media->getCreatedTime()}\n";
//    echo "Caption: {$media->getCaption()}\n";
//    echo "Number of comments: {$media->getCommentsCount()}";
//    echo "Number of likes: {$media->getLikesCount()}";
//    echo "Get link: {$media->getLink()}";
//    echo "High resolution image: {$media->getImageHighResolutionUrl()}";
//    echo "Media type (video or image): {$media->getType()}";
//    $account = $media->getOwner();
//    echo "Account info:\n";
//    echo "Id: {$account->getId()}\n";
//    echo "Username: {$account->getUsername()}\n";
//    echo "Full name: {$account->getFullName()}\n";
//    echo "Profile pic url: {$account->getProfilePicUrl()}\n";
//
// }
//
//
// public function getFeed()
// {
//
//
//$instagram  = Instagram::withCredentials(new \GuzzleHttp\Client(), 'login', 'password', new Psr16Adapter('Files'));
//$instagram->login();
//$instagram->saveSession();
//
//$posts  = $instagram->getFeed();
//
//foreach ($posts as $post){
//    echo $post->getImageHighResolutionUrl()."\n";
//}
// }
// public function getHighlights()
// {
//     $settings=json_decode(file_get_contents('settings.json'));
//$username = $settings->username;
//$password = $settings->password;
//
//$instagram = \InstagramScraper\Instagram::withCredentials(new \GuzzleHttp\Client(), $username, $password, new Psr16Adapter('Files'));
//$instagram->login();
//
////$userId = $instagram->getAccount('instagram')->getId();
//$userId = 556625332;
//echo "<pre>";
//
//$highlights = $instagram->getHighlights($userId);
//$hcount=0;
//foreach ($highlights as $highlight) {
//    $hcount++;
//    echo "\n------------------------------------------------------------------------------------------------------------------------\n";
//    echo "Highlight info ($hcount):\n";
//    echo "<img width=80 src='".$highlight->getImageThumbnailUrl()."'>\n";
//    echo "Id: {$highlight->getId()}\n";
//    echo "Title: {$highlight->getTitle()}\n";
//    echo "Image thumbnail url: {$highlight->getImageThumbnailUrl()}\n";
//    echo "Image cropped thumbnail url: {$highlight->getImageCroppedThumbnailUrl()}\n";
//    echo "Owner Id: {$highlight->getOwnerId()}\n";
//    $account = $highlight->getOwner();
//    echo "Account info:\n";
//    echo " Id: {$account->getId()}\n";
//    echo " Username: {$account->getUsername()}\n";
//    echo " Profile pic url: {$account->getProfilePicUrl()}\n";
//
//    echo "------------------------------------------------------------------------------------------------------------------------\n";
//
//    $userStories=$instagram->getHighlightStories($highlight->getId());
//    for ($i=0; $i<count($userStories);$i++)
//    {
//      $stories = $userStories[$i]->getStories();
//      //$owner   = $userStories[$i]->getOwner();
//      //echo "\n===========================================================";
//      //echo "\nUserStorie: " . $i;
//      //echo "\nId:         " . $owner['id'];
//      //echo "\nUserName:   " . $owner['username'];
//
//      //for each stories => get Story
//      for ($j=0; $j<count($stories);$j++)
//      {
//          $story = $stories[$j];
//          echo "\n--------------------------------------------------------";
//          echo "\nStorie:         " . $j;
//          echo "\nId:             " . $story['id'];
//          echo "\nCreation Time:  " . $story['createdTime'];
//          echo "\nType:           " . $story['type'];
//          echo "\n<img height=100 src=\"".$story['imageThumbnailUrl']."\">";
//
//      }
//    }
//
//}
//echo "</pre>";
// }
// public function getLocationById()
// {
//
//    $instagram = \InstagramScraper\Instagram::withCredentials(new \GuzzleHttp\Client(), 'username', 'password', new Psr16Adapter('Files'));
//    $instagram->login();
//
//    // Location id from facebook
//    $location = $instagram->getLocationById(1);
//
//    echo "Location info: \n";
//    echo "Id: {$location->getId()}\n";
//    echo "Name: {$location->getName()}\n";
//    echo "Latitude: {$location->getLat()}\n";
//    echo "Longitude: {$location->getLng()}\n";
//    echo "Slug: {$location->getSlug()}\n";
//    echo "Is public page available: {$location->getHasPublicPage()}\n";
//
// }
//
// public function getMediaByCode()
// {
//
//
//// If account is public you can query Instagram without auth
//$instagram = new \InstagramScraper\Instagram(new \GuzzleHttp\Client());
//
//// If account is private and you subscribed to it, first login
//$instagram = \InstagramScraper\Instagram::withCredentials(new \GuzzleHttp\Client(), 'username', 'password', new Psr16Adapter('Files'));
//$instagram->login();
//
//$media = $instagram->getMediaByCode('BHaRdodBouH');
//
//echo "Media info:\n";
//echo "Id: {$media->getId()}\n";
//echo "Shortcode: {$media->getShortCode()}\n";
//echo "Created at: {$media->getCreatedTime()}\n";
//echo "Caption: {$media->getCaption()}\n";
//echo "Number of comments: {$media->getCommentsCount()}";
//echo "Number of likes: {$media->getLikesCount()}";
//echo "Get link: {$media->getLink()}";
//echo "High resolution image: {$media->getImageHighResolutionUrl()}";
//echo "Media type (video or image): {$media->getType()}";
//$account = $media->getOwner();
//echo "Account info:\n";
//echo "Id: {$account->getId()}\n";
//echo "Username: {$account->getUsername()}\n";
//echo "Full name: {$account->getFullName()}\n";
//echo "Profile pic url: {$account->getProfilePicUrl()}\n";
// }
// public function getMediaById()
// {
//
//// If account is public you can query Instagram without auth
//$instagram = new \InstagramScraper\Instagram(new \GuzzleHttp\Client());
//
//// If account is private and you subscribed to it, first login
//$instagram = \InstagramScraper\Instagram::withCredentials(new \GuzzleHttp\Client(), 'username', 'password', new Psr16Adapter('Files'));
//$instagram->login();
//
//$media = $instagram->getMediaById('1270593720437182847');
//echo "Media info:\n";
//echo "Id: {$media->getId()}\n";
//echo "Shortcode: {$media->getShortCode()}\n";
//echo "Created at: {$media->getCreatedTime()}\n";
//echo "Caption: {$media->getCaption()}\n";
//echo "Number of comments: {$media->getCommentsCount()}";
//echo "Number of likes: {$media->getLikesCount()}";
//echo "Get link: {$media->getLink()}";
//echo "High resolution image: {$media->getImageHighResolutionUrl()}";
//echo "Media type (video or image): {$media->getType()}";
//$account = $media->getOwner();
//echo "Account info:\n";
//echo "Id: {$account->getId()}\n";
//echo "Username: {$account->getUsername()}\n";
//echo "Full name: {$account->getFullName()}\n";
//echo "Profile pic url: {$account->getProfilePicUrl()}\n";
//
// }
//
// public function getMediaByUrl()
// {
//
//// If account is public you can query Instagram without auth
//$instagram = new \InstagramScraper\Instagram(new \GuzzleHttp\Client());
//
//// If account is private and you subscribed to it, first login
//$instagram = \InstagramScraper\Instagram::withCredentials(new \GuzzleHttp\Client(), 'username', 'password', new Psr16Adapter('Files'));
//$instagram->login();
//
//$media = $instagram->getMediaByUrl('https://www.instagram.com/p/BHaRdodBouH');
//echo "Media info:\n";
//echo "Id: {$media->getId()}\n";
//echo "Shortcode: {$media->getShortCode()}\n";
//echo "Created at: {$media->getCreatedTime()}\n";
//echo "Caption: {$media->getCaption()}\n";
//echo "Number of comments: {$media->getCommentsCount()}";
//echo "Number of likes: {$media->getLikesCount()}";
//echo "Get link: {$media->getLink()}";
//echo "High resolution image: {$media->getImageHighResolutionUrl()}";
//echo "Media type (video or image): {$media->getType()}";
//$account = $media->getOwner();
//echo "Account info:\n";
//echo "Id: {$account->getId()}\n";
//echo "Username: {$account->getUsername()}\n";
//echo "Full name: {$account->getFullName()}\n";
//echo "Profile pic url: {$account->getProfilePicUrl()}\n";
//
// }
//
// public function getMediaByComment()
// {
//
//
//$instagram = \InstagramScraper\Instagram::withCredentials(new \GuzzleHttp\Client(), 'username', 'password', new Psr16Adapter('Files'));
//$instagram->login();
//
//// Get media comments by shortcode
//$comments = $instagram->getMediaCommentsByCode('BG3Iz-No1IZ', 8000);
//
//// or by id
//$comments = $instagram->getMediaCommentsById('1130748710921700586', 10000);
//
//// Let's take first comment in array and explore available fields
//$comment = $comments[0];
//
//echo "Comment info: \n";
//echo "Id: {$comment->getId()}\n";
//echo "Created at: {$comment->getCreatedAt()}\n";
//echo "Comment text: {$comment->getText()}\n";
//$account = $comment->getOwner();
//echo "Comment owner: \n";
//echo "Id: {$account->getId()}";
//echo "Username: {$account->getUsername()}";
//echo "Profile picture url: {$account->getProfilePicUrl()}\n";
//
//// You can start loading comments from specific comment by providing comment id
//$comments = $instagram->getMediaCommentsByCode('BG3Iz-No1IZ', 200, $comment->getId());
//
// }
// public function getMediaByLocationId()
// {
//
//
//$instagram = \InstagramScraper\Instagram::withCredentials(new \GuzzleHttp\Client(), 'user', 'passwd', new Psr16Adapter('Files'));
//$instagram->login();
//
//$medias = $instagram->getMediasByLocationId('116231', 20);
//$media = $medias[0];
//
//$seperator = PHP_SAPI === 'cli' ? "$\n" : "<br>\n";
//echo "Media info:$seperator";
//echo "Id: {$media->getId()}$seperator";
//echo "Shortcode: {$media->getShortCode()}$seperator";
//echo "Created at: {$media->getCreatedTime()}$seperator";
//echo "Caption: {$media->getCaption()}$seperator";
//echo "Number of comments: {$media->getCommentsCount()}";
//echo "Number of likes: {$media->getLikesCount()}";
//echo "Get link: {$media->getLink()}";
//echo "High resolution image: {$media->getImageHighResolutionUrl()}";
//echo "Media type (video or image): {$media->getType()}";
//$account = $media->getOwner();
//echo "Account info:$seperator";
//echo "Id: {$account->getId()}$seperator";
//echo "Username: {$account->getUsername()}$seperator";
//echo "Full name: {$account->getFullName()}$seperator";
//echo "Profile pic url: {$account->getProfilePicUrl()}$seperator";
//
//echo "<br>";
//echo "Location Name: {$media->getLocationName()}$seperator";
//echo "Location Slug: {$media->getLocationSlug()}$seperator";
// }
//
// public function getMediaByTag()
// {
//
//    $instagram = \InstagramScraper\Instagram::withCredentials(new \GuzzleHttp\Client(), 'username', 'password', new Psr16Adapter('Files'));
//    $instagram->login();
//
//    $medias = $instagram->getMediasByTag('youneverknow', 20);
//    $media = $medias[0];
//    echo "Media info:\n";
//    echo "Id: {$media->getId()}\n";
//    echo "Shortcode: {$media->getShortCode()}\n";
//    echo "Created at: {$media->getCreatedTime()}\n";
//    echo "Caption: {$media->getCaption()}\n";
//    echo "Number of comments: {$media->getCommentsCount()}";
//    echo "Number of likes: {$media->getLikesCount()}";
//    echo "Get link: {$media->getLink()}";
//    echo "High resolution image: {$media->getImageHighResolutionUrl()}";
//    echo "Media type (video or image): {$media->getType()}";
//    $account = $media->getOwner();
//    echo "Account info:\n";
//    echo "Id: {$account->getId()}\n";
//    echo "Username: {$account->getUsername()}\n";
//    echo "Full name: {$account->getFullName()}\n";
//    echo "Profile pic url: {$account->getProfilePicUrl()}\n";
//
// }
// public function getPaginateFeed()
// {
//
//    ini_set('display_errors', 1);ini_set('display_startup_errors', 1);error_reporting(E_ALL);
//$seperator = PHP_SAPI === 'cli' ? "\n" : "\n<br>";
//
//if (PHP_SAPI === 'cli') {
//  $seperator = "\n";
//  $c1 = '';
//  $c2 = '';
//  $c3 = '';
//  $cend = '';
//}
//else {
//  $seperator = "\n";
//  $c1 = '<label style="color:red;">';
//  $c2 = '<label style="color:green;">';
//  $c3 = '<label style="color:blue;">';
//  $cend = '</label>';
//}
//
//
//$seperator = PHP_SAPI === 'cli' ? "\n" : "<br>\n";
//$instagram = \InstagramScraper\Instagram::withCredentials(new \GuzzleHttp\Client(), 'username', 'passwd', new Psr16Adapter('Files'));
//$instagram->login();
//$instagram->saveSession(3600*24);
//
//
//// getFeed has 4 arguments:
//// - Nr of medias
//// - maxId (cursor)
//// - nr of comments
//// - nr of likes => dows not seem to do anything
//$medias = $instagram->getFeed(50,'',6,4);
//echo "${c1}Test getFeed():${cend}";
//display_media($medias);
//echo "${seperator}${seperator}";
//
//
//echo "${seperator}==========================================================================================";
//echo "${seperator}${c1}Test getPaginateFeed():${cend}";
//$maxId       = null;
//$hasNextPage = true;
//$count_fetch = 0;
//while ($hasNextPage)
//{
//  $count_fetch++;
//  if ($count_fetch>3) break;
//
//  $arr = $instagram->getPaginateFeed(10, $maxId, 6, 4);
//  $maxId            = $arr['maxId'];
//  $hasNextPage      = $arr['hasNextPage'];
//
//  echo "${seperator}+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++";
//  echo "${seperator}${c2}Fetch page: " . $count_fetch."${cend}";
//  echo "${seperator}";
//  echo "${seperator}Media Count: " . $arr['media_count'];
//  echo ", Storie Count: " . $arr['storie_count'];
//  display_media($arr['medias']);
//  echo "${seperator}................................................................................";
//  display_story($arr['userStories']);
//  echo "${seperator}";
//
//}
//
//function display_media($medias)
//{
//  global $seperator, $c1, $c2, $c3, $cend;
//  echo "${seperator}Nr of ${c3}media${cend} page: ".count($medias);
//  echo "${seperator}First media Id     :  " . $medias[0]->getId();
//  echo "${seperator}First media Creates:  " . $medias[0]->getCreatedTime();
//  echo "${seperator}First media Caption:  " . $medias[0]->getCaption();
//
//  if (PHP_SAPI === 'cli') echo "${seperator}First media image:  " . $medias[0]->getImageHighResolutionUrl();
//  else echo "${seperator}<img height=100 src='" . $medias[0]->getImageHighResolutionUrl() . "'>\n";
//}
//
//function display_story($userStories)
//{
//  global $seperator, $c1, $c2, $c3, $cend;
//  echo "${seperator}Nr of ${c3}userStories${cend} page: ".count($userStories);
//  if (count($userStories)>0)
//  {
//    echo "${seperator}First userStory expiringAt     :  " . gmdate("Y-m-d H:i:s", $userStories[0]->getExpiringAt());
//    echo " - " . floor(($userStories[0]->getExpiringAt()-time())/3600) . "h";
//    echo " " . floor((($userStories[0]->getExpiringAt()-time())%3600)/60) . "m ";
//    echo "${seperator}First userStory last mutation:  " . gmdate("Y-m-d H:i:s", $userStories[0]->getLastMutatedAt());
//    echo " - " . floor(($userStories[0]->getLastMutatedAt()-time())/3600) . "h";
//
//    echo "${seperator}First userStory user Id:  " . $userStories[0]->getOwner()->getId();
//    echo "${seperator}First userStory user Name:  " . $userStories[0]->getOwner()->getUsername();
//    echo "${seperator}${seperator}Note: no content of story, only the fact that a story of this user is present";
//    echo "${seperator}Fetch storie(s) with command:";
//    echo " '\$Instagram->getStories(".$userStories[0]->getOwner()->getId().");'";
//  }
//}
//
// }
// public function getPaginateMediaComment()
// {
//
//    ini_set('display_errors', 1);ini_set('display_startup_errors', 1);error_reporting(E_ALL);
//
//$seperator = PHP_SAPI === 'cli' ? "\n" : "<br>\n";
//$instagram = \InstagramScraper\Instagram::withCredentials(new \GuzzleHttp\Client(), 'user', 'passwd', new Psr16Adapter('Files'));
//$instagram->login();
//
//
//// You can start loading comments from specific comment by providing comment id
//// $comments = $instagram->getPaginateMediaCommentsByCode('BG3Iz-No1IZ', 200, $comment->getId());
////
//// However this results in a never ending loop of commentsCount
//$mediaCode='CHiRTyDHgkc';
//$comments = $instagram->getMediaCommentsByCode($mediaCode, 1000);
//echo "Total nr of comments: ".count($comments);
//
//$pageSize=floor(count($comments)/2)+2;
//$comments = $instagram->getMediaCommentsByCode($mediaCode, $pageSize);
//echo "${seperator}Nr of comments page 1: ".count($comments);
//$comments = $instagram->getMediaCommentsByCode($mediaCode, $pageSize);
//echo "${seperator}Nr of comments page 2: ".count($comments);
//$comments = $instagram->getMediaCommentsByCode($mediaCode, $pageSize);
//echo "${seperator}Nr of comments page 3: ".count($comments);
//echo "${seperator}${seperator}Not working right: 3x page > total";
//// ...
//
//
//$maxId       = null;
//$hasPrevious = true;
//$counter     = 0;
//while ($hasPrevious)
//{
//  $counter++;
//  $PaginateComments = $instagram->getPaginateMediaCommentsByCode($mediaCode, $pageSize, $maxId);
//  $comments         = $PaginateComments->comments;
//  $maxId            = $PaginateComments->maxId;
//  $hasPrevious      = $PaginateComments->hasPrevious;
//
//  echo "${seperator}${seperator}--------------------------------------------------------------------------------";
//  echo "${seperator}Nr of comments page ${counter}: ".count($comments);
//  echo "${seperator}Total comments: " . $PaginateComments->commentsCount;
//  echo "${seperator}Has Previous:   " . $PaginateComments->hasPrevious;
//
//  echo "${seperator}First comment Id     :  " . $PaginateComments->comments[0]->getId();
//  echo "${seperator}First comment Creates:  " . $PaginateComments->comments[0]->getCreatedAt();
//  echo "${seperator}First comment Text   :  " . $PaginateComments->comments[0]->getText();
//
//}
// }
//
//
//
// public function  getPaginateMediasByTag(){
//
//
//$instagram = \InstagramScraper\Instagram::withCredentials(new \GuzzleHttp\Client(), 'username', 'password', new Psr16Adapter('Files'));
//$instagram->login();
//
//$result = $instagram->getPaginateMediasByTag('zara');
//$medias = $result['medias'];
//
//if ($result['hasNextPage'] === true) {
//    $result = $instagram->getPaginateMediasByTag('zara', $result['maxId']);
//    $medias = array_merge($medias, $result['medias']);
//}
//
//echo json_encode($medias);
// }
// public function getPaginateMediasByUsername(){
//
//$instagram = new \InstagramScraper\Instagram(new \GuzzleHttp\Client());
//$response = $instagram->getPaginateMedias('kevin');
//
//foreach ($response['medias'] as $media) {
//    /** @var \InstagramScraper\Model\Media $media */
//
//    echo "Media info:" . PHP_EOL;
//    echo "Id: {$media->getId()}" . PHP_EOL;
//    echo "Shortcode: {$media->getShortCode()}" . PHP_EOL;
//    echo "Created at: {$media->getCreatedTime()}" . PHP_EOL;
//    echo "Caption: {$media->getCaption()}" . PHP_EOL;
//    echo "Number of comments: {$media->getCommentsCount()}" . PHP_EOL;
//    echo "Number of likes: {$media->getLikesCount()}" . PHP_EOL;
//    echo "Get link: {$media->getLink()}" . PHP_EOL;
//    echo "High resolution image: {$media->getImageHighResolutionUrl()}" . PHP_EOL;
//    echo "Media type (video or image): {$media->getType()}" . PHP_EOL . PHP_EOL;
//    $account = $media->getOwner();
//
//    echo "Account info:" . PHP_EOL;
//    echo "Id: {$account->getId()}" . PHP_EOL;
//    echo "Username: {$account->getUsername()}" . PHP_EOL;
//    echo "Full name: {$account->getFullName()}" . PHP_EOL;
//    echo "Profile pic url: {$account->getProfilePicUrl()}" . PHP_EOL;
//    echo  PHP_EOL  . PHP_EOL;
//}
//
//echo "HasNextPage: {$response['hasNextPage']}" . PHP_EOL;
//echo "MaxId: {$response['maxId']}" . PHP_EOL;
//}
//public function getSidecarMediaByUrl(){
//     // If account is public you can query Instagram without auth
//$instagram = new \InstagramScraper\Instagram(new \GuzzleHttp\Client());
//
//// If account is private and you subscribed to it firstly login
//$instagram = \InstagramScraper\Instagram::withCredentials(new \GuzzleHttp\Client(), 'username', 'password', new Psr16Adapter('Files'));
//$instagram->login();
//
//$media = $instagram->getMediaByUrl('https://www.instagram.com/p/BQ0lhTeAYo5');
//echo "Media info:\n";
//printMediaInfo($media);
//}
//
//
//function printMediaInfo(\InstagramScraper\Model\Media $media, $padding = '') {
//    echo "${padding}Id: {$media->getId()}\n";
//    echo "${padding}Shortcode: {$media->getShortCode()}\n";
//    echo "${padding}Created at: {$media->getCreatedTime()}\n";
//    echo "${padding}Caption: {$media->getCaption()}\n";
//    echo "${padding}Number of comments: {$media->getCommentsCount()}\n";
//    echo "${padding}Number of likes: {$media->getLikesCount()}\n";
//    echo "${padding}Get link: {$media->getLink()}\n";
//    echo "${padding}High resolution image: {$media->getImageHighResolutionUrl()}\n";
//    echo "${padding}Media type (video/image/sidecar): {$media->getType()}\n";
//}
//
//
//public function getStories(){
//
//$settings=json_decode(file_get_contents('settings.json'));
//$username = $settings->username;
//$password = $settings->password;
//
//$instagram = \InstagramScraper\Instagram::withCredentials(new \GuzzleHttp\Client(), $username, $password, new Psr16Adapter('Files'));
//$instagram->login();
//
//$stories = $instagram->getStories();
//print_r($stories);
//}
//public function getStoriesFromUserStories(){
//
//
//    ini_set('display_errors', 1);ini_set('display_startup_errors', 1);error_reporting(E_ALL);
//
//
//
//$settings=json_decode(file_get_contents('settings.json'));
//$username = $settings->username;
//$password = $settings->password;
//
//$instagram = \InstagramScraper\Instagram::withCredentials(new \GuzzleHttp\Client(), $username, $password, new Psr16Adapter('Files'));
//$instagram->login();
//$instagram->saveSession(10*24*3600);
//
//
//// *************************** get storie from unsetStories **********************************
///* getStories returns the following imap_fetchstructure (class Story extends Media, can be treated as Media, however no 'owner' in Stories -> owner in UserStories)
//- Array
//  - UserStories
//    - Owner
//    - Stories
//      - Story
//      - Story
//      - Story
//      - ....
//  - UserStories
//    - Owner
//    - Stories
//      - Story
//      - Story
//      - Story
//      - ....
//  - .....
//*/
//// Get userStories
//$userStories = $instagram->getStories();
//
////For each userStorie => get Stories
//for ($i=0; $i<count($userStories);$i++)
//{
//  $stories = $userStories[$i]->getStories();
//  $owner   = $userStories[$i]->getOwner();
//  echo "\n<br>===========================================================";
//  echo "\n<br>UserStorie: " . $i;
//  echo "\n<br>Id:         " . $owner['id'];
//  echo "\n<br>UserName:   " . $owner['username'];
//
//  //for each stories => get Story
//  for ($j=0; $j<count($stories);$j++)
//  {
//      $story = $stories[$j];
//      echo "\n<br>--------------------------------------------------------";
//      echo "\n<br>Storie:         " . $j;
//      echo "\n<br>Id:             " . $story['id'];
//      echo "\n<br>Creation Time:  " . $story['createdTime'];
//      echo "\n<br>Type:           " . $story['type'];
//      echo "\n<br><img height=100 src=\"".$story['imageThumbnailUrl']."\">";
//
//  }
//}
//}
public function getThreads(){


$instagram=$this->instagram_details;
$threads = $instagram->getThreads(10, 10, 20);
$thread = $threads[0];

echo "Thread Info:\n";
echo "Id: {$thread->getId()}\n";
echo "Title: {$thread->getTitle()}\n";
echo "Type: {$thread->getType()}\n";
echo "Read State: {$thread->getReadState()}\n\n";

$items = $thread->getItems();
$item = $items[0];

echo "Item Info:\n";
echo "Id: {$item->getId()}\n";
echo "Type: {$item->getType()}\n";
echo "Time: {$item->getTime()}\n";
echo "User ID: {$item->getUserId()}\n";
echo "Text: {$item->getText()}\n\n";

$reelShare = $item->getReelShare();

echo "Reel Share Info:\n";
echo "Text: {$reelShare->getText()}\n";
echo "Type: {$reelShare->getType()}\n";
echo "Owner Id: {$reelShare->getOwnerId()}\n";
echo "Mentioned Id: {$reelShare->getMentionedId()}\n\n";

$reelMedia = $reelShare->getMedia();

echo "Reel Media Info:\n";
echo "Id: {$reelMedia->getId()}\n";
echo "Caption: {$reelMedia->getCaption()}\n";
echo "Code: {$reelMedia->getCode()}\n";
echo "Expiring At: {$reelMedia->getExpiringAt()}\n";
echo "Image: {$reelMedia->getImage()}\n\n";

}
//
//
//public function likeAndUnlikeMedia(){
//    ini_set('display_errors', 1);ini_set('display_startup_errors', 1);error_reporting(E_ALL);
//
//$instagram = \InstagramScraper\Instagram::withCredentials(new \GuzzleHttp\Client(), 'user', 'passwd', new Psr16Adapter('Files'));
//$instagram->login();
//
//$mediaId='2474033634010898853';
//
//function loghasLiked($bool)
//{
//  echo "HasLiked: ";
//  var_export($bool);
//  if     ($bool === null) echo " -> Unknown\n<br>"; //e.g. getMedia by tag won't give us haslike (-> an additional call to getMediaByCode/Id will give the result)
//  elseif ($bool)          echo " -> Liked\n<br>";
//  else                    echo " -> Not Liked\n<br>";
//}
//
//try {
//    loghasLiked($instagram->getMediaById($mediaId)->getHasLiked());
//    //hasLiked: False -> Not Liked
//
//    $instagram->like($mediaId);
//    loghasLiked($instagram->getMediaById($mediaId)->getHasLiked());
//    //hasLiked: True -> Liked
//
//    $instagram->unlike($mediaId);
//    loghasLiked($instagram->getMediaById($mediaId)->getHasLiked());
//    //hasLiked: False -> Not Liked
//
//    echo "\n<br>";
//    $media = $instagram->getCurrentTopMediasByTagName('Photography')[0];
//    loghasLiked($media->getHasLiked());
//    //hasLiked: NULL -> Unknown
//
//    $media = $instagram->getMediaById($media->getId());
//    loghasLiked($media->getHasLiked());
//    //hasLiked: False -> Not Liked
//
//} catch (InstagramException $ex) {
//    echo $ex->getMessage();
//}
//}
//
//public function paginateAccountMediaByUsername(){
//
//
//
//
//    $instagram = \InstagramScraper\Instagram::withCredentials(new \GuzzleHttp\Client(), 'username', 'password', new Psr16Adapter('Files'));
//$instagram->login();
//
//$result = $instagram->getPaginateMedias('kevin');
//$medias = $result['medias'];
//if ($result['hasNextPage'] === true) {
//    $result = $instagram->getPaginateMedias('kevin', $result['maxId']);
//    $medias = array_merge($medias, $result['medias']);
//}
//
//echo json_encode($medias);
//}
//
//
//public function saveSessionWithLongExpirationDate(){
//
//ini_set('display_errors', 1);ini_set('display_startup_errors', 1);error_reporting(E_ALL);//Example how to:
//
//$instagram = \InstagramScraper\Instagram::withCredentials(new \GuzzleHttp\Client(), 'login', 'password', new Psr16Adapter('Files'));
//$instagram->login();
//
////Save Session to reuse Insragram LoginL
//$instagram->saveSession(); //no argument given -> no change in expiration date
//$instagram->saveSession(3600);  //expiration set to now + 1 hour
//$instagram->saveSession(86400); //expiration set to now + 1 day
//
//$posts  = $instagram->getFeed();
//
//foreach ($posts as $post){
//    echo $post->getImageHighResolutionUrl()."\n";
//}
//
//
//$config = new Config();
//$config->setDefaultTtl(86400); //default ttl in seconds, should be as long as instagram login stays valid (don't know how long this is)
//$instagram = \InstagramScraper\Instagram::withCredentials(new \GuzzleHttp\Client(), 'user', 'passed', new Psr16Adapter('Files', $config));
//$instagram->login();
//$instagram->saveSession(); //Expiration date set to value as specified in config - defaultTtl
//
//$posts  = $instagram->getFeed();
//
//foreach ($posts as $post){
//    echo $post->getImageHighResolutionUrl()."\n";
//}
//
//}
//public function searchAccountsByUsername(){
//
//
//
//    $instagram = \InstagramScraper\Instagram::withCredentials(new \GuzzleHttp\Client(), 'username', 'password', new Psr16Adapter('Files'));
//    $instagram->login();
//
//
//    $accounts = $instagram->searchAccountsByUsername('raiym');
//
//    $account = $accounts[0];
//    // Following fields are available in this request
//    echo "Account info:\n";
//    echo "Username: {$account->getUsername()}";
//    echo "Full name: {$account->getFullName()}";
//    echo "Profile pic url: {$account->getProfilePicUrl()}";
//
//
//}
//
//
//public function setCustomCookies(){
//
//$newCookie = [
//    "ig_did"        =>	"88E6839******C29587D777B",
//    "mid"           =>	"XtFTPg******3hDNk3",
//    "shbid"         =>	"6262",
//    "shbts"         =>	"1594047690****683",
//    "sessionid"     =>	"3640101987****3A25",
//    "csrftoken"     =>	"VeI80i*****0l6ggxd",
//    "ds_user_id"    =>	"36*****872",
//];
//
//$instagram = \InstagramScraper\Instagram::withCredentials(new \GuzzleHttp\Client(), $this->instaUsername, $this->instaPassword, new Psr16Adapter('Files'));
//$instagram->setUserAgent('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:78.0) Gecko/20100101 Firefox/78.0');
//$instagram->setCustomCookies($newCookie);
//$instagram->login();
//$instagram->saveSession();
//
//
//try {
//    $account = $instagram->getAccount('username');
//
//} catch (InstagramException $ex) {
//    echo $ex->getMessage();
//}
//}




}
