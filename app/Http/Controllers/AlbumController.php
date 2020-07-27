<?php

namespace App\Http\Controllers;
use App\Complaint_Report;
use Illuminate\Http\Request;
use App\Temporaryuploads;
use App\Albums;
use App\AlbumImages;
use App\Album_views;
use App\NewsFeed;
use App\Stories;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use App\User;
use App\Followers;

use \stdClass;

class AlbumController extends Controller
{
    public function uploadMedia(Request $request)
    {
        if($request->hasFile('media')){
            $file = $request->file('media');
            $allowedfileExtension=['jpeg','jpg','png','mp4','avi','flv','wmv','mpg', 'mpeg', '3gp'];
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
            $file->move(public_path('album_images'),$filename);
            $tempAdd = new Temporaryuploads;
            $tempAdd->user_id = $request->user->id;
            $tempAdd->file_path = $filename;
            $tempAdd->save();
            return response()->json([
                'status' => 1,
                'message' => config('constant.FILE_UPLOADED_SUCCESS')
            ]);
        }else{
            return response()->json([
                'status' => 0,
                'message' => config('constant.SELECT_FILE')
            ]);
        }
        
    } // uploadMedia Closing

    

    public function saveAlbum(Request $request)
    {   
        if(!$request->hasFile('media') || !isset($request->name) || !isset($request->place) || !isset($request->date))
            return response()->json([
                'status' => 0,
                'message' => config('constant.PARAMETER_MISSING')
            ]);
            
        $user_id = $request->user->id;
        $name = $request->name;
        $isAlbumAlreadyExist = Albums::where('name', '=', $name)->where('user_id', '=', $user_id)->first();
        
        if($isAlbumAlreadyExist)
        return response()->json([
            'status' => 1,
            'message' => config('constant.ALBUM_ALREADY_EXIST')
        ]);
     
        
        $allowedfileExtension=['jpeg','jpg','png','mp4', 'avi', 'flv', 'wmv', 'mpg', 'mpeg', '3gp'];

        foreach($request->file('media') as $fileValue)
        {
            $extension = $fileValue->getClientOriginalExtension();
            if(!in_array(strtolower($extension), $allowedfileExtension))
            return response()->json([
                'status' => 0,
                'message' => config('constant.FILE_TYPE_NOT_ALLOWED')
            ]);
        }
        $newAlbums = new Albums;
        $newAlbums->user_id = $user_id;
        $newAlbums->name = $name;
        $newAlbums->date =  $request->date;
        $newAlbums->place = $request->place;
        $newAlbums->description = isset($request->description)?$request->description:'';
        
        $newAlbums->save();
       
        $album_id = $newAlbums->id;

        // News Create
        $note = $request->user->firstName.' has created a album '.$name;
              $arr = array (
                   'user_id'=>$request->user->id,
                   'note'=>$note,
                   'description'=>$note,
                   'album_id'=>$album_id
              );
            
         $this->addNews($arr);

        $album_images_array = array();
        foreach($request->file('media') as $key=>$file)
        {
           
            $filename = $file->getClientOriginalName();

            //get type of file
            $valType = $this->getFileType($filename);
           
            $file->getRealPath();
            //Display File Mime Type
            $filename = time().$filename;
            $imgs = array('album_id'=>$album_id, 'user_id'=> $user_id, 'album_media_path'=>$filename);

            array_push($album_images_array, array('album_id'=>$album_id,'type'=>$valType, 'user_id'=> $user_id, 'album_media_path'=>$filename));
            //Move Uploaded File
            $file->move(public_path('album_images'),$filename);
        }
       
        AlbumImages::insert($album_images_array);
        return response()->json([
            'status' => 1,
            'message' => config('constant.ALBUM_SAVED_SUCCESS')
        ]);
    }

    
    public function updateAlbumApi(Request $request)
    {   
         
        if(!$request->hasFile('media') || !isset($request->name) || !isset($request->place) || !isset($request->date) || !isset($request->stories_id))
            return response()->json([
                'status' => 0,
                'message' => config('constant.PARAMETER_MISSING')
            ]);

        $user_id = $request->user->id;
        $name = $request->name;
        $stories_id = $request->stories_id;


        $album_id = $request->id;
        $whereArr = [['album_id', '=', $album_id],['user_id', '=', $request->user->id]];

        
        // $isStories = Stories::where('user_id', '=', $user_id)->where('id', '=', $stories_id)->count();

        // if(!$isStories)
        // return response()->json([
        //     'status' => 1,
        //     'message' => "Stories Not added Please add Stories."
        // ]);

        $allowedfileExtension=['jpeg','jpg','png','mp4', 'avi', 'flv', 'wmv', 'mpg', 'mpeg', '3gp'];

        foreach($request->file('media') as $fileValue)
        {
            $extension = $fileValue->getClientOriginalExtension();
            if(!in_array(strtolower($extension), $allowedfileExtension))
            return response()->json([
                'status' => 0,
                'message' => config('constant.FILE_TYPE_NOT_ALLOWED')
            ]);
        }
        

        // $note = $request->user->firstName.' has created a album '.$name;
        //       $arr = array (
        //            'user_id'=>$request->user->id,
        //            'note'=>$note,
        //            'description'=>$note,
        //            'album_id'=>$album_id
        //       );
            
        //$this->addNews($arr);

        $album_images_array = array();
        foreach($request->file('media') as $file)
        {
            $filename = $file->getClientOriginalName();

            $valType = $this->getFileType($filename);

            $file->getRealPath();
            //Display File Mime Type
            $filename = time().$filename;
            array_push($album_images_array, array('album_id'=>$album_id,'type'=>$valType,'user_id'=> $user_id, 'album_media_path'=>$filename));
            //Move Uploaded File
            $file->move(public_path('album_images'),$filename);
        }
        
        AlbumImages::where($whereArr)->update($album_images_array);
        return response()->json([
            'status' => 1,
            'message' => config('constant.ALBUM_SAVED_SUCCESS')
        ]);
    }

