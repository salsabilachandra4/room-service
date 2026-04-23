<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RoomController;

Route::get('/rooms/available', [RoomController::class, 'available']);
Route::get('/rooms', [RoomController::class, 'index']);
Route::get('/rooms/{id}', [RoomController::class, 'show']);
Route::post('/rooms', [RoomController::class, 'store']);
Route::put('/rooms/{id}', [RoomController::class, 'update']);
Route::delete('/rooms/{id}', [RoomController::class, 'destroy']);
Route::get('/rooms/{id}/bookings', [RoomController::class, 'bookings']);
