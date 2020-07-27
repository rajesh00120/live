<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('Login/login');
});

Route::get('/login', function () {
    return view('Login/login');
});

// Route::get('/dashboard', function () {
//     return view('Dashboard.dashboard',array());
// });



// Route::get('/reports', function () {
//     return view('Reports/reports');
// });


Route::get('/users', function () {
          $arr = array('name'=>'hello');
    return view('Users/users',array('users'=>$arr));
});


Route::get('/dashboard','AdminController@DashboardPage');
Route::post('/getAjaxUsersList','AdminController@getAjaxUsersList');
Route::post('/loginPostUrl','AdminController@loginPostUrl');
Route::get('/reports','AdminController@ReportComplaints');
Route::get('/profile','AdminController@myprofile');
Route::post('/profile_edit/{id}','AdminController@edit_profile');
Route::post('/profile_logout','AdminController@logout_profile');
Route::post('/getAjaxMediaList','AdminController@getAjaxMediaList');
Route::get('/manage_media/{id}','AdminController@manage_media');
Route::get('/logout','AdminController@logout');
Route::post('/select_mnth','AdminController@sorted_data');
Route::post('/create_ads','AdminController@create_ads');
Route::post('/create_slash','AdminController@create_slash');

Route::get('/advertise','AdminController@advertise_list');
Route::get('/view_reports/{id}','AdminController@complaints_info');
Route::get('/block_users/{id}/{users_status}','AdminController@block_users');
Route::get('/delete_users/{id}','AdminController@delete_users');
Route::post('/select_state/{countryID}','AdminController@selected_states');
Route::post('/password_edit/{id}','AdminController@edit_password');
Route::post('/select_city/{stateID}','AdminController@selected_city');
Route::get('/pause_users/{id}/{status}','AdminController@change_status');
Route::get('/hello','AdminController@hello');
Route::post('/delete_ad','AdminController@delete_ad');
Route::post('/delete_complaint','AdminController@delete_complaint');
Route::post('/delete_complainant','AdminController@delete_complainant');
Route::post('/delete_album','AdminController@delete_album');
Route::post('/delete_photos','AdminController@delete_photos');
Route::post('/delete_videos','AdminController@delete_videos');
Route::post('/delete_album_user','AdminController@delete_album_user');

Route::post('/match_pass','AdminController@match_pass');

Route::get('/privacy_policy', function (){
    return view('terms_n_condition/privacy_policy');
});
Route::get('/terms_and_conditions', function (){
    return view('terms_n_condition/terms_and_conditions');
});
Route::get('/about_us', function (){
    return view('About/about_us');
});
Route::get('/media', function (){
    return view('Media/media');
});

Route::get('/view_complaints', function (){
    return view('Reports/view_complaints');
});


Route::get('/notifications', function (){
    return view('Notifications/notifications');
});