    public function userAlbumList(Request $request)
    {
        $user_id = $request->user->id;
        $userAlbums = Albums::where('user_id', '=', $user_id)->get()->toArray();
       
       
        if(!count($userAlbums))
            return response()->json([
            'status' => 0,
            'message' => config('constant.NO_ALBUM_FOUND')
        ]);
        $img_path = URL("public/album_images");
        foreach ($userAlbums as $key => $value) {
           // print_r($value['']);die;
           // $stories = Stories::select('id', 'stories_name')->where('id',$value['stories_id'])->orderBy('id','desc')->first()->toArray();
            $userImages = AlbumImages::select('id','type', DB::Raw('CONCAT("'.$img_path.'","/", album_media_path) as album_media_path'))->where('album_id', '=', $value['id'])->get()->toArray();
            //$userAlbums[$key]['stories'] = $stories;
            $userAlbums[$key]['album_images'] = $userImages;
        }
        

        return response()->json([
            'status' => 1,
            'message' => config('constant.ALBUM_LIST'),
            'data'=>$userAlbums
        ]);
    }

   

    public function updateAlbum(Request $request)
    {
        $id =  $request->id;
        if(isset($request->date))
        $dataToUpdate = ['date'=>$request->date];
        else if(isset($request->place))
        $dataToUpdate = ['place'=>$request->place];
        else
        return response()->json([
            'status' => 0,
            'message' => config('constant.PARAMETER_MISSING')
        ]);
        
        if(!isset($request->id))
            return response()->json([
                'status' => 0,
                'message' => config('constant.PARAMETER_MISSING')
            ]);

        $whereArr = [['id', '=', $id],['user_id', '=', $request->user->id]];

        if( Albums::where($whereArr)->first() ){
            $updated = Albums::where($whereArr)->update($dataToUpdate);
              

            if($updated){
                $note = $request->user->firstName.' has updated a album ';
                $arr = array (
                    'user_id'=>$request->user->id,
                    'note'=>$note,
                    'description'=>$note,
                    'album_id'=>$id
                );
                $this->addNews($arr);
                return response()->json([
                    'status' => 1,
                    'message' => config('constant.ALBUM_UPDATED')
                ]);
            } else {
                return response()->json([
                    'status' => 1,
                    'message' => config('constant.SOMETHING_WENT_WRONG')
                ]);
            }
            
           
           
           
        }else{
            return response()->json([
                'status' => 0,
                'message' => config('constant.NO_ALBUM_FOUND')
            ]);
        }
    }

