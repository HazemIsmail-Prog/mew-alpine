<?php

use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\StepController;
use App\Livewire\ContractIndex;
use App\Livewire\DocumentIndex;
use App\Livewire\StakeholderIndex;
use App\Livewire\UserIndex;
use App\Models\Contract;
use App\Models\Document;
use App\Models\Stakeholder;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::middleware('auth')->group(function () {

    Route::view('dashboard', 'dashboard')
        ->name('dashboard');

    Route::view('profile', 'profile')
        ->name('profile');

    Route::get('/users', UserIndex::class)->name('users.index')->can('viewAny', User::class);
    Route::get('/contracts', ContractIndex::class)->name('contracts.index')->can('viewAny', Contract::class);
    Route::get('/stakeholders', StakeholderIndex::class)->name('stakeholders.index')->can('viewAny', Stakeholder::class);
    Route::get('/documents', DocumentIndex::class)->name('documents.index')->can('viewAny', Document::class);

    Route::post('/steps', [StepController::class, 'store'])->can('createSteps', Document::class);
    Route::patch('/steps/{step}', [StepController::class, 'toggleCompleted'])->can('updateSteps', Document::class);
    Route::get('/steps/{document}', [StepController::class, 'getSteps'])->can('viewSteps', Document::class);
    Route::patch('/steps/{step}', [StepController::class, 'update'])->can('updateSteps', Document::class);
    Route::delete('/steps/{step}', [StepController::class, 'destroy'])->can('deleteSteps', Document::class);
    
    
    Route::post('/attachments', [AttachmentController::class, 'store'])->can('createAttachments', Document::class);
    Route::get('/attachments/{document}', [AttachmentController::class, 'getAttachments'])->can('viewAttachments', Document::class);
    Route::patch('/attachments/{attachment}', [AttachmentController::class, 'update'])->can('updateAttachments', Document::class);
    Route::delete('/attachments/{attachment}', [AttachmentController::class, 'destroy'])->can('deleteAttachments', Document::class);
});


require __DIR__ . '/auth.php';
