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
    return view('welcome');
});

Route::get('ambiguous', function () {
    return [
        gettext('One apple'),
        trans('messages.text'),
        pgettext('Apple context', 'One apple')
    ];
});

Route::get('plurals', function () {
    $count = rand(1, 20);
    return [
        //  Какой-то комментарий
        trans('Какая-то строка'),
        sprintf(
        // Making some apples
            ngettext('%d apple', '%d apples', $count),
            $count
        ),
        sprintf(
            npgettext('Apple context', '%d apple', '%d apples', $count),
            $count
        )
    ];
});

Route::get('dots', function () {
    return [
        trans('apples.one'),
        trans_choice('apples.count', 1),
        trans('apples.more.one'),
        trans('apples.more.two'),
    ];
});