    public function deleteAlbum(Request $request)
    {
        $id =  $request->id;
        if(!isset($request->id))
            return response()->json([
                'status' => 0,
                'message' => config('constant.PARAMETER_MISSING')
            ]);
        $whereArr = [['id', '=', $id],['user_id', '=', $request->user->id]];
        if( Albums::where($whereArr)->first() ){
            $deleted = Albums::where($whereArr)->delete();
            
            if($deleted){
                   // News Create
                //    $note = $request->user->firstName.' has deleted a album';
                //    $arr = array (
                //        'user_id'=>$request->user->id,
                //        'note'=>$note,
                //        'description'=>$note,
                //        'album_id'=>null
                //    );
                //    $this->addNews($arr);

                $AlbumImages = AlbumImages::where('album_id', '=', $id)->get();
                foreach ($AlbumImages as $value) {
                    if(\File::exists(public_path('album_images/'.$value->album_media_path))){
                        \File::delete(public_path('album_images/'.$value->album_media_path));
                    }
                }
                AlbumImages::where('album_id', '=', $id)->delete();
                return response()->json([
                    'status' => 1,
                    'message' => config('constant.ALBUM_DELETED')
                ]);
            }
            else
            return response()->json([
                'status' => 1,
                'message' => config('constant.SOMETHING_WENT_WRONG')
            ]);
        }else{
            return response()->json([
                'status' => 0,
                'message' => config('constant.NO_ALBUM_FOUND')
            ]);
        }
    }

    public function deleteAlbumList(Request $request)
    {
        $id =  $request->id;
        if(!isset($request->id))
            return response()->json([
                'status' => 0,
                'message' => config('constant.PARAMETER_MISSING')
            ]);
        $whereArr = [['id', '=', $id],['user_id', '=', $request->user->id]];
        $albumids = $request->album_image_ids;
        if( Albums::where($whereArr)->first() ){
            //$deleted = Albums::where($whereArr)->delete();
            
           
                // News Create
                //    $note = $request->user->firstName.' has deleted a album';
                //    $arr = array (
                //        'user_id'=>$request->user->id,
                //        'note'=>$note,
                //        'description'=>$note,
                //        'album_id'=>null
                //    );
                //    $this->addNews($arr);

                $AlbumImages = AlbumImages::where('album_id', '=', $id)->get();
                
                foreach ($AlbumImages as $value) {
                     if(in_array($value->id,$albumids)) {
                        if(\File::exists(public_path('album_images/'.$value->album_media_path))){
                            \File::delete(public_path('album_images/'.$value->album_media_path));
                        }
                        AlbumImages::where('id', '=', $value->id)->delete();
                     }
                    
                }
                //AlbumImages::where('album_id', '=', $id)->delete();
                return response()->json([
                    'status' => 1,
                    'message' => config('constant.ALBUM_DELETED')
                ]);
            
        }else{
            return response()->json([
                'status' => 0,
                'message' => config('constant.NO_ALBUM_FOUND')
            ]);
        }
    }


    public function storiesCreate(Request $request){

        print_r($request->all());
        print_r($request['card']['number']);
        print_r($request['card']['month']);
        print_r($request['card']['year']);
        die;

        if(!isset($request->stories_name))
            return response()->json([
                'status' => 0,
                'message' => config('constant.PARAMETER_MISSING')
            ]);
          
           
          $user_id = $request->user->id;
          $name = $request->user->firstName;
          $storyName = $request->stories_name;

          $filter = Stories::where('user_id',$user_id)->where('stories_name',$request->stories_name)->count();

           if($filter) {
            return response()->json([
                'status' => 0,
                'message' => config('constant.STORY_DUPLICATE'),
            ]);
           }

           

          $stories = array('user_id'=>$user_id,'stories_name'=>$request->stories_name);
          $insert = Stories::create($stories);

          if($insert) {
            $note = $name.' has created a story '.$storyName;
              $arr = array (
                   'user_id'=>$request->user->id,
                   'note'=>$note,
                   'description'=>$note,
                   'stories_id'=>$insert->id
              );
            
              $this->addNews($arr);
              
          }



        return response()->json([
            'status' => 1,
            'message' => config('constant.STORY_SAVED_SUCCESS'),
            'data'=>$insert
            
        ]);
            
            
    }

    public function getStories(Request $request){

        if(!isset($request->user->id))
            return response()->json([
                'status' => 0,
                'message' => config('constant.PARAMETER_MISSING')
            ]);
          

          $user_id = $request->user->id;
          $stories = Stories::select('id','user_id','stories_name AS name','created_at')->where('user_id',$user_id)->orderBy('id','desc')->get()->toArray();
    
           if($stories) {
            return response()->json([
                'status' => 1,
                'message' => config('constant.GET_STORIES'),
                'data' =>$stories,  
            ]);
           } else {
            return response()->json([
                'status' => 0,
                'message' => 'Record not found.',
                'data' =>array(),  
            ]);
           }
   
            
    }

