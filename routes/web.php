<?php

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('threads/create', 'ThreadController@create')->name('thread.create');
Route::get('threads/{channel}/{thread}', 'ThreadController@show')->name('thread.show');
Route::get('threads/{channel?}', 'ThreadController@index')->name('thread.index');
Route::post('threads', 'ThreadController@store')->name('thread.store');
Route::post('threads/{channel}/{thread}/replies', 'ReplyController@store')->name('reply.store');
Route::delete('threads/{channel}/{thread}', 'ThreadController@destroy')->name('thread.destroy');

Route::post('replies/{reply}/favourite', 'FavouriteController@store')->name('reply.favourite');

Route::get('profile/{user}', 'UserController@show')->name('user.show');
