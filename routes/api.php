<?php

use Illuminate\Http\Request;

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
Route::post('users/login', 'Api\UserController@login');

//Route::post('details', 'Api\UserController@details');

    Route::group(['middleware' => 'auth:api'], function(){
    	//user api routes
	Route::get('users/profile', 'Api\UserController@profile');
	Route::get('users/{user_id}', 'Api\UserController@get_user');
	Route::get('users/logout','Api\UserController@logout');
// Attendance api routes
	Route::get('attendances/{class_level}/{section}/{shift}/{session}/{date}','Api\AttendanceController@attendance_view');
	Route::post('attendances','Api\AttendanceController@attendance_create');
	Route::get('attendances/{attendance_id}','Api\AttendanceController@get_attendance');
	Route::put('attendances/{attendance_id}','Api\AttendanceController@update_attendance');
   Route::delete('attendances/{attendance_id}','Api\AttendanceController@deleted');

   //student
   Route::get('students/{class_code}/{section}/{shift}/{session}','Api\StudentController@student_classwise');
   Route::get('students/{student_id}','Api\StudentController@getstudent');
   Route::put('students/{student_id}','Api\StudentController@update_student');

   // classes
   Route::get('classes','Api\ClassController@classes');
   Route::get('classes/{class_id}','Api\ClassController@getclass');
   Route::put('classes/{class_id}','Api\ClassController@update_class');
	//Route::get('/attendance/create-file','attendanceController@index_file');
	//Route::post('/attendance/create-file','attendanceController@create_file');

	//Route::post('/attendance-view','Api\UserController@attendance_view');
	
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('products', function () {
    return response(['Product 1', 'Product 2', 'Product 3'],200);
});