    public function userStoriesList(Request $request)
    {  
        $user_id = $request->user->id;
        $stories_id = $request->stories_id;
        $userAlbums = Albums::where('user_id', '=', $user_id)->where('stories_id', '=', $stories_id)->orderBy('id','desc')->get()->toArray();
      
        if(!count($userAlbums))
            return response()->json([
            'status' => 0,
            'message' => config('constant.NO_ALBUM_FOUND')
        ]);
        $img_path = URL("public/album_images");
        foreach ($userAlbums as $key => $value) {
            $userImages = AlbumImages::select('id','type', DB::Raw('CONCAT("'.$img_path.'","/", album_media_path) as album_media_path'))->where('album_id', '=', $value['id'])->get()->toArray();
            $userAlbums[$key]['album_images'] = $userImages;
        }
       
        
       

        return response()->json([
            'status' => 1,
            'message' => config('constant.ALBUM_LIST'),
            'data'=>$userAlbums
        ]);
       
    }


    public function TimelineAlbumList(Request $request)
    {
        //$user_id = $request->user->id;
        $userId = $request->user_id;
        $img_path = URL("public/album_images");
        $userAlbums = DB::table('albumimages')->rightJoin('albums', 'albums.id', '=', 'albumimages.album_id')->select('albums.id as albumid','albums.name','albums.date','albums.timeline_desc',DB::raw('DATE_FORMAT(albums.date, "%d") as day'),DB::raw("MONTHNAME(albums.date) as Month"),DB::raw("YEAR(albums.date) as Year"),DB::Raw('CONCAT("'.$img_path.'","/", albums.timeline_cover) as timeline_cover'),'albums.created_at')->where('albums.user_id','=',$userId)->orderBy('Year','desc')->orderBy('albumimages.id','desc')->get()->toArray();
         
        $timeMonths = [];
        $timelineData = [];
        if($userAlbums) {
            foreach ($userAlbums as $key => $value) {
                $date = $value->day.'-'.$value->Month.'-'.$value->Year;
                if(!in_array($date,$timeMonths)) {
                    array_push($timeMonths,$date);
                    array_push($timelineData,$value);
                }
                 
            }
        } 
       

        return response()->json([
            'status' => 1,
            'message' => config('constant.ALBUM_LIST'),
            'data'=>$timelineData
        ]);
    }


