<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return 'Game routes working!';
})->name('game.home');
