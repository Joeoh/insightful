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
    return view('welcome');
});

Auth::routes();



Route::get('/dashboard', 'CampaignController@index');

Route::get('/create', 'Campaign\NewCampaign@index');
Route::post('/create', 'Campaign\NewCampaign@store');

Route::get('/api/reviews-with-keyword/{campaign}/{aroundDate}/{keyword}', 'Campaign\ApiController@reviewsWithKeyword');

Route::get('/campaign/{id}', 'Campaign\Single@view');
Route::get('/campaign/{id}/insight', 'Campaign\Single@insight');