    public function userAlbumsList(Request $request)
    {  
        
        $userId = $request->user_id;
        $Year = $request->year;
        $Month = $request->month;
        $Day = $request->day;
        $date = $Year.'-'.$Month.'-'.$Day;
        $img_path = URL("public/album_images");

        
        $userStatus = User::select('user_status','country','state','city')->where('id',$userId)->first();
        $follow_status = $this->getFollowStatus($userId, $request->user->id);
        $advertiseMent='';
        if($userStatus->country && $userStatus->state && $userStatus->city) {
            $whereData = [
                ['ad_country', $userStatus->country],
                ['ad_state', $userStatus->state],
                ['ad_city', $userStatus->city],
                ['ad_status', 1],
            ];
            $img_path_adv = URL("public/ad_images");
            $advertiseMent = DB::table('advertisement')->select('ad_url',DB::Raw('CONCAT("'.$img_path_adv.'","/", ad_images) as images'))->where($whereData)->get()->toArray();
           
        }
       
        if($follow_status==2 && ($userStatus->user_status==2)) {   //follow user status ACCEPT && userstatus private
            $userAlbums = Albums::select('*',DB::raw("YEAR(date) as Year"),DB::Raw('CONCAT("'.$img_path.'","/", timeline_cover) as timeline_cover'))->where('user_id', '=', $userId)->where('date',$date)->whereYear('date', '=', $Year)->whereMonth('date', '=', $Month)->orderBy('id','desc')->get()->toArray();
            if(!count($userAlbums))
                return response()->json([
                'status' => 0,
                'message' => config('constant.NO_ALBUM_FOUND')
            ]);
            foreach ($userAlbums as $key => $value) {
                    $userImages = AlbumImages::select('id','type', DB::Raw('CONCAT("'.$img_path.'","/", album_media_path) as album_media_path'))->where('album_id', '=', $value['id'])->get()->toArray();
                    $albumViews = Album_views::where('album_id','=',$value['id'])->count();
                    $userAlbums[$key]['adverisement'] = $advertiseMent;
                    $userAlbums[$key]['album_views_count'] = $albumViews;

                    $new_data = array();
                    foreach($userImages as $k=>$v){
                        $mediaViews = Album_views::where('album_media_id','=',$v['id'])->count();
                        $v['media_views_count']=$mediaViews;
                        $new_data[] = $v;
    
                    }
                    $userAlbums[$key]['album_images'] = $new_data;
                    $userAlbums[$key]['album_advertisement'] = $advertiseMent;
            }
            return response()->json([
                'status' => 1,
                'message' => config('constant.ALBUM_LIST'),
                'data'=>$userAlbums
            ]);

        } else if($userStatus->user_status==1) { //userstatus public
         
            $userAlbums = Albums::select('*',DB::raw("YEAR(date) as Year"),DB::Raw('CONCAT("'.$img_path.'","/", timeline_cover) as timeline_cover'))->where('user_id', '=', $userId)->where('date',$date)->whereYear('date', '=', $Year)->whereMonth('date', '=', $Month)->orderBy('id','desc')->get()->toArray();
            if(!count($userAlbums))
                return response()->json([
                'status' => 0,
                'message' => config('constant.NO_ALBUM_FOUND')
            ]);
            foreach ($userAlbums as $key => $value) {
                    $userImages = AlbumImages::select('id','type', DB::Raw('CONCAT("'.$img_path.'","/", album_media_path) as album_media_path'))->where('album_id', '=', $value['id'])->get()->toArray();
                    $albumViews = Album_views::where('album_id','=',$value['id'])->count();
                    $userAlbums[$key]['adverisement'] = $advertiseMent;
                    $userAlbums[$key]['album_views_count'] = $albumViews;

                    $new_data = array();
                    foreach($userImages as $k=>$v){
                        $mediaViews = Album_views::where('album_media_id','=',$v['id'])->count();
                        $v['media_views_count']=$mediaViews;
                        $new_data[] = $v;
    
                    }
                    $userAlbums[$key]['album_images'] = $new_data;
            }
            return response()->json([
                'status' => 1,
                'message' => config('constant.ALBUM_LIST'),
                'data'=>$userAlbums
            ]);

        } else if($request->user->id==$request->user_id){
            
            $userAlbums = Albums::select('*',DB::raw("YEAR(date) as Year"),DB::Raw('CONCAT("'.$img_path.'","/", timeline_cover) as timeline_cover'))->where('user_id', '=', $userId)->where('date',$date)->whereYear('date', '=', $Year)->whereMonth('date', '=', $Month)->orderBy('id','desc')->get()->toArray();
            if(!count($userAlbums))
                return response()->json([
                'status' => 0,
                'message' => config('constant.NO_ALBUM_FOUND')
            ]);
            foreach ($userAlbums as $key => $value) {
                    $userImages = AlbumImages::select('id','type', DB::Raw('CONCAT("'.$img_path.'","/", album_media_path) as album_media_path'))->where('album_id', '=', $value['id'])->get()->toArray();
                    $albumViews = Album_views::where('album_id','=',$value['id'])->count();
                    $userAlbums[$key]['album_views_count'] = $albumViews;
                    $userAlbums[$key]['adverisement'] = $advertiseMent;
                    $new_data = array();
                    foreach($userImages as $k=>$v){
                        $mediaViews = Album_views::where('album_media_id','=',$v['id'])->count();
                        $v['media_views_count']=$mediaViews;
                        $new_data[] = $v;
                    }
                    $userAlbums[$key]['album_images'] = $new_data;
            }
            return response()->json([
                'status' => 1,
                'message' => config('constant.ALBUM_LIST'),
                'data'=>$userAlbums
            ]);

        } else  {   
            return response()->json([
                'status' => 1,
                'message' => 'This user private album or you need to follow user.',
            ]);
        }
       
    }


    // public function userAlbumsListByDate($requestDate){
        
    //     $getDate = $requestDate;
    //     print_r($getDate);die;
    //     $Year = $getDate[0];
    //     $Month = $getDate[1];
    //     $Day = $getDate[2];

    //     echo$userId = $userId;
    //     echo $date = $Year.'-'.$Month.'-'.$Day;
    //     $img_path = URL("public/album_images");
      

    //     $userAlbums = Albums::select('*',DB::raw("YEAR(date) as Year"),DB::Raw('CONCAT("'.$img_path.'","/", timeline_cover) as timeline_cover'))->where('user_id', '=', $userId)->where('date',$date)->whereYear('date', '=', $Year)->whereMonth('date', '=', $Month)->orderBy('id','desc')->get()->toArray();

