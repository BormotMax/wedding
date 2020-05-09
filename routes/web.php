<?php

use Illuminate\Support\Facades\Route;

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
    return redirect('login');
});
Route::get('login', 'LoginController@index');
Route::post('login', 'LoginController@login')->name('login');
Route::middleware('auth')->group(function() {
    Route::middleware('admin')->prefix('admin')->group(function() {
        Route::get('/', 'AdminController@index')->name('admin.main');
        Route::resource('users', 'UsersManagementController');
        Route::resource('files', 'FilesController');
        Route::resource('folders', 'FoldersController');
        Route::put('files/access/{file}', 'FilesController@updateAccess');
        Route::put('folders/access/{folder}', 'FoldersController@updateAccess');
    });
    Route::get('files/get', 'FilesController@get');
    Route::get('folders/get', 'FoldersController@get');
    Route::get('roles', 'RolesController@index');
});
