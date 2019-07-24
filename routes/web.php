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

Route::get('/', 'HomeController@index')->name('home');

//Froentend Post
Route::get('post/{slug}', 'PostController@details')->name('post.details');
Route::get('posts', 'PostController@index')->name('post.index');

Route::post('subscriber','SubscriberController@store')->name('subscriber.store');

Auth::routes();

Route::group(['middleware' => ['auth']], function () {
    Route::post('favorite/{id}/add','FavoriteController@add')->name('post.favorite');

});

Route::group(['as'=>'admin.','prefix' => 'admin', 'namespace' => 'Admin','middleware'=>['auth','admin']], function () {
    Route::get('dashboard','DashboardController@index')->name('dashboard');

    //Admin Profile Update
    Route::get('settings','SettingsController@index')->name('settings');
    Route::put('profile/update','SettingsController@updateProfile')->name('profile.update');
    Route::put('password/update','SettingsController@updatePassword')->name('password.update');

    Route::resource('tag','TagController');
    Route::resource('category','CategoryController');

    //Dashboard Post
    Route::resource('post','PostController');
    Route::get('/pending/post','PostController@pending')->name('post.pending');
    Route::put('/post/{id}/approve','PostController@approval')->name('post.approve');

    Route::get('/subsriber','SubscriberController@index')->name('subscriber.index');
    Route::delete('/subsriber/{id}','SubscriberController@destroy')->name('subscriber.destroy');

    //Favorite Post
    Route::get('/favorite','FavoriteController@index')->name('favorite.index');
});

Route::group(['as'=>'author.','prefix' => 'author', 'namespace' => 'Author','middleware'=>['auth','author']], function () {
    Route::get('dashboard','DashboardController@index')->name('dashboard');

     //Author Profile Update
     Route::get('settings','SettingsController@index')->name('settings');
     Route::put('profile/update','SettingsController@updateProfile')->name('profile.update');
     Route::put('password/update','SettingsController@updatePassword')->name('password.update');

    Route::resource('post','PostController');

     //Favorite Post
     Route::get('/favorite','FavoriteController@index')->name('favorite.index');
});