    //     if(!count($userAlbums))
    //         return response()->json([
    //         'status' => 0,
    //         'message' => config('constant.NO_ALBUM_FOUND')
    //     ]);
      
    //     foreach ($userAlbums as $key => $value) {
    //             $userImages = AlbumImages::select('id','type', DB::Raw('CONCAT("'.$img_path.'","/", album_media_path) as album_media_path'))->where('album_id', '=', $value['id'])->get()->toArray();
    //             $albumViews = Album_views::where('album_id','=',$value['id'])->count();
    //             $userAlbums[$key]['album_views_count'] = $albumViews;
    //             $new_data = array();
    //             foreach($userImages as $k=>$v){
    //                 $mediaViews = Album_views::where('album_media_id','=',$v['id'])->count();
    //                 $v['media_views_count']=$mediaViews;
    //                 $new_data[] = $v;

    //             }
    //             $userAlbums[$key]['album_images'] = $new_data;
    //     }
       
    //     return response()->json([
    //         'status' => 1,
    //         'message' => config('constant.ALBUM_LIST'),
    //         'data'=>$userAlbums
    //     ]);
    // }

    public function userDetailsList(Request $request)
    {  
        $album_id = $request->id;
        $userAlbums = Albums::where('id', '=', $album_id)->orderBy('id','desc')->get()->toArray();
      
        if(!count($userAlbums))
            return response()->json([
            'status' => 0,
            'message' => config('constant.NO_ALBUM_FOUND')
        ]);
        $img_path = URL("public/album_images");
        foreach ($userAlbums as $key => $value) {
            $userImages = AlbumImages::select('id','type', DB::Raw('CONCAT("'.$img_path.'","/", album_media_path) as album_media_path'))->where('album_id', '=', $value['id'])->get()->toArray();
            $userAlbums[$key]['album_images'] = $userImages;
        }
       
        
        return response()->json([
            'status' => 1,
            'message' => config('constant.FOUND'),
            'data'=>$userAlbums
        ]);
       
    }

    
   
    public function getNews(Request $request){
        if(!$request->user->id){
          return response()->json([
              'status' => 0,
              'message' => config('constant.PARAMETER_MISSING')
          ]);
        }
       
        $user_id = $request->user->id;
        $FollowedList = DB::table('followers')->Join('users', 'users.id', '=', 'followers.following_id')->select('users.id')->where('follower_id','=',$user_id)->orWhere('following_id','=',$user_id)->where('status','=',2)->get();
                                            
            $ids = array();
            if(count($FollowedList)>0){
                foreach($FollowedList as $key=>$val){
                    $ids[] = $val->id;
                }
            }                                  
           
          $news_list = DB::table('newsfeed')->whereIn('user_id', $ids)->whereNotIn('user_id', [$user_id])->orderBy('id','desc')->get();
          $ret = array();
         
         if(count($news_list)>0){
              
            foreach ($news_list as $key => $value) {
                    $user_details = DB::table('users')->select('id','name','userName','firstName','lastName','email','profile_pic')->where('id', $value->user_id)->first();
                
                    $ret[$key]['news_title'] = $value->note;
                    $ret[$key]['user_details'] = $user_details;
                    
                    if($value->album_id) {
                        if($this->getNewsDeatils($value->album_id)){
                            $ret[$key]['album'] = $this->getNewsDeatils($value->album_id);
                        } else {
                            $ret[$key]['album'] = new stdClass();
                        }
                        
                    } else {
                        $ret[$key]['album'] = new stdClass();
                    }
                
            }

         } else {
            $ret['album'] = new stdClass();
         }
       if(count($ret)>0){
        return response()->json([
            'status' => 1,
            'message' => config('constant.GETFEED'),
            'data'=>$ret,
        ]);
       } else {
        return response()->json([
            'status' => 0,
            'message' => config('constant.NOTFOUND'),
            'data'=>$ret,
        ]);
       }
      
         
  } 

    public function getNewsDeatils($album_id){
            if($album_id) {
                $userAlbums = Albums::where('id', '=', $album_id)->get()->first();
                if(!$userAlbums)
                return '';
                
                $img_path = URL("public/album_images");
               
                //$m = new \Moment\Moment();
                //$userAlbums['date'] = time_elapsed($userAlbums->created_at);
                    
                    $userImages = AlbumImages::select('id','type', DB::Raw('CONCAT("'.$img_path.'","/", album_media_path) as album_media_path'))->where('album_id', '=', $userAlbums->id)->get()->toArray();
                    if(count($userImages)>0) {
                       
                        $userAlbums['album_images'] = $userImages;
                    } else {
                        $userAlbums['album_images'] = [];
                    }
                    
                        return $userAlbums;
                       
               
            } 
        }


        public function albumViewPostApi(Request $request){
            if($request->album_id || $request->album_media_id) {
                $userAlbums = Albums::where('id', '=', $request->album_id)->get()->first();
               if($userAlbums){
                $arr = array (
                    'album_id'=>isset($request->album_id) ? $request->album_id:null,
                    'album_media_id'=>isset($request->album_media_id) ? $request->album_media_id:null,
                    'user_id'=>$request->user->id
                );
                  $check = Album_views::where('album_media_id',$request->album_media_id)->where('user_id',$request->user->id)->first();
                  if(!$check) {
                    $feed = Album_views::create($arr);
                    return response()->json([
                        'status' => 1,
                        'message' => 'Record Added Succesfully.',
                        'data'=>$feed
                    ]);
                  } else {
                    return response()->json([
                        'status' => 1,
                        'message' => 'You have already views.',
                        'data'=>$check
                    ]);
                  }
                  
               } else  {
                return response()->json([
                    'status' => 0,
                    'message' => config('constant.NO_ALBUM_FOUND')
                ]);
               }
             
            } else {
                
                    return response()->json([
                        'status' => 0,
                        'message' => config('constant.PARAMETER_MISSING')
                    ]);
                  
            } 
        }

    public function albumViewGetApi(Request $request){
        if($request->album_id || $request->album_media_id) {
            
                $arr = array (
                    'album_id'=>isset($request->album_id) ? $request->album_id:null,
                    'album_media_id'=>isset($request->album_media_id) ? $request->album_media_id:null,
                    'user_id'=>$request->user->id
                );
            

                    if(isset($request->album_id)){
                        $feed = Album_views::where('album_id','=',$request->album_id)->count();
                    } else {
                        $feed = Album_views::where('album_media_id','=',$request->album_media_id)->count();
                    }
    
                return response()->json([
                    'status' => 1,
                    'message' => 'Record Succesfully.',
                    'data'=>$feed
                ]);
            
            
        } else {
                return response()->json([
                    'status' => 0,
                    'message' => config('constant.PARAMETER_MISSING')
                ]);
                
        } 
    }


    public function updateTimeline(Request $request)
    {

        if(!$request->hasFile('timeline_cover') || !isset($request->timeline_description) || !isset($request->month) || !isset($request->year))
        return response()->json([
            'status' => 0,
            'message' => config('constant.PARAMETER_MISSING')
        ]);

        $month = $request->month;
        $year = $request->year;
        $day = $request->day;

        $user_id = $request->user->id;
        $timeline_description = $request->timeline_description;
        $timeline_cover = $request->timeline_cover;
        
        $date = $year.'-'.$month.'-'.$day;
        $getDetails = Albums::where('user_id', '=', $user_id)->where('date',$date)->whereYear('date', '=', $year)->whereMonth('date', '=', $month)->get()->toArray();

        if(!count($getDetails)){
            return response()->json([
                'status' => 0,
                'message' => config('constant.NOTFOUND')
            ]);  
        }
            
            $allowedfileExtension=['jpeg','jpg','png'];
       
            $extension = $request->file('timeline_cover')->getClientOriginalExtension();
            if(!in_array(strtolower($extension), $allowedfileExtension))
            return response()->json([
                'status' => 0,
                'message' => config('constant.FILE_TYPE_NOT_ALLOWED')
            ]);
        
            $file = $request->file('timeline_cover'); 
            $filename = $file->getClientOriginalName();
            $file->getRealPath();
            //Display File Mime Type
            $filename = time().$filename;
            //Move Uploaded File
            $file->move(public_path('album_images'),$filename);
            $album_timeline = array('timeline_desc'=>$timeline_description, 'timeline_cover'=>$filename);
            Albums::where('user_id', '=', $user_id)->where('date',$date)->whereYear('date', '=', $year)->whereMonth('date', '=', $month)->update($album_timeline);

            $get_up = Albums::where('user_id', '=', $user_id)->where('date',$date)->whereYear('date', '=', $year)->whereMonth('date', '=', $month)->first();

            return response()->json([
                'status' => 1,
                'message' => config('constant.TIMELINE'),
                'data'=> $get_up
            ]);

    
    }

