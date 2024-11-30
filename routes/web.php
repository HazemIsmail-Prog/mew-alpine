<?php

use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\StakeholderController;
use App\Http\Controllers\StepController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::middleware('auth')->group(function () {

    Route::get('/', function () {
        return redirect()->route('documents.index');
    })->name('dashboard');

    Route::view('profile', 'profile')
        ->name('profile');

    Route::apiResource('/contracts', ContractController::class)->except('show');
    Route::controller(ContractController::class)->group(function () {
        Route::get('/contracts/getData', 'getData');
        Route::put('/contracts/{contract}/toggle-active', 'toggleActive');
    });

    Route::apiResource('/tags', TagController::class)->except('show');
    Route::controller(TagController::class)->group(function () {
        Route::get('/tags/getData', 'getData');
        Route::put('/tags/{tag}/toggle-active', 'toggleActive');
    });

    Route::apiResource('/stakeholders', StakeholderController::class)->except('show');
    Route::controller(StakeholderController::class)->group(function () {
        Route::get('/stakeholders/getData', 'getData');
        Route::put('/stakeholders/{stakeholder}/toggle-active', 'toggleActive');
    });

    Route::apiResource('/users', UserController::class)->except('show');
    Route::controller(UserController::class)->group(function () {
        Route::get('/users/getData', 'getData');
        Route::put('/users/{user}/toggle-active', 'toggleActive');
    });

    Route::controller(DocumentController::class)->group(function () {
        Route::get('/documents/getData', 'getData');
        Route::put('/documents/{document}/toggle-completed', 'toggleCompleted');
    });
    Route::apiResource('/documents', DocumentController::class);

    Route::apiResource('/documents/{document}/steps', StepController::class)->except('show');
    Route::put('/documents/{document}/steps/{step}/toggle-completed', [StepController::class, 'toggleCompleted']);
    Route::post('/documents/{document}/steps/reorder', [StepController::class, 'reorderSteps']);

    Route::apiResource('/documents/{document}/attachments', AttachmentController::class)->except('show');
});


require __DIR__ . '/auth.php';
