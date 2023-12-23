<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vcard;
use Illuminate\Auth\Access\Response;

class VcardPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->user_type == 'A';
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Vcard $vcard): bool
    {
        return $user->user_type == 'A' || $user->id == $vcard->phone_number;
    }
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Vcard $vcard): bool
    {
        return $user->user_type == 'A' || $user->id == $vcard->phone_number;
    }

    public function updateCredentials(User $user, Vcard $vcard): bool
    {
        return $user->user_type == 'V' && $user->id == $vcard->phone_number;
    }

    public function admin(User $user, Vcard $vcard): bool
    {
        return $user->user_type == 'A';
    }

    public function stats(User $user, Vcard $vcard): bool
    {
        return $user->user_type == 'V' &&  $user->id == $vcard->phone_number;
    }
    
    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Vcard $vcard): bool
    {
        return ($user->user_type == 'A' || $user->id == $vcard->phone_number) && $vcard->balance == 0;
    }
}