    public function postReport(Request $request){
      
           if(!isset($request->user_id) || !isset($request->media_id) || !isset($request->reports))

            return response()->json([
                'status' => 0,
                'message' => config('constant.PARAMETER_MISSING')
            ]);
           
            $check = Complaint_Report::where('user_id',$request->user_id)->where('media_id',$request->media_id)->get()->toArray();
            $data = array(
                'user_id'=>$request->user_id,
                'media_id'=>$request->media_id,
                'reports'=>$request->reports
            );

            if($check)  {
                $ret = Complaint_Report::where('user_id',$request->user_id)->where('media_id',$request->media_id)->update($data);
            } else {
                $ret = Complaint_Report::create($data);
            }
           

           
            if($ret){
                return response()->json([
                    'status' => 1,
                    'message' => config('constant.NEWSFEED')
                ]);
            } else {
                return response()->json([
                    'status' => 1,
                    'message' => config('constant.GET_ERROR')
                ]);
            }
     }

     public function getFileType($filename){
        $typeFile = explode('.',$filename);
        $n = explode('_',$typeFile[0]);
        $type = end($n);
        $valType = "";
        switch ($type) {
            case "image":
                $valType = 1;
                break;
            case "video":
                $valType = 2;
                break;
            case "360":
                $valType = 3;
                break;
            default:
            $valType = "";
         }
         return $valType;
    }

    public function uploadSingleMedia(Request $request){
        
        if($request->hasFile('single_media') && isset($request->id)){
           
            $file = $request->file('single_media');
           
            $allowedfileExtension=['jpeg','jpg','png','mp4','avi','flv','wmv','mpg', 'mpeg', '3gp'];
            //Display File Name
            $filename = $file->getClientOriginalName();
            //Display File Extension
            $extension = $file->getClientOriginalExtension();
            $user_id = $request->user->id; 
            $album_id = $request->id;

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
            $file->move(public_path('album_images'),$filename);

            $valType = $this->getFileType($filename);

            $album_images_array = array('album_id1'=>$album_id,'type'=>$valType, 'user_id'=> $user_id, 'album_media_path'=>$filename);
             
            //AlbumImages::insert($album_images_array);
            $tempAdd = new AlbumImages;
            $tempAdd->album_id = $album_id;
            $tempAdd->type = $valType;
            $tempAdd->user_id = $user_id;
            $tempAdd->album_media_path = $filename;
            $retu = $tempAdd->save();
    
            $img_path = URL("public/album_images");
           
            $ret = AlbumImages::select('id','album_id','type','user_id', DB::Raw('CONCAT("'.$img_path.'","/", album_media_path) as album_media_path'))->where('album_id',$album_id)->where('user_id',$user_id)->orderBy('id','desc')->first();

                $note = $request->user->firstName.' has created a media.';
                    $arr = array (
                        'user_id'=>$request->user->id,
                        'note'=>$note,
                        'description'=>$note,
                        'album_id'=>$album_id
                    );
                    
                  
                $this->addNews($arr);
        

            return response()->json([
                'status' => 1,
                'message' => config('constant.FILE_UPLOADED_SUCCESS'),
                'data' =>$ret
            ]);
        }else{
            return response()->json([
                'status' => 0,
                'message' => config('constant.PARAMETER_MISSING')
            ]);
        }
       
  }


  public function addNews($arr){
    if(!count($arr)){
      return response()->json([
          'status' => 0,
          'message' => config('constant.PARAMETER_MISSING')
      ]);
    }
    
    $tempAdd = new NewsFeed;
    $tempAdd->user_id = $arr['user_id'];
    $tempAdd->note = $arr['note'];
    $tempAdd->description = $arr['description'];
    $tempAdd->album_id = $arr['album_id'];
    $tempAdd->save();

    //$feed = NewsFeed::create($arr); 
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

public function slashImage(Request $request){  print_r('hi');
    $img_path_adv = URL("public/assets/ad_images");
    $advertiseMent = DB::table('advertisement')->select('*',DB::Raw('CONCAT("'.$img_path_adv.'","/", ad_images) as images'))->where('type','slash')->first();
    return response()->json([
        'status' => 1,
        'message' => 'Slash Image',
        'data' =>$advertiseMent
    ]);

    
}


    


}// Controller Class closing
