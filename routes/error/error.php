<?php
Route::view('/error/403', 'errors.403')->name('403');
Route::view('/error/404', 'errors.404')->name('404');
Route::view('/error/429', 'errors.429')->name('429');