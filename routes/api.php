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

Route::namespace('Api')
    ->group(function () {

        Route::namespace('v1')->prefix('v1')->group(function () {
            Route::group(['prefix' => 'documents'], function () {

                Route::get('/', 'DocumentsController@index');
                Route::post('/', 'DocumentsController@store');

                Route::prefix('{document}')->group(function () {
                    Route::get('/', 'DocumentsController@show');
                    Route::delete('/', 'DocumentsController@destroy');

                    Route::prefix('attachment')->group(function () {
                        Route::get('/', 'DocumentsAttachmentController@index');

                        Route::prefix('previews')->group(function () {
                            Route::get('/', 'DocumentsAttachmentController@index');
                            Route::get('/{preview}', 'DocumentsAttachmentController@previews');
                            Route::get('/upload', 'DocumentsAttachmentController@upload');
                            Route::post('/upload', 'DocumentsAttachmentController@upload');
                        });
                    });
                });
            });
        });
    });