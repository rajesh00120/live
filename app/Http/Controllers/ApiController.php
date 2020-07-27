<?php
namespace App\Http\Controllers;

use App\AlbumImages;
use App\User;
use App\Followers;
use App\Countries;
use App\States;
use App\Cities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\DB;

require "phpmailer/vendor/autoload.php"; 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class ApiController extends Controller
{
    public $loginAfterSignUp = true;
 
    public function register(Request $request)
    {
        $isEmailExist = User::where('email', '=', $request->email)->first();
        if($isEmailExist && $isEmailExist->is_verified != 0)
        return response()->json([
            'status' => 0,
            'message' => config('constant.EMAIL_ALLREADY_EXIST')
        ], 200);

        $isUserNameExist = User::where('userName', '=', $request->userName)->first();
        if($isUserNameExist && $isEmailExist->is_verified != 0)
        return response()->json([
            'status' => 0,
            'message' => config('constant.USER_NAME_ALREADY')
        ], 200);

        $otp = rand(100000, 999999);

        $user = new User();
        $user->firstName = $request->firstName;
        $user->lastName = $request->lastName;
        $user->userName = isset($request->userName)?$request->userName:'';
        $user->email = $request->email;
        $user->address = $request->address;
        $user->country = $request->country;
        $user->state = $request->state;
        $user->city = $request->city;
        
        $user->password = bcrypt($request->password);
        $user->verification_otp = $otp;
        
        $addressData = array(
            'country'=>$request->country,
            'state'=>$request->state,
            'city'=>$request->city
        );

        if($isEmailExist && $isEmailExist->is_verified == 0){
            $isEmailExist->verification_otp = $otp;
            $isEmailExist->save();

            $user->id = $isEmailExist->id;
            $user->updated_at = $isEmailExist->updated_at;
            $user->created_at = $isEmailExist->created_at;
        }
        else{
            $this->addCountryStateCity($addressData);
            $user->save();
        }
         
        $to = $request->email;
        
        $subject = 'Verification OTP.';
        $resMessage = "Your OTP has been send Successfully.";
        $message = '<div class="user_content_block">
                               <div class="allborder nopadding text-left padding10 user_content_inner" style="font-size:12px; font-family:Helvetica;" contenteditable="true">
                               <p>Hi ,' . $request->firstName . '</p>
                               <p></p>
                               <p>Your One Time OTP is <strong>' . $otp. '</strong></p>
                               <p><strong></strong></p>
                               <p><strong>Thanks</strong><br>
                               </p>
                               </div>
                               </div>';
                               
        $mail =  $this->sendMail($to,$message,$subject,$resMessage);

        return response()->json([
            'status' => 1,
            'message' => config('constant.REGISTER_SUCCESS'),
            'data' => $user,
            'mail'=>$mail
        ], 200);
    }
 
    public function login(Request $request)
    {
        
            
        $input = $request->only('email', 'password');



        $isVerified = User::where('email', '=', $request->email)->first();


       
      
        if(isset($isVerified->email) && $isVerified->is_verified == 0)
         

        return response()->json([
            'status' => 0,
            'message' => config('constant.INVALID_CREDENTIAL')
        ], 200);


        $jwt_token = null;
      
        if (!$jwt_token = JWTAuth::attempt($input)) {
            
            return response()->json([
                'status' => 0,
                'message' => Config('constant.INVALID_CREDENTIAL'),
            ], 401);
        }

        $userData = User::select('id', 'firstName', 'lastName', 'email', 'google_id', 'facebook_id', 'followers', 'followings', 'profile_pic')->where('email', '=', $request->email)->first();
        return response()->json([
            'status' => 1,
            'token' => $jwt_token,
            'data'=> $userData
        ]);
    }

    public function userStatus(Request $request) {
       
        if(!isset($request->user_status)){
            return response()->json([
                'status' => 0,
                'message' => config('constant.ALL_FIELDS_MANDATORY'),
            ]);
        }
           
            $message = config('constant.USER_STATUS_SUCCESS');
            $update = User::where('id', '=', $request->user->id)->update(['user_status'=>$request->user_status]);
            if($update) {
                return response()->json([
                    'status' => 1,
                    'message' => config('constant.USER_STATUS_SUCCESS'),
                ]);
            } else {
                return response()->json([
                    'status' => 0,
                    'message' => config('constant.GET_ERROR'),
                ]);
            }
           
       
    }

    public function socialLogin(Request $request)
    {
        $input = $request->only('facebook_id', 'google_id', 'name', 'email', 'profile_pic');
        if(!isset($input['name']) || !isset($input['email'])){
            return response()->json([
                'status' => 0,
                'message' => config('constant.ALL_FIELDS_MANDATORY'),
            ]);
        }
        if(isset($input['facebook_id'])){
            $currentUser = $this->loginAction(true, $input['facebook_id'],$input);
        }else if(isset($input['google_id'])){
            $currentUser = $this->loginAction(false, $input['google_id'],$input);
        }else{
            return response()->json([
                'status' => 0,
                'message' => config('constant.LOGIN_ERROR'),
            ]);
        }
        
        return response()->json([
            'status'=>1,
            'data' => $currentUser
        ]);
    }

    public function loginAction($login_type, $social_value, $input)
    {
        $user = new User();
        if($login_type)
            $socialKey = 'facebook_id';
        else
            $socialKey = 'google_id';

        $profile_pic = isset($input['profile_pic'])?$input['profile_pic']:'';
        $getUserfromSocialId=User::where($socialKey,'=',$social_value)->first();
        $user->$socialKey = $social_value;
        if($getUserfromSocialId){
            User::where($socialKey,'=',$social_value)->update(['name'=>$input['name'], 'profile_pic'=>$profile_pic]);
            $generateToken = JWTAuth::fromUser($getUserfromSocialId);
        }else{
            $checkEmailExist = User::where('email','=',$input['email'])->first();
            if($checkEmailExist)
            {
                User::where('email','=',$input['email'])->update([$socialKey =>$social_value, 'name'=>$input['name'], 'profile_pic'=>$profile_pic]);
            }else{
                $user->$socialKey = $input[$socialKey];
                $user->name = $input['name'];
                $user->email = $input['email'];
                $user->profile_pic = $profile_pic;
                $user->save();
            }
            $getUserfromSocialId=User::where($socialKey,'=',$social_value)->first();
            $generateToken = JWTAuth::fromUser($getUserfromSocialId);
        }
        // User::where($socialKey,'=',$social_value)->update(['remember_token' =>$generateToken]);
        $getUserfromSocialId->token = $generateToken;
        return $getUserfromSocialId;
    }
 
    public function logout(Request $request)
    {
        try {
            JWTAuth::invalidate($request->user->token);
 
            return response()->json([
                'status' => 1,
                'message' => config('constant.LOGOUT_SUCCESS')
            ]);
        } catch (JWTException $exception) {
            return response()->json([
                'status' => 0,
                'message' => config('constant.LOGOUT_ERROR')
            ], 500);
        }
    }
 
    public function getUserProfile(Request $request)
    {
        $follower_id = $request->user->id;

        $following = DB::table('followers')->Join('users', 'users.id', '=', 'followers.follower_id')->where('following_id','=',$follower_id)
                                            ->where('status','=',2)->count();

                                             $follower = DB::table('followers')->Join('users', 'users.id', '=', 'followers.following_id')->where('follower_id','=',$follower_id)
                                            ->where('status','=',2)->count();
        $request->user->followers= $following;
        $request->user->followings= $follower;
        return response()->json(['status' => 1,'data'=> $request->user]); 

    }

    public function changeProfilePic(Request $request)
    {
        if($request->hasFile('profile_pic')){
            $file = $request->file('profile_pic');
            $allowedfileExtension=['jpeg','jpg','png'];
            //Display File Name
            $filename = $file->getClientOriginalName();
            //Display File Extension
            $extension = $file->getClientOriginalExtension();
            //Display File Size
            // $size = $file->getSize();
            $file->getRealPath();
            if(!in_array(strtolower($extension), $allowedfileExtension))
            return response()->json([
                'status' => 0,
                'message' => config('constant.FILE_TYPE_NOT_ALLOWED')
            ]);
            //Display File Mime Type
            $filename = time().$filename;
            //Move Uploaded File
            $file->move(public_path('profile_pics'),$filename);
            User::where('id', '=', $request->user->id)->update(['profile_pic' => URL("public/profile_pics/".$filename)]);
            return response()->json([
                'status' => 1,
                'message' => config('constant.PROFILE_PIC_CHANGED_SUCCESS')
            ]);
        }else{
            return response()->json([
                'status' => 0,
                'message' => config('constant.ALL_FIELDS_MANDATORY')
            ]);
        }
        
    } // uploadMedia Closing

    public function searchPeople(Request $request)
    {   
        if(isset($request->keyword)){
            $searchResult = User::select('name', 'firstName', 'lastName', 'id', 'profile_pic')->where('firstName','=',$request->keyword)
                                ->where('id','!=',$request->user->id)->get()->toArray();
            $searchResultIds = array();
            if($searchResult){
                foreach ($searchResult as $key => $value) {
                    $imagesCount = AlbumImages::where('user_id', '=', $value['id'])->count();
                    $searchResult[$key]['follow_status'] = $this->getFollowStatus($value['id'], $request->user->id);
                    $searchResult[$key]['imageCount'] = $imagesCount;
                }
                $searchResultIds = array_column($searchResult, 'id');
            }
             // Getting only Ids
            array_push($searchResultIds, $request->user->id);
            $similarResult = User::select('name','userName', 'firstName', 'lastName', 'id', 'profile_pic')->where('firstName', 'like', '%' . $request->keyword . '%')->orWhere('userName', 'like', '%' . $request->keyword . '%')
                                    ->whereNotIn('id', $searchResultIds)->get()->toArray();
            foreach ($similarResult as $key1 => $value1) {
                $imagesCount = AlbumImages::where('user_id', '=', $value1['id'])->count();
                $similarResult[$key1]['follow_status'] = $this->getFollowStatus($value1['id'], $request->user->id);
                $similarResult[$key1]['imageCount'] = $imagesCount;
            }
        }else{
            return response()->json([
                'status' => 0,
                'message' => config('constant.SEARCH_KEYWORD_NOT_PROVIDED'),
            ]);
        }
        if(count($searchResult) || count($similarResult))
        return response()->json([
            'status'=>1,
            'searchResult' => $searchResult,
            'similarResult' => $similarResult
        ]);
        else
        return response()->json([
            'status'=>1,
            'message' => config('constant.SEARCH_RESULT_NOT_FOUND'),
            'searchResult' => array(),
            'similarResult' =>array()
        ]);
    }

    

    public function followUser(Request $request)
    {   
        $follower_id = $request->user->id;
        $following_id = $request->user_id;
        $status = $request->status;
        if(isset($request->user_id) && isset($request->user->id) && isset($request->status) ||  $request->status){
            $isFollowingUserExist = User::select('followings', 'followers')->where('id', '=', $following_id)->first();
            if(!$isFollowingUserExist)
            return response()->json([
                'status'=>0,
                'message' => config('constant.USER_NOT_EXIST')
            ]);
            
            $isFollowedExist = Followers::where('follower_id','=',$follower_id)
                                            ->where('following_id','=',$following_id)->first();
            if($isFollowedExist){

                Followers::where('follower_id','=',$follower_id)
                ->where('following_id','=',$following_id)->update(['status' =>$status]);
                
            }else{

                $is_public = User::where(['id'=>$following_id,'user_status'=>1])->first();
                if(!empty($is_public)){
                    $status = 2;
                }

                $followerModel = new Followers();
                $followerModel->follower_id = $follower_id;
                $followerModel->following_id = $following_id;
                $followerModel->status = $status;
                $followerModel->save();
            }

            $message = config('constant.USER_FOLLOW_SUCCESS');
            if($status == '0' || $status == 0)
            {
                $message = config('constant.USER_UNFOLLOW_SUCCESS');
                User::where('id', '=', $following_id)->update(['followers'=>$isFollowingUserExist->followers-1]);
                User::where('id', '=', $follower_id)->update(['followings'=>$request->user->followings-1]);

                // $message = config('constant.USER_FOLLOW_SUCCESS');
                // User::where('id', '=', $following_id)->update(['followers'=>$isFollowingUserExist->followers+1]);
                // User::where('id', '=', $follower_id)->update(['followings'=>$request->user->followings+1]);
            }   

            return response()->json([
                'status'=>1,
                'message' => $message
            ]);    
        }else{
            return response()->json([
                'status' => 0,
                'message' => config('constant.ALL_FIELDS_MANDATORY'),
            ]);
        }
        
    }


    public function followUserRequestList(Request $request)
    {   
        $follower_id = $request->user->id;
    
        if(isset($request->user->id)){
            

            $isFollowedExist = DB::table('followers')->leftJoin('users', 'users.id', '=', 'followers.follower_id')->where('following_id','=',$follower_id)
                                            ->where('status','=',1)->get();
                                          
            
            return response()->json([
                'status'=>1,
                'message' => 'List of requested follow list.',
                'data'=>$isFollowedExist
            ]);    
        }else{
            return response()->json([
                'status' => 0,
                'message' => config('constant.ALL_FIELDS_MANDATORY'),
            ]);
        }
        
    }


    public function followerUserList(Request $request)
    {   
        $follower_id = $request->user->id;
    
        if(isset($request->user->id)){
            
            $isFollowedExist = DB::table('followers')->Join('users', 'users.id', '=', 'followers.follower_id')->where('following_id','=',$follower_id)
                                            ->where('status','=',2)->get();
                  
             $val = array();
             if($isFollowedExist){
                foreach ($isFollowedExist as $key => $value) {
                    $val[$key]['following_id'] = $value->following_id;
                    $val[$key]['follower_id'] = $value->follower_id;
                    $val[$key]['userName'] = $value->userName;
                    $val[$key]['firstName'] = $value->firstName;
                    $val[$key]['lastName'] = $value->lastName;
                    $val[$key]['email'] = $value->email;
                    $val[$key]['profile_pic'] = $value->profile_pic;
                    $followStatus = $this->getFollowStatus($value->follower_id,$request->user->id);
                    //$userDetails->follow_status = $followStatus;
                    $val[$key]['follwing_by_me'] = $followStatus;
                }  
             }
                                          
                                          
            if($isFollowedExist) {
                return response()->json([
                    'status'=>1,
                    'message' => 'List of Follower.',
                    'counts'=>count($val),
                    'data'=>$val
                ]);  
            } else {
                return response()->json([
                    'status'=>0,
                    'counts'=>0,
                    'message' => config('constant.NOTFOUND'),
                    'data'=>[]
                ]);
            }
              
        }else{
            return response()->json([
                'status' => 0,
                'message' => config('constant.ALL_FIELDS_MANDATORY'),
            ]);
        }
        
    }

    public function followingUserList(Request $request)
    {   
        
      
        $follower_id = $request->user->id;

        if(isset($request->user->id)){
            
            DB::connection()->enableQueryLog();
            $isFollowedExist = DB::table('followers')->Join('users', 'users.id', '=', 'followers.following_id')->where('follower_id','=',$follower_id)
                                            ->where('status','=',2)->get();
                                            
                                            
                                            $val = array();
                                            if($isFollowedExist){
                                               foreach ($isFollowedExist as $key => $value) {
                                                   $val[$key]['following_id'] = $value->following_id;
                                                   $val[$key]['follower_id'] = $value->follower_id;
                                                   $val[$key]['userName'] = $value->userName;
                                                   $val[$key]['firstName'] = $value->firstName;
                                                   $val[$key]['lastName'] = $value->lastName;
                                                   $val[$key]['email'] = $value->email;
                                                   $val[$key]['profile_pic'] = $value->profile_pic;
                                                   $followStatus = $this->getFollowStatus($value->following_id,$request->user->id);

                                                  // $followStatus = $this->getFollowStatus($request->user->id,$value->following_id);
                                                   $val[$key]['follwing_by_me'] = $followStatus;
                                                   //$val[$key]['follwing_by_me'] = false;
                                               }  
                                            }             
            if($isFollowedExist) {
                return response()->json([
                    'status'=>1,
                    'message' => 'List of Following.',
                    'counts'=>count($val),
                    'data'=>$val
                ]);  
            } else {
                return response()->json([
                    'status'=>0,
                    'counts'=>0,
                    'message' => config('constant.NOTFOUND'),
                    'data'=>[]
                ]);
            }
              
        }else{
            return response()->json([
                'status' => 0,
                'message' => config('constant.ALL_FIELDS_MANDATORY'),
            ]);
        }
        
    }



    public function acceptFollow(Request $request)
    {   
        // $follower_id = $request->user->id;
        // $following_id = $request->user_id;
        $follower_id = $request->user_id;
        $following_id = $request->user->id;
        $status = $request->status;
    
        if(isset($request->user_id) && isset($request->user->id) && isset($request->status)){
            
           //$isFollowedExist = Followers::where('follower_id','=',$follower_id)->where('following_id','=',$following_id)->first();

           $isFollowedExist = Followers::where('follower_id','=',$follower_id)->where('following_id','=',$following_id)->first();

           $isFollowingUserExist = User::select('followings', 'followers')->where('id', '=', $following_id)->first();

            if(!$isFollowingUserExist || !$isFollowedExist)
            return response()->json([
                'status'=>0,
                'message' => config('constant.USER_NOT_EXIST')
            ]);

               
                   Followers::where('follower_id','=',$follower_id)
                   ->where('following_id','=',$following_id)->update(['status' =>$status]);
                

            if($status==2)
            {
                $message = config('constant.ACCEPT_REQUEST');
                User::where('id', '=', $following_id)->update(['followers'=>$isFollowingUserExist->followers+1]);
                User::where('id', '=', $follower_id)->update(['followings'=>$request->user->followings+1]);
            }else if($status==3){
                $message = config('constant.REJECT_REQUEST');
                // User::where('id', '=', $following_id)->update(['followers'=>$isFollowingUserExist->followers-1]);
                // User::where('id', '=', $follower_id)->update(['followings'=>$request->user->followings-1]);
            }
                                          
            
            return response()->json([
                'status'=>1,
                'message' => $message,
                //'data'=>$isFollowedExist
            ]);    
        }else{
            return response()->json([
                'status' => 0,
                'message' => config('constant.ALL_FIELDS_MANDATORY'),
            ]);
        }
        
    }

    public function getUserDetails(Request $request)
    {   
        if(isset($request->user_id)){

            $follower_id = $request->user->id;

            $following = DB::table('followers')->Join('users', 'users.id', '=', 'followers.follower_id')->where('following_id','=',$request->user_id)
                                                ->where('status','=',2)->count();
    
            $follower = DB::table('followers')->Join('users', 'users.id', '=', 'followers.following_id')->where('follower_id','=',$request->user_id)
            ->where('status','=',2)->count();

            $request->user->followers= $following;
            $request->user->followings= $follower;

            $userDetails = User::select('name','userName', 'firstName', 'lastName', 'id', 'profile_pic', 'followers', 'followings','user_status')->where('id','=',$request->user_id)->first();
            if($userDetails){
                $imagesCount = AlbumImages::where('user_id', '=', $userDetails->id)->count();
                $userDetails->imageCount = $imagesCount;
                $followStatus = $this->getFollowStatus($request->user_id,$request->user->id);
                $userDetails->follow_status = $followStatus;

                $userDetails->followers= $following;
                $userDetails->followings= $follower;

                if($imagesCount){
                    $img_path = URL("public/album_images");
                    $userMedia = AlbumImages::select('id', DB::Raw('CONCAT("'.$img_path.'","/", album_media_path) as album_media_path'))->where('user_id', '=', $userDetails->id)->get()->toArray();
                    $userDetails->userMedia = $userMedia;
                }
                return response()->json([
                    'status'=>1,
                    'data' => $userDetails
                ]);
            }else
            return response()->json([
                'status' => 1,
                'message' => config('constant.USER_NOT_EXIST'),
            ]);
            
        }else{
            return response()->json([
                'status' => 0,
                'message' => config('constant.PARAMETER_MISSING'),
            ]);
        }
    }

    public function forgotPassword(Request $request) {
         
       
        try{

        
            if(!$request->email) {
                return $result = collect(["status" => "0", "message" => "Please Provide me mail id.", 'errorCode' => '', 'errorDesc' => '', "data" => array()]);
            }
        
            $to = $request->email;
            //$to = 'md.shamshad550@gmail.com';

            $getUser = User::where('email', '=', $request->email)->first();
            
            if(!$getUser) {
                return $result = collect(["status" => "0", "message" => "Sorry !!! I could'nt have recognised record please enter correct email id.", 'errorCode' => '', 'errorDesc' => '', "data" => array()]);
            }
            
            $otp = rand(100000, 999999);
        
            $subject = 'Forgot Password.';
            $resMessage = "Your OTP has been send Successfully.";
            $message = '<div class="user_content_block">
                                <div class="allborder nopadding text-left padding10 user_content_inner" style="font-size:12px; font-family:Helvetica;" contenteditable="true">
                                <p>Hi ,' . $getUser->name . '</p>
                                <p></p>
                                <p>Your One Time OTP is <strong>' . $otp. '</strong></p>
                                <p><strong></strong></p>
                                <p><strong>Thanks</strong><br>
                                </p>
                                </div>
                                </div>';
                

            $mail =  $this->sendMail($to,$message,$subject,$resMessage);
            $update = User::where('id','=',$getUser->id)->update(['verification_otp'=>$otp]);
            $user_details = User::where('email', '=', $request->email)->first();
            //print_r($mail);die;
            
            if($mail) {
                
                return $result = collect(["status" => "1", "message" => $resMessage, 'errorCode' => '', 'errorDesc' => '', "data" => $user_details]);
            } else {
                return $result = collect(["status" => "0", "message" => "unable to send otp.", 'errorCode' => '', 'errorDesc' => '', "data" => array()]);
            } 

        }catch(Exception  $ex){

            return $result = collect(["status" => "0", "message" => "something went wrong.".$ex, 'errorCode' => '', 'errorDesc' => '', "data" => array()]);
        }   
     
}

public function resendOtp(Request $request) {
         
    if(!$request->email) {
        return $result = collect(["status" => "0", "message" => "Please Provide me mail id.", 'errorCode' => '', 'errorDesc' => '', "data" => array()]);
    }
 
    $to = $request->email;

    $getUser = User::where('email', '=', $request->email)->first();
     
    if(!$getUser) {
        return $result = collect(["status" => "0", "message" => "Sorry !!! I could'nt have recognised record please enter correct email id.", 'errorCode' => '', 'errorDesc' => '', "data" => array()]);
     }
    
    $otp = rand(100000, 999999);
  
    $subject = 'Resend OTP.';
    $resMessage = "Your OTP has been send Successfully.";
    $message = '<div class="user_content_block">
                           <div class="allborder nopadding text-left padding10 user_content_inner" style="font-size:12px; font-family:Helvetica;" contenteditable="true">
                           <p>Hi ,' . $getUser->name . '</p>
                           <p></p>
                           <p>Your One Time OTP is <strong>' . $otp. '</strong></p>
                           <p><strong></strong></p>
                           <p><strong>Thanks</strong><br>
                           </p>
                           </div>
                           </div>';
                           
       $mail =  $this->sendMail($to,$message,$subject,$resMessage);
       if($mail) {
           $update = User::where('id','=',$getUser->id)->update(['verification_otp'=>$otp]);
           return $result = collect(["status" => "1", "message" => $resMessage, 'errorCode' => '', 'errorDesc' => '', "data" => array()]);
       } else {
        return $result = collect(["status" => "0", "message" => "Oops Something went wrong.", 'errorCode' => '', 'errorDesc' => '', "data" => array()]);
       } 
 
}

//Changed or update password
public function update_Password(Request $request) {
             
    if(!$request->new_password || !$request->confirm_password) {
       $result = collect(["status" => "2", "message" => 'Please Provide me confirm_password and new_password.', 'errorCode' => '', 'errorDesc' => '', "data" => new \stdClass()]);
       return $result;  
    }
   if (trim($request->new_password) && trim($request->confirm_password) && trim($request->id)) {
   
       $user  = User::where('id', $request->id)->get();
           if ($request->new_password == $request->confirm_password) {
               User::where('id', trim($request->id))->update(['password' => bcrypt($request->new_password)]);
               $result = collect(["status" => "1", "message" => 'Your password has been changed.', 'errorCode' => '', 'errorDesc' => '', "data" => $user]);
               return $result;
           } else {
               $result = collect(["status" => "2", "message" => 'Confirm passowrd is not match.', 'errorCode' => '', 'errorDesc' => '', "data" => new \stdClass()]);
               return $result;
           }
   
} else {
   $result = collect(["status" => "2", "message" => 'Please Provide me all request.', 'errorCode' => '', 'errorDesc' => '', "data" => new \stdClass()]);
   return $result;
} 
}



public function verificationOtp(Request $request){
    if(!$request->email || !$request->otp) {
        return $result = collect(["status" => "0", "message" => "Please Provide me mail id or otp", 'errorCode' => '', 'errorDesc' => '', "data" => array()]);
    }
 
    $to = $request->email;

    $getUser = User::where('email', '=', $request->email)->where('verification_otp', '=', $request->otp)->first();
     
    if($getUser){
        $success = User::where('email', '=', $request->email)->where('verification_otp', '=', $request->otp)->update(['is_verified' => 1]);
        if($success)
            return $result = collect(["status" => "1", "message" => "You have successfully verified OTP.", 'errorCode' => '', 'errorDesc' => '', "data" => array()]);
        else
            return $result = collect(["status" => "0", "message" => "Your OTP not verified.", 'errorCode' => '', 'errorDesc' => '', "data" => array()]);
    }
    else{
        return $result = collect(["status" => "0", "message" => "Please enter correct OTP.", 'errorCode' => '', 'errorDesc' => '', "data" => array()]);
    } 
}

public function checkUserName(Request $request){
    if(!$request->search) {
        if(!isset($request->search)){
            return response()->json([
                'status' => 0,
                'message' => config('constant.ALL_FIELDS_MANDATORY'),
            ]);
        }
    }
 
    $to = $request->email;

    $getUser = User::where('userName', '=', $request->search)->first();
     
    if($getUser) {
        return $result = collect(["status" => "0", "message" => "Already exist."]);
     } else {
        return $result = collect(["status" => "1", "message" => "Available user name"]);
     }

      
}


    public function sendMail($to,$message,$subject,$resMessage){

        $developmentMode = true;
        $mailer = new PHPMailer($developmentMode);
        // echo $to;
        // echo '<br>'.$message;
        // echo '<br>'.$subject;
        // echo '<br>'.$resMessage;
        
        try {
            $mailer->SMTPDebug = 0;
            $mailer->isSMTP();

            if ($developmentMode) {
                $mailer->SMTPOptions = [
                    'ssl'=> [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    ]
                ];
            }


            $mailer->Host = 'smtp.gmail.com';
            $mailer->SMTPAuth = true;
            $mailer->Username = 'micahapp237@gmail.com';
            $mailer->Password = 'Micah@123';
            $mailer->SMTPSecure = 'tls';
            $mailer->Port = 587;
           


            $mailer->setFrom('support@museum.com', 'admin');
            $mailer->addAddress($to, '');

            $mailer->isHTML(true);

            $mailer->Subject = $subject;
            
            $mailer->Body = $message;

            $send = $mailer->send();
            $mailer->ClearAllRecipients();
            if($send){
                return true;
            }else{
                return false;
            }
            
            
        

        } catch (Exception $ex) {
            
            return $ex;
        
        }


    }

    public function getFollowStatus($follow_to, $follow_by)
    {   
        $isFollow = Followers::where([
                        ['following_id', '=', $follow_to],
                        ['follower_id', '=', $follow_by]
                    ])->first();
                    if(!$isFollow)
                        return 0;
        return $isFollow->status;
    }

    public function addCountryStateCity($data){
         
          $country = Countries::select('country_id','country_name','status')->where('country_name',$data['country'])->first();
          
          if(!isset($country->country_name)){ // Country Add
            $cid = DB::table('countries')->insertGetId(
                ['country_name' => $data['country'], 'status' => 1]
               );

                if($cid){ // State add
                    $sid = DB::table('states')->insertGetId(
                        ['state_name' => $data['state'],'country_id' => $cid, 'status' => 1]
                       );
                       if($sid) { // City add
                        $id = DB::table('cities')->insertGetId(
                            ['city_name' => $data['city'],'state_id' => $sid, 'status' => 1]
                           );
                           //die('country');
                       }
                }
          } else  {
            $state = States::select('state_id','state_name','country_id')->where('state_name',$data['state'])->where('country_id',$country->country_id)->first();

            if(!isset($state->state_name)){ // State add
                $sid = DB::table('states')->insertGetId(
                    ['state_name' => $data['state'],'country_id' => $country->country_id, 'status' => 1]
                );
                if($sid){ // City Add
                    $id = DB::table('cities')->insertGetId(
                        ['city_name' => $data['city'],'state_id' => $sid, 'status' => 1]
                       ); 
                        // die('State');
                } 

              } else {  // City Add
                        
                $id = DB::table('cities')->insertGetId(
                    ['city_name' => $data['city'],'state_id' => $state->state_id, 'status' => 1]
                   );
                  // die('City'); 
              }
          } 

        


    }


   public function slashImage(){
    $img_path_adv = URL("public/assets/ad_images");
        $advertiseMent = DB::table('advertisement')->select('*',DB::Raw('CONCAT("'.$img_path_adv.'","/", ad_images) as images'))->where('type','slash')->first();
        return response()->json([
            'status' => 1,
            'message' => 'Slash Image',
            'data' =>$advertiseMent
        ]);
   }



}
