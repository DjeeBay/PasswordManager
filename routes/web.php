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

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\IconController;
use App\Http\Controllers\PrivateCategoryController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laragear\TwoFactor\Http\Controllers\ConfirmTwoFactorCodeController;

Auth::routes();

Route::middleware(['auth'])->group(function() {
    $allowedIp = env('2FA_IP_BYPASS');
    $currentIp = $_SERVER['REMOTE_ADDR'];
    Route::middleware(env('ENABLE_TWO_FACTOR_AUTHENTICATION', false) && $currentIp !== $allowedIp ? '2fa.confirm' : 'auth')->group(function() {
        Route::get('/', 'HomeController@index')->name('home');
        Route::get('/home', 'HomeController@index')->name('home');

        Route::post('user/check-passphrase', [UserController::class, 'checkPassphrase'])->name('user.check_passphrase');
        Route::post('user/update-passphrase/{id}', [UserController::class, 'updatePassphrase'])->name('user.update_passphrase');
        Route::resource('/user', UserController::class);
        Route::resource('/category', CategoryController::class);
        Route::resource('/private-category', PrivateCategoryController::class);

        Route::post('/icon/add', 'IconController@add');
        Route::resource('/icon', IconController::class);

        Route::get('/keepass/import/', 'KeepassController@getImport')->name('keepass.get_import');
        Route::get('/keepass/{category_id}', 'KeepassController@get')->name('keepass.get');
        Route::get('/keepass/private/{private_category_id}', 'KeepassController@getPrivate')->name('keepass.get_private');
        Route::delete('/keepass/{category_id}/delete/{id}', 'KeepassController@delete')->name('keepass.delete');
        Route::delete('/keepass/private/{category_id}/delete/{id}', 'KeepassController@deletePrivate')->name('keepass.delete_private');
        Route::post('/keepass/{category_id}/save', 'KeepassController@save')->name('keepass.save');
        Route::post('/keepass/private/{category_id}/save', 'KeepassController@savePrivate')->name('keepass.save_private');
        Route::post('/keepass/{category_id}/create_multiple', 'KeepassController@createMultiple')->name('keepass.create-multiple');
        Route::post('/keepass/private/{category_id}/create_multiple', 'KeepassController@createMultiplePrivate')->name('keepass.create-multiple-private');
        Route::post('/keepass/import', 'KeepassController@import')->name('keepass.import');
        Route::get('/keepass/export/database', 'KeepassController@exportDatabase')->name('keepass.export_database');
        Route::get('/keepass/entry/{id}', 'KeepassController@getEntry')->name('keepass.get_entry');
        Route::get('/keepass/private/entry/{id}', 'KeepassController@getPrivateEntry')->name('keepass.get_private_entry');

        Route::get('/historic/index', 'HistoricController@index')->name('historic.index');
        Route::get('/historic/restore/{id}', 'HistoricController@restore')->name('historic.restore');

        Route::get('/favorite/index', 'FavoriteController@index')->name('favorite.index');
        Route::post('/favorite/removeMultiple', 'FavoriteController@removeMultiple')->name('favorite.remove-multiple');
        Route::post('/favorite/addMultiple', 'FavoriteController@addMultiple')->name('favorite.add-multiple');
        Route::post('/favorite/private/addMultiple', 'FavoriteController@addMultiplePrivate')->name('favorite.add-multiple-private');
    });

    Route::post('2fa/confirm', 'TwoFactorController@twoFactorConfirm')->name('2fa.confirm');
    Route::get('2fa/confirm', [ConfirmTwoFactorCodeController::class, 'form']);
    Route::post('2fa/code_check', 'TwoFactorController@twoFactorCodeCheck')->name('2fa.code_check');
    Route::get('auth_2fa', 'TwoFactorController@enableTwoFactor')->name('auth.two_factor');
});
