<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DocumentPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->role === 'superAdmin') {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->role === 'admin' || $user->role === 'user';
    }
    
    public function viewSteps(User $user): bool
    {
        return $user->role === 'admin' || $user->role === 'user';
    }
    
    public function viewAttachments(User $user): bool
    {
        return $user->role === 'admin' || $user->role === 'user';
    }
    
    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }
    
    public function createSteps(User $user): bool
    {
        return $user->role === 'admin';
    }
    
    public function createAttachments(User $user): bool
    {
        return $user->role === 'admin';
    }
    
    public function update(User $user, Document $document): bool
    {
        return $user->role === 'admin';
    }
    
    public function updateSteps(User $user): bool
    {
        return $user->role === 'admin';
    }
    
    public function updateAttachments(User $user): bool
    {
        return $user->role === 'admin';
    }
    
    public function delete(User $user, Document $document): bool
    {
        return $user->role === 'admin';
    }
    
    public function deleteSteps(User $user): bool
    {
        return $user->role === 'admin';
    }
    
    function deleteAttachments(User $user): bool
    {
        return $user->role === 'admin';
    }
}
