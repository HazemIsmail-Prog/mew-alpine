<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DocumentPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->type === 'superAdmin') {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->type === 'admin' || $user->type === 'user';
    }
    
    public function viewSteps(User $user): bool
    {
        return $user->type === 'admin' || $user->type === 'user';
    }
    
    public function viewAttachments(User $user): bool
    {
        return $user->type === 'admin' || $user->type === 'user';
    }
    
    public function create(User $user): bool
    {
        return $user->type === 'admin';
    }
    
    public function createSteps(User $user): bool
    {
        return $user->type === 'admin';
    }
    
    public function createAttachments(User $user): bool
    {
        return $user->type === 'admin';
    }
    
    public function update(User $user, Document $document): bool
    {
        return $user->type === 'admin';
    }
    
    public function updateSteps(User $user): bool
    {
        return $user->type === 'admin';
    }
    
    public function updateAttachments(User $user): bool
    {
        return $user->type === 'admin';
    }
    
    public function delete(User $user, Document $document): bool
    {
        return $user->type === 'admin';
    }
    
    public function deleteSteps(User $user): bool
    {
        return $user->type === 'admin';
    }
    
    function deleteAttachments(User $user): bool
    {
        return $user->type === 'admin';
    }
}
