<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return 'Marriage Planner routes working!';
})->name('marriage-planner.home');
