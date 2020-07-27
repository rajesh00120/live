<?php
namespace App\Http\Controllers;
use App\Albums;
use App\AlbumImages;
use App\User;
use App\Followers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use JWTAuth;
use Illuminate\Support\Facades\Auth;
use Session;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Helper;
//namespace App\Helpers;
 // Important
 use Image;
require "phpmailer/vendor/autoload.php"; 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class AdminController extends Controller
{
    public function __construct()
    {
        header('Access-Control-Allow-Origin: ', 'http://localhost:3000/');
        header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        // header('Access-Control-Max-Age', '1000');
        header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
        header('Access-Control-Allow-Credentials', true);
        // next();
    }

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
        $user->password = bcrypt($request->password);
        $user->verification_otp = $otp;
        
        if($isEmailExist && $isEmailExist->is_verified == 0){
            $isEmailExist->verification_otp = $otp;
            $isEmailExist->save();

            $user->id = $isEmailExist->id;
            $user->updated_at = $isEmailExist->updated_at;
            $user->created_at = $isEmailExist->created_at;
        }
        else{
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
    public function loginPostUrl(Request $request)
    {

          //$data_entered = $request->all();
        // print_r($data_entered);die;
        $data = Session::all();
        $credentials = $request->only('email', 'password');

        // print_r(JWTAuth::attempt($credentials));die;
        if ($jwt_token = JWTAuth::attempt($credentials)) {
             //print_r(JWTAuth::attempt($credentials));die;
            // die;
            // return redirect()->route('Dashboard.dashboard');
            $userData = User::select('id', 'firstName', 'lastName', 'email')->where('email', '=', $request->email)->first();
            //print_r($userData);die('asff');
            Session::put('name',$userData->firstName);
            Session::put('email',$userData->email);
            Session::put('login',TRUE);
            $users = User::select('userName','profile_pic')->where('is_verified','1')->orderBy('id','desc')->limit(7)->get()->toArray();
            $img_path = URL("public/album_images");
            $albums = DB::table('albums')->select('users.userName as userNameData','users.profile_pic','albums.name',DB::Raw('CONCAT("'.$img_path.'","/", albums.timeline_cover) as timeline_cover'))->Join('users', 'users.id', '=', 'albums.user_id')->groupBy('albums.user_id')->orderBy('albums.id','desc')->limit(7)->get()->toArray();
            $usersCounts = User::where('is_verified','1')->count();
            $albumCounts = Albums::count();
            $data = array(
                'users'=>$users, 
                'usersCount'=>$usersCounts,
                'albumCounts'=>$albumCounts,
                'albums'=>$albums
            );
            return view('Dashboard.dashboard', $data);

        }
        else{
            return redirect()->back()->withErrors('Wrong username/password combination.');
        }
        
    }
    public function logins(Request $request)
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
        public function DashboardPage(Request $request)
        {
            $users = User::select('userName','profile_pic')->where('is_verified','1')->orderBy('id','desc')->limit(7)->get()->toArray();
            $img_path = URL("public/album_images");
            $albums = DB::table('albums')->select('users.userName as userNameData','users.profile_pic','albums.name',DB::Raw('CONCAT("'.$img_path.'","/", albums.timeline_cover) as timeline_cover'))->Join('users', 'users.id', '=', 'albums.user_id')->groupBy('albums.user_id')->orderBy('albums.id','desc')->limit(7)->get()->toArray();
            $usersCounts = User::where('is_verified','1')->count();
            $albumCounts = Albums::count();
            $data = array(
                'users'=>$users, 
                'usersCount'=>$usersCounts,
                'albumCounts'=>$albumCounts,
                'albums'=>$albums
            );
            return view('Dashboard.dashboard', $data);
        }

        public function getAjaxUsersList(Request $request)
        {
            if($request->ajax()){
                $draw = intval($request->draw);
                $start = intval($request->start);
                $length = intval($request->length);
                $search = $request->search['value'];
                if($search == ''){
                    $totalrows = DB::table('users')->where('is_verified',1)->get()
                    ->count();
                }else{
                    $totalrows = DB::table('users')->where('is_verified',1)->where(function($query) use ($search){
                      $query->where('email', 'like', '%'.$search.'%') 
                      ->orWhere('name', 'like', '%'.$search.'%')
                      ->orWhere('address', 'like', '%'.$search.'%');
                    })
                    ->get()
                    ->count();
                }
                if($search == ''){
                    $customerListing = DB::table('users')
                    ->select('id','email','name','address','profile_pic','blocked_users')
                    ->orderBy('created_at','ASC')
                    ->where('is_verified',1)
                    ->where('inactive_users','=',1)
                    ->offset($start)
                    ->limit($length)
                    ->get();
                }else{
                    $customerListing = DB::table('users')
                    ->select('id','email','name','address','profile_pic','blocked_users')
                    ->where(function($query) use ($search){
                        $query->where('email', 'like', '%'.$search.'%') 
                        ->orWhere('name', 'like', '%'.$search.'%')
                        ->orWhere('address', 'like', '%'.$search.'%');
                    })
                    ->where('is_verified',1)
                    ->where('inactive_users','=',1)
                    ->offset($start)
                    ->limit($length)
                    ->get();
                }
                $data = array();
                $startcount = $start+1;
                if(!empty($customerListing)){
                  foreach ($customerListing as $key => $customer) {
                      $cust_address=$customer->address;
                        $img = asset('public/assets/profile_pic/'.$customer->profile_pic);
                       $attr = array();
                       if($customer->profile_pic!='' && $customer->profile_pic!='null'){
                            $attr[] = "<img src='$customer->profile_pic'  width='50' height='50'  />";
                       }
                       else{
                            $attr[] = "<img src= http://172.16.200.38/museum/public/assets/admin.png width='50' height='50'  />";
                       }
                      // $attr[] = $customer->profile_pic;
                       $attr[] = $customer->name;
                       $attr[] = $customer->address;
                       $attr[] = $customer->email;
                       if($customer->blocked_users=='1'){
                        $attr[] ="<a href='block_users/$customer->id/$customer->blocked_users' data-id=$customer->id data-value=$customer->blocked_users><i class='fa fa-ban' aria-hidden='true' style='color: grey; cursor: pointer; margin-right: 10px'></a></i><a href=delete_users/$customer->id onclick='return confirm('Are you sure you want to delete this user?');' ><i class='fa fa-trash' aria-hidden='true' style='color: red; cursor: pointer'></i></a>"; 
                       }
                       if($customer->blocked_users=='0'){
                        $attr[] ="<a href='block_users/$customer->id/$customer->blocked_users' data-id=$customer->id data-value=$customer->blocked_users><i class='fa fa-ban' aria-hidden='true' style='color: red; cursor: pointer; margin-right: 10px'></a></i><a  href=delete_users/$customer->id onclick='return confirm('Are you sure you want to delete this user?');'><i class='fa fa-trash' aria-hidden='true' style='color: red; cursor: pointer'></i></a>"; 
                       }                       
                       $attr[] ='';
                       $data[] = $attr;
                       $startcount++;
                  }
                }
                $output = array(
                    "draw" => $draw,
                    "recordsTotal" => $totalrows,
                    "recordsFiltered" => $totalrows,
                    "data" => $data
                );
                return response()->json($output);
              }
        }


        public function myprofile(Request $request){ 
            //print_r($request->all()); die;
            $data = DB::table('users')
            ->select('id','userName','email','password','profile_pic')
            //->where('id','63')
            ->first();
            return view('Profile.profile')->with('profile_data', $data);
        }
        public function edit_profile(Request $request, $id){ //print_r($id); die;
            $img_path = "http://172.16.200.38/museum/public/assets/profile_pic/";
            $fileNameToStore="";
            $this->validate($request, [
                'file' => 'mimes:jpeg,bmp,png,ogg',
            ]);
            if($request->hasFile('file')) { 
                $filename = $request->file->getClientOriginalName();
                $filesize = $request->file->getClientSize();
                $onlyfilename = pathinfo( $filename, PATHINFO_FILENAME);
                $image = $request->file('file'); 
                $extension = $request->file->getClientOriginalExtension(); 
                $fileNameToStore = $onlyfilename.'_'.time().'.'.$extension; 
                $image->move(public_path('assets/profile_pic'), $fileNameToStore);
            }
            $imagess = $img_path.$fileNameToStore;
            $updateObj =array(
                'userName' => $request['username'],
                'email' => $request['my_email'],
                'password' => bcrypt($request['confirm_password'])
            );
            if($fileNameToStore!="")
            {
                $updateObj['profile_pic'] = $imagess; 
            }
            $update =  DB::table('users')
            ->where('id','=',$id)
            ->update($updateObj);
            return redirect()->back()->with('success', 'added');
        }
        public function edit_password(Request $request, $id){ //alert($request->all()); die;
            $update =  DB::table('users')
            ->where('id','=',$id)
            ->update(['password' => bcrypt($request['confirm_password'])]);
            return redirect()->back()->with('success', 'added');
        }
        public function getAjaxMediaList(Request $request)
        {
            if($request->ajax()){
                $draw = intval($request->draw);
                $start = intval($request->start);
                $length = intval($request->length);
                $search = $request->search['value'];
                if($search == ''){
                    
                    $totalrows = DB::table('users')
                    ->rightJoin('albumimages', 'users.id', '=', 'albumimages.user_id')
                    ->rightJoin('albums', 'albumimages.user_id', '=', 'albums.user_id')
                    ->groupBy('users.id')
                    ->get()
                    ->count();
                }else{
                   
                    $totalrows = DB::table('users')->where(function($query) use ($search){
                      $query->where('users.email', 'like', '%'.$search.'%') 
                      ->orWhere('users.name', 'like', '%'.$search.'%')
                      ->orWhere('users.address', 'like', '%'.$search.'%');
                    })
                    ->rightJoin('albumimages', 'users.id', '=', 'albumimages.user_id')
                    ->rightJoin('albums', 'albumimages.user_id', '=', 'albums.user_id')
                    ->groupBy('users.id')
                    ->get()
                    ->count();
                }
                if($search == ''){
                    $customerListing = DB::table('users')
                        ->rightJoin('albumimages', 'users.id', '=', 'albumimages.user_id')
                        ->rightJoin('albums', 'albumimages.user_id', '=', 'albums.user_id')
                        ->select('users.id as users_id','users.name as username','users.profile_pic as profile_pic','albums.name as album_name','albumimages.album_media_path as albums','albumimages.type as type')
                        //->where('blocked_users','=','1')
                        ->groupBy('users.id')
                        ->offset($start)
                        ->limit($length)
                        ->get();
                }else{
                    $customerListing = DB::table('users')
                        ->rightJoin('albumimages', 'users.id', '=', 'albumimages.user_id')
                        ->rightJoin('albums', 'albumimages.user_id', '=', 'albums.user_id')
                        ->select('users.id as users_id','users.name as username','users.profile_pic as profile_pic','albums.name as album_name','albumimages.album_media_path as albums','albumimages.type as type')
                        //->where('blocked_users','=','1')
                        ->where(function($query) use ($search){
                            $query->where('users.name', 'like', '%'.$search.'%') 
                            ->orWhere('albums.name', 'like', '%'.$search.'%');
                        })
                        ->groupBy('users.id')
                        ->offset($start)
                        ->limit($length)
                        ->get();
                }
                $data = array(); 
                $startcount = $start+1;
                if(!empty($customerListing)){
                  foreach ($customerListing as $key => $customer) {

                    $attr = array();
                    if($customer->profile_pic!='' && $customer->profile_pic!='null'){
                        $attr[] = "<img src='$customer->profile_pic'  width='50' height='50'  />";
                   }
                   else{
                        $attr[] = "<img src= http://172.16.200.38/museum/public/assets/admin.png width='50' height='50'  />";
                   }
                    //$attr[] = "<img src=$customer->profile_pic width='50' height='50' />";
                    $attr[] = $customer->username;
                    $attr[] = Albums::where('user_id','=',$customer->users_id)->select('name')->count(); //$customer->album_name;
                    $attr[] = albumimages::where('user_id','=',$customer->users_id)->where('type','=',1)->select('album_media_path')->count(); 
                    $attr[] = albumimages::where('user_id','=',$customer->users_id)->where('type','=',2)->select('album_media_path')->count();
                    $album_value=Albums::where('user_id','=',$customer->users_id)->select('name')->count();
                    if($album_value!=''){
                        $attr[] ="<a href=manage_media/$customer->users_id><button type='button' class='btn btn-info'>Manage</button></a>";
                    }
                    else{
                        $attr[] ="<a href='javascript:void(0);'><button type='button' class='btn btn-info' disabled>Manage</button></a>";
                    }
                    $attr[] ='';
                    $data[] = $attr;
                    $startcount++;

                  }
                }
              $output = array(
                         "draw" => $draw,
                         "recordsTotal" => $totalrows,
                         "recordsFiltered" => $totalrows,
                         "data" => $data
                    );
                  return response()->json($output);
              }
        }
        public function logout(){
            Session::flush();
            echo ("<SCRIPT LANGUAGE='JavaScript'>
            window.location.href='http://35.233.149.139/museum/login';
            </SCRIPT>"); 
        }
        public function manage_media($id){
            $user_info = DB::table('users')
            ->select('name','email','id','profile_pic')
            ->where('id','=',$id)
            ->get();

            $dates = DB::table('albums')
            ->where('albums.user_id','=',$id)
            ->orderBy('albums.user_id', 'DESC')
            ->select('albums.date')
            ->distinct()
            ->get();
            $img_path = URL("public/album_images");
            $userAlbums = DB::table('albums')->select('albums.id as albumid','albums.name','albums.timeline_desc',DB::raw("MONTHNAME(albums.date) as Month"),DB::raw("YEAR(albums.date) as Year"),DB::Raw('CONCAT("'.$img_path.'","/", albums.timeline_cover) as timeline_cover'),'albums.created_at')->where('albums.user_id','=',$id)->orderBy('Year','desc')->orderBy('albums.date','asc')->get()->toArray();
             
            $timeMonths = [];
            $timelineData = [];
            if($userAlbums) {
                foreach ($userAlbums as $key => $value) {
                    if(!in_array($value->Month.'-'.$value->Year,$timeMonths)) {
                        array_push($timeMonths,$value->Month.'-'.$value->Year);
                        array_push($timelineData,$value);
                    }
                }
            } 
            $total_album = DB::table('albums')
            ->where('albums.user_id','=',$id)
            ->select('albums.timeline_cover','albums.date','albums.name as album_name','albums.user_id','albums.id as album_id')
            ->get();
            $total_photos = DB::table('albums')
            ->leftJoin('albumimages', 'albums.id', '=', 'albumimages.album_id')
            ->where('albumimages.user_id','=',$id)
            ->where('type','=',1)
            ->select('albumimages.album_media_path','albumimages.created_at','albums.name as album_name','albumimages.id as images_id')
            ->get();
            $total_videos = DB::table('albums')
            ->leftJoin('albumimages', 'albums.id', '=', 'albumimages.album_id')
            ->where('albumimages.user_id','=',$id)
            ->where('type','=',2)
            ->select('albumimages.album_media_path','albumimages.created_at','albums.name as album_name','albumimages.id as videos_id')
            ->get();
           
            return view('Media.media_steps')->with(array('total_album'=>$total_album,'total_videos'=>$total_videos,'total_photos'=>$total_photos,'user_info'=>$user_info,'datesNew'=>$timelineData,'dates'=>$dates));
        }
        public function sorted_data(Request $request){ 
            $data_id=$request['selected_id'];
            $month_year = $request['selected_val'];
            $date = $month_year; 
            $conv_date = date('Y-m', strtotime($date)); 
            return $sorted_data = DB::table('albums')
            ->leftJoin('albumimages', 'albums.id', '=', 'albumimages.album_id')
            ->where('albums.date','like' ,"%{$conv_date}%")
            ->where('albums.user_id','=',$data_id)
            ->select('albums.id as album_id','albums.timeline_cover','albums.date','albums.name as album_name','albums.user_id','albumimages.album_media_path','albumimages.created_at','albumimages.type','albumimages.id as media_id')
            ->get();
            // echo "<pre>"; print_r($sorted_data); echo "</pre>";
        }

        //compress_image
        function compress_image($source_url, $destination_url, $quality) {

//check GD is available or not
// if (extension_loaded('gd')) {
// echo "<br>GD support is loaded ";
// }else{
// echo "<br>GD support is NOT loaded ";
// }
// if(function_exists('gd_info')){
// echo "<br>GD function support is available ";
// }else{
// echo "<br>GD function support is NOT available ";
// }
            //$save_url = public_path('assets/ad_images/').$destination_url; 
            //$source_url= 'http://35.233.149.139/museum/public/assets/ad_images/slash1582813644.jpg'; 
            //print_r($source_url); die;
            $info = getimagesize($source_url);
            //print_r($info['mime']); die;
            if ($info['mime'] == 'image/jpeg')
                    $image = imagecreatefromjpeg($source_url);
            elseif ($info['mime'] == 'image/gif')
                    $image = imagecreatefromgif($source_url);
            elseif ($info['mime'] == 'image/png')
                    $image = imagecreatefrompng($source_url);
            imagejpeg($image, $destination_url, $quality);
            return $destination_url;
        }

        public function create_ads(Request $request){ 
            $post_request = $request->all(); //echo "<pre>"; print_r($post_request);  die;
            
            $country = DB::table('countries')->select('country_name')->where('country_id','=',$request['country'])->get()->toArray();
            $state = DB::table('states')->select('state_name')->where('state_id','=',$request['state'])->get()->toArray();
            $city = DB::table('cities')->select('city_name')->where('city_id','=',$request['city'])->get()->toArray();

            foreach($country as $key => $value){
                $get_country = $value->country_name; 
            }
            foreach($state as $key => $value){
                $get_state = $value->state_name; 
            }
            foreach($city as $key => $value){
                $get_city = $value->city_name; 
            }

            $fileNameToStore="";
            $this->validate($request, [
                'file' => 'mimes:jpeg,bmp,png,ogg',
            ]);
            if($request->hasFile('file')) { 
                $filename = $request->file->getClientOriginalName();
                $filesize = $request->file->getClientSize();
                $onlyfilename = pathinfo( $filename, PATHINFO_FILENAME);
                $image = $request->file('file'); 
                $image = $this->manage_adv($post_request); 
                // $extension = $request->file->getClientOriginalExtension(); 
                // $fileNameToStore = $onlyfilename.'_'.time().'.'.$extension; 
                // $image->move(public_path('assets/ad_images'), $fileNameToStore);
            }

            //added_code

            //added_code


            // $data = DB::table('advertisement')->insert(
            //     array(
            //             'ad_images' => $fileNameToStore,
            //             'ad_title' => $request->get('adv_title'),
            //             'ad_url'=> $request->get('adv_url'),
            //             'ad_country'=> $get_country,
            //             'ad_state'=> $get_state,
            //             'ad_city'=> $get_city,
            //             'created_at'=> date("Y-m-d")
            //         )
            //     );
                return redirect()->back()->with('success', 'added');
        }

        //compress_adv
        function manage_adv($post_request) { //echo "<pre>"; print_r($post_request);die;

            $country = DB::table('countries')->select('country_name')->where('country_id','=',$post_request['country'])->get()->toArray();
            $state = DB::table('states')->select('state_name')->where('state_id','=',$post_request['state'])->get()->toArray();
            $city = DB::table('cities')->select('city_name')->where('city_id','=',$post_request['city'])->get()->toArray();

            foreach($country as $key => $value){
                $get_country = $value->country_name; 
            }
            foreach($state as $key => $value){
                $get_state = $value->state_name; 
            }
            foreach($city as $key => $value){
                $get_city = $value->city_name; 
            }

            if ($_FILES["file"]["error"] > 0) {
                $error = $_FILES["file"]["error"];
            } 
            else if (($_FILES["file"]["type"] == "image/gif") || 
            ($_FILES["file"]["type"] == "image/jpeg") || 
            ($_FILES["file"]["type"] == "image/png") || 
            ($_FILES["file"]["type"] == "image/pjpeg")) {
                $fileNameToStore = 'slash'.time().'.'.'jpg';
                $url = public_path('assets/ad_images/').'slash'.time().'.'.'jpg';
                $filename = $this->compress_image($_FILES["file"]["tmp_name"], $url, 80); //print_r($filename); die;
                
                $data = DB::table('advertisement')->insert(
                    array(
                            'ad_images' => $fileNameToStore,
                            'ad_title' => $post_request['adv_title'],
                            'ad_url'=> $post_request['adv_url'],
                            'ad_country'=> $get_country,
                            'ad_state'=> $get_state,
                            'ad_city'=> $get_city,
                            'created_at'=> date("Y-m-d")
                        )
                    );
                    return redirect()->back()->with('success', 'added');

                /* Force download dialog... */
                header("Content-Type: application/force-download");
                header("Content-Type: application/octet-stream");
                header("Content-Type: application/download");
                /* Don't allow caching... */
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                /* Set data type, size and filename */
                header("Content-Type: application/octet-stream");
                header("Content-Transfer-Encoding: binary");
                //header("Content-Length: " . strlen($buffer));
                header("Content-Disposition: attachment; filename=$url");
                header('Content-Type: image/jpeg');
                /* Send our file... */
                //echo $buffer;
            }else {
                $error = "Uploaded image should be jpg or gif or png";
            }
        }
        //compress_adv

        function manage_image($post) { //print_r($post);die;
            if ($_FILES["file"]["error"] > 0) {
                $error = $_FILES["file"]["error"];
            } 
            else if (($_FILES["file"]["type"] == "image/gif") || 
            ($_FILES["file"]["type"] == "image/jpeg") || 
            ($_FILES["file"]["type"] == "image/png") || 
            ($_FILES["file"]["type"] == "image/pjpeg")) {
                $fileNameToStore = 'slash'.time().'.'.'jpg';
                $url = public_path('assets/ad_images/').'slash'.time().'.'.'jpg';
                $filename = $this->compress_image($_FILES["file"]["tmp_name"], $url, 80); //print_r($filename); die;
                
                $old_image = DB::table('advertisement')
                ->select('ad_images')
                ->where('type', 'slash')
                ->first(); 
                $old_image_name = $old_image->ad_images;
                $old_image_name_path =  public_path('assets/ad_images/').$old_image_name; //print_r($old_image_name_path); die;
                $Path = $old_image_name_path;
                if (file_exists($Path)){
                    if (unlink($Path)) {   
                        echo "success";
                    } else {
                        echo "fail";    
                    }   
                } else {
                    echo "file does not exist";
                }
                $res = DB::table('advertisement')
                ->where('type', 'slash')
                ->delete();
                $data = DB::table('advertisement')->insert(
                array(
                        'ad_images' => $fileNameToStore,
                        'type' => 'slash',
                        'ad_title' => '',
                        'ad_url'=> '',
                        'ad_country'=> '',
                        'ad_state'=> '',
                        'ad_city'=> '',
                        'created_at'=> date("Y-m-d")
                    )
                );
                return redirect()->back()->with('slash', 'added');
                //$image->move(public_path('assets/ad_images'), $fileNameToStore); 
                
                //$buffer = file_get_contents($url); //print_r($buffer); die;
                /* Force download dialog... */
                header("Content-Type: application/force-download");
                header("Content-Type: application/octet-stream");
                header("Content-Type: application/download");
                /* Don't allow caching... */
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                /* Set data type, size and filename */
                header("Content-Type: application/octet-stream");
                header("Content-Transfer-Encoding: binary");
                //header("Content-Length: " . strlen($buffer));
                header("Content-Disposition: attachment; filename=$url");
                header('Content-Type: image/jpeg');
                /* Send our file... */
                //echo $buffer;
            }else {
                $error = "Uploaded image should be jpg or gif or png";
            }
        }
        //compress_image
        public function create_slash(Request $request){ 
            $post = $request->all(); 
            $fileNameToStore="";
            $this->validate($request, [
                //'file' => 'mimes:jpeg,png,jpg|max:5000000',
                'file' => 'mimes:jpeg,png,jpg',
            ]);
            
            if($request->hasFile('file')) { 
                $size = $request->file('file')->getSize(); 
                if($size>15000000){
                    return redirect()->back()->with('upload_error', 'Please use jpeg format image.');
                }
                else{
                    $filename = $request->file->getClientOriginalName(); 
                    $filesize = $request->file->getClientSize(); 
                    $onlyfilename = pathinfo( $filename, PATHINFO_FILENAME); //echo "<pre>"; 
                    $image = $this->manage_image($post); 
                    }
            }
            return redirect()->back()->with('slash', 'added');
        }
        public function advertise_list(){
            $rowCount = DB::table('countries')
            ->select('*')
            ->where('status','=',1)
            ->orderBy('country_name','asc')
            ->get();
            $data = DB::table('advertisement')
            ->select('*')
            //->where('ad_status','=',1)
            ->get();
            return view('Advertisement.adverstisement')->with(array('data'=>$data,'rowCount'=>$rowCount));
        }
        public function selected_states($id){ 
            return $stateCount = DB::table('states')
            ->select('*')
            ->where('country_id','=',$id)
            ->where('status','=',1)
            ->orderBy('state_name','asc')
            ->get();
        }
        public function selected_city($id){
            return $cityCount = DB::table('cities')
            ->select('*')
            ->where('state_id','=',$id)
            ->where('status','=',1)
            ->orderBy('city_name','asc')
            ->get();
        }
        public function ReportComplaints(Request $request)
        {
            //$reports = Complaint_Report::join('')->groupBy('user_id')->orderBy('id','desc')->get()->toArray();
            $total_complaints = DB::table('complaint_report')
            ->select(DB::raw('count(complaint_report.user_id) as total_complaint'))
            ->get();
            $reports = DB::table('complaint_report')
            ->select('albumimages.album_media_path as media_images','albumimages.type as media_types','complaint_report.id','complaint_report.user_id','users.userName as userName','users.profile_pic','users.email',DB::raw('DATE_FORMAT(complaint_report.created_at, "%d-%b-%Y") as created_at'))
            ->Join('users', 'users.id', '=', 'complaint_report.user_id')
            ->Join('albumimages', 'albumimages.id', '=', 'complaint_report.media_id')
            //->where('users.blocked_users','=',1)
            ->groupBy('complaint_report.user_id')
            ->orderBy('complaint_report.id','desc')
            ->limit(8)
            ->get()
            ->toArray();
            $data1 = array();
            foreach ($reports as $key => $value) {
                $reports1 = DB::table('complaint_report')
                ->select('albumimages.album_media_path as media_images','complaint_report.id','complaint_report.user_id','users.userName as userName','users.profile_pic','users.email',DB::raw('DATE_FORMAT(complaint_report.created_at, "%d-%b-%Y") as created_at'))
                ->Join('users', 'users.id', '=', 'complaint_report.user_id')
                ->Join('albumimages', 'albumimages.id', '=', 'complaint_report.media_id')
                ->where('complaint_report.user_id',$value->user_id)
                ->get()
                ->toArray();
                $data1[$value->user_id][] = $reports1;
            }
            $data = array(
                'reports'=>$reports,
                'counts'=>$data1
            );
            return view('Reports.reports', $data);
        }
        public function complaints_info($id){
            $complainant_info = DB::table('users')
            ->select('users.id as complainant_id','users.email','users.profile_pic','users.userName as user_name','albumimages.album_media_path','albums.name as album_name','albums.date as date','albumimages.type','complaint_report.media_id','complaint_report.id as complainant_report_id')
            ->join('complaint_report','complaint_report.user_id','=','users.id')
            ->join('albumimages','albumimages.id','=','complaint_report.media_id')
            ->join('albums','albums.id','=','albumimages.album_id')
            ->where('users.id','=',$id)
            ->get()
            ->toArray();

            $media_info = DB::table('complaint_report')
            ->select('complaint_report.media_id')
            ->where('complaint_report.user_id','=',$id)
            ->get();

            $data=array();
            foreach($media_info as $key => $value){
              $related_complainant = DB::table('complaint_report')
              ->leftjoin('users','users.id','=','complaint_report.user_id')
              ->select('complaint_report.user_id','complaint_report.media_id','users.id as realted_comp_id','users.email as realted_comp_email','users.profile_pic as realted_comp_pic','users.userName as realted_comp_name')
              ->where('complaint_report.media_id','=',$value->media_id)
              ->get()
              ->toArray();
              $data[] = $related_complainant;
            }
            $all_data = array(
                'complainant_info'=>$complainant_info,
                'complainant_data'=>$data
            );
            return view('Reports.view_complaints')->with(array('all_data'=>$all_data));
        }
        public function block_users($id, $status){
            if($status==0){
                DB::table('users')
                ->where('id', $id)
                ->update(['blocked_users' => 1]);
            }
            if($status==1){
                DB::table('users')
                ->where('id', $id)
                ->update(['blocked_users' => 0]);
            }
            return redirect()->back()->with('success', 'blocked');
        }
        public function delete_users($id){
            $user_info = DB::table('users')
            ->where('users.id', '=', $id)
            ->update(['inactive_users'=> 0]);
            return redirect()->back()->with('success', 'delete');
        }
        public function change_status($id, $status){
            if($status==0){
                DB::table('advertisement')
                ->where('id', $id)
                ->update(['ad_status' => 1]);
            }
            if($status==1){
                DB::table('advertisement')
                ->where('id', $id)
                ->update(['ad_status' => 0]);
            }
            return redirect()->back()->with('status', 'status');
        }
        public function delete_ad(Request $request){
            $ad_image = DB::table('advertisement')->select('ad_images')->where('id', $request['deleteid'])->first();
            $ad_image_path = public_path('assets/ad_images/'.$ad_image->ad_images);
            if(file_exists($ad_image_path)){
                unlink($ad_image_path);
            }
            $res = DB::table('advertisement')
            ->where('id', $request['deleteid'])
            ->delete();
            if($res) {
                echo "deleted";  
            } else {
                echo "Error";
            }
        }
        public function delete_complaint(Request $request){ 
            $res = DB::table('complaint_report')
            ->where('id', $request['deleteid'])
            ->delete();
            if($res) {
                echo "deleted";  
            } else {
                echo "Error";
            }
            return redirect()->back()->with('delete', 'media');
        }
        public function delete_complainant(Request $request){
            $res = DB::table('complaint_report')
            ->where('user_id', $request['deleteid'])
            ->delete();
            if($res) {
                echo "deleted";  
            } else {
                echo "Error";
            }
            return redirect()->back();
        }
        public function delete_album(Request $request){
            $del_albumimages = DB::table('albumimages')->select('album_media_path')->where('album_id', $request['deleteid'])->get()->toArray();
            $store_image = array();
            foreach($del_albumimages as $key => $value){
                $img_path = public_path('album_images/'.$value->album_media_path);
                if(file_exists($img_path)){
                    unlink($img_path);
                }
            }
            $del_album = DB::table('albums')->select('timeline_cover')->where('id', $request['deleteid'])->first();
            $del_album_path = public_path('album_images/'.$del_album->timeline_cover);
            if(file_exists($del_album_path)){
                unlink($del_album_path);
            }
            DB::table('albums')
            ->where('id', $request['deleteid'])
            ->delete();
            DB::table('albumimages')
            ->where('album_id', $request['deleteid'])
            ->delete();
            return redirect()->back()->with('delete', 'media');
        }
        public function delete_photos(Request $request){
            $del_photos = DB::table('albumimages')->select('album_media_path')->where('id', $request['deleteid'])->first();
            $del_photos_path = public_path('album_images/'.$del_photos->album_media_path);
            if(file_exists($del_photos_path)){
                unlink($del_photos_path);
            }
            DB::table('albumimages')
            ->where('id', $request['deleteid'])
            ->delete();
            DB::table('albums_views')
            ->where('album_media_id', $request['deleteid'])
            ->delete();
            return redirect()->back()->with('delete', 'media');
        }
        public function delete_videos(Request $request){
            $del_videos = DB::table('albumimages')->select('album_media_path')->where('id', $request['deleteid'])->first();
            $del_videos_path = public_path('album_images/'.$del_videos->album_media_path);
            if(file_exists($del_videos_path)){
                unlink($del_videos_path);
            }
            DB::table('albumimages')
            ->where('id', $request['deleteid'])
            ->delete();
            DB::table('albums_views')
            ->where('album_media_id', $request['deleteid'])
            ->delete();
        }
        public function delete_album_user(Request $request){
            $delete_album = DB::table('albums')->select('timeline_cover')->where('user_id', $request['deleteid'])->get()->toArray();
            foreach($delete_album as $key => $value){
                $delete_album_path = public_path('album_images/'.$value->timeline_cover);
                if(file_exists($delete_album_path)){
                    unlink($delete_album_path);
                }
            }

            $delete_media = DB::table('albumimages')->select('album_media_path')->where('user_id', $request['deleteid'])->get()->toArray();
            foreach($delete_media as $key => $value){
                $delete_media_path = public_path('album_images/'.$value->album_media_path);
                if(file_exists($delete_media_path)){
                    unlink($delete_media_path);
                }
            }
            DB::table('albums')
            ->where('user_id', $request['deleteid'])
            ->delete();
            DB::table('albumimages')
            ->where('user_id', $request['deleteid'])
            ->delete();
            DB::table('albums_views')
            ->where('user_id', $request['deleteid'])
            ->delete();
            return redirect()->back()->with('delete', 'media');
        }
        public function match_pass(Request $request){
            //print_r($request->all()); die;
            // $curren_password = bcrypt($request['curr_pass']);
            // $get_pass = DB::table('users')
            // ->select('password')
            // ->where('password', $curren_password)
            // ->first();

            // $json_data = $get_pass[0]->password; echo "<pre>"; print_r($json_data);
            
            // //$curren_password = $request['curr_pass']; 
            // echo "<pre>"; print_r($get_pass->password);
            // if($curren_password === $json_data){
            //     print_r('workinggg');
            // }
            // else{
            //     print_r('try');
            // }
        }
}
