<?php

use Illuminate\Http\Request;
//use Symfony\Component\Routing\Annotation\Route;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
// Route::post('login', 'ApiController@login');
// Route::post('register', 'ApiController@register');

// if($_SERVER['HTTP_ORIGIN']==='http://localhost:3000'){
//     header('Access-Control-Allow-Origin:',  '*');

//     header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
//     header('Access-Control-Max-Age', '1000');
//     header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
// }






Route::get('/', function(){
    return 'Welcome to Museum';
});
Route::post('social_login', 'ApiController@socialLogin');
Route::post('register', 'ApiController@register');
Route::get('check-username/{search}', 'ApiController@checkUserName');
Route::post('login', 'ApiController@login');
Route::post('forgot-password', 'ApiController@forgotPassword');
Route::post('resend-otp', 'ApiController@resendOtp');
Route::post('verify-otp', 'ApiController@verificationOtp');
Route::patch('update-password/{id}', 'ApiController@update_Password');

 //Admin Controller Api
// Route::group(['middleware' => 'cors'], function () {
    Route::post('admin-login', 'AdminController@login');
    Route::get('slash-image', 'AlbumsController@slashImage');

    // Route::get('slash-image', function(){
    //     return 'Welcome to Museum';
    // });

Route::group(['middleware' => 'jwt.verify'], function () {

    Route::get('logout', 'ApiController@logout');
    Route::patch('update-user-status', 'ApiController@userStatus');
    Route::get('follow-request-list', 'ApiController@followUserRequestList');
    Route::patch('accept-reject', 'ApiController@acceptFollow');
    
    
    Route::get('user_profile', 'ApiController@getUserProfile');
    Route::get('search_people', 'ApiController@searchPeople');
    Route::post('follow_user', 'ApiController@followUser');
    Route::post('change_profile_pic', 'ApiController@changeProfilePic');

    Route::post('upload_media', 'AlbumsController@uploadMedia');
    Route::post('add_album', 'AlbumsController@saveAlbum');
    Route::post('testSaveAlbum', 'AlbumsController@testSaveAlbum');
    
    Route::get('user_albums', 'AlbumsController@userAlbumList');
    Route::get('timeline_list', 'AlbumsController@TimelineAlbumList');
    Route::get('testtimeline_list', 'AlbumsController@testTimelineAlbumList');

    Route::post('update-timeline', 'AlbumsController@updateTimeline');

    Route::post('update-albumCover', 'AlbumsController@updateAlbumCover');

    
    Route::patch('edit_album/{id}', 'AlbumsController@updateAlbum');
    Route::post('update_album/{id}', 'AlbumsController@updateAlbumApi');
    Route::get('get_user_details', 'ApiController@getUserDetails'); // User viewing Another User Profile
    Route::delete('delete_album/{id}', 'AlbumsController@deleteAlbum'); // User viewing Another User Profile
    Route::delete('delete_album_list/{id}', 'AlbumsController@deleteAlbumList'); // User viewing Another User Profile

    Route::get('user-albums-list', 'AlbumsController@userAlbumsList');
    Route::get('test_user-albums-list', 'AlbumsController@testUserAlbumsList');
    Route::get('albums-details/{id}', 'AlbumsController@userDetailsList'); 
    
    Route::post('upload_single_media', 'AlbumsController@uploadSingleMedia');
    

    //Views counts albums 
    Route::post('views-post', 'AlbumsController@albumViewPostApi');    
    Route::get('views-get', 'AlbumsController@albumViewGetApi');   
  
    // Stories
    Route::post('stories-create', 'AlbumsController@storiesCreate');    
    Route::get('stories', 'AlbumsController@getStories');
    Route::get('stories-detail', 'AlbumsController@userStoriesList');

    
   
    //follow
    Route::get('follower-list', 'ApiController@followerUserList');
    Route::get('following-list', 'ApiController@followingUserList');
    
    
     // Reports
     Route::post('report-post', 'AlbumsController@postReport');  
     
    
    //Admin Controller Api
    


    

    Route::get('get-news', 'AlbumsController@getNews');
    
    
});