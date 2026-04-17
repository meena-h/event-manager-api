<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\EventStatusController;
use App\Http\Controllers\Api\ReminderController;
use App\Http\Controllers\Api\ReminderStatusController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::get('/profile',  [AuthController::class, 'profile']);
    Route::post('/logout',  [AuthController::class, 'logout']);

    // Events — CRUD
    Route::get('/events',         [EventController::class, 'index']);
    Route::post('/events',        [EventController::class, 'store']);
    Route::get('/events/{id}',    [EventController::class, 'show']);
    Route::put('/events/{id}',    [EventController::class, 'update']);
    Route::delete('/events/{id}', [EventController::class, 'destroy']);

    // Events — Status
    Route::post('/events/{id}/done',   [EventStatusController::class, 'markDone']);
    Route::post('/events/{id}/undone', [EventStatusController::class, 'markUndone']);
    Route::post('/events/{id}/cancel', [EventStatusController::class, 'cancel']);

    // Reminders — CRUD
    Route::get('/reminders',         [ReminderController::class, 'index']);
    Route::post('/reminders',        [ReminderController::class, 'store']);
    Route::get('/reminders/{id}',    [ReminderController::class, 'show']);
    Route::put('/reminders/{id}',    [ReminderController::class, 'update']);
    Route::delete('/reminders/{id}', [ReminderController::class, 'destroy']);

    // Reminders — Status
    Route::post('/reminders/{id}/done',   [ReminderStatusController::class, 'markDone']);
    Route::post('/reminders/{id}/undone', [ReminderStatusController::class, 'markUndone']);
    Route::post('/reminders/{id}/cancel', [ReminderStatusController::class, 'cancel']);
});