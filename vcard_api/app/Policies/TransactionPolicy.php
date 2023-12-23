<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TransactionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->user_type == 'V';
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Transaction $transaction): bool
    {
        return $user->user_type == 'V' && $user->id == $transaction->vcard;
    }

    /**
     * Determine whether the user can create models.
     */
    public function createDebit(User $user): bool
    {
        return $user->user_type == 'V';
    }

    public function createCredit(User $user): bool
    {
        return $user->user_type == 'A';
    }
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Transaction $transaction): bool
    {
        return $user->user_type == 'V' && $user->id == $transaction->vcard;
    }
}
