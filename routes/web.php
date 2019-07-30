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

//Public Author Profile
Route::get('profile/{username}', 'AuthorController@profile')->name('author.profile');

//Froentend Post
Route::get('post/{slug}', 'PostController@details')->name('post.details');
Route::get('posts', 'PostController@index')->name('post.index');

//Category wise post
Route::get('category/{slug}', 'PostController@postByCategory')->name('category.posts');

//Tag wise post
Route::get('tag/{slug}', 'PostController@postByTag')->name('tag.posts');

Route::post('subscriber','SubscriberController@store')->name('subscriber.store');

//Search
Route::get('search', 'SearchController@search')->name('search');

Auth::routes();

Route::group(['middleware' => ['auth']], function () {
    Route::post('favorite/{id}/add','FavoriteController@add')->name('post.favorite');
    Route::post('comment/{id}','CommentController@store')->name('comment.store');

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

    //Author
    Route::get('/authors','AuthorController@index')->name('author.index');
    Route::delete('/authors/{id}','AuthorController@destroy')->name('author.destroy');

    //Comments
    Route::get('/comments','CommentController@index')->name('comment.index');
    Route::delete('/comments/{id}','CommentController@destroy')->name('comment.destroy');
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

     //Comments
    Route::get('/comments','CommentController@index')->name('comment.index');
    Route::delete('/comments/{id}','CommentController@destroy')->name('comment.destroy');
});

View::composer('layouts.frontend.partial.footer',function ($view){
    $categories = App\Category::all();
    $view->with('categories',$categories);
});
