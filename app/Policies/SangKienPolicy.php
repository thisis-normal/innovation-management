<?php

namespace App\Policies;

use App\Models\SangKien;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class SangKienPolicy
{
    use HandlesAuthorization;
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['manager', 'secretary', 'admin']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SangKien $sangKien): bool
    {
        if ($user->hasRole(['manager', 'secretary', 'admin'])) {
            return true;
        }
        return $sangKien->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SangKien $sangKien): bool
    {
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SangKien $sangKien): bool
    {
        return true;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SangKien $sangKien): bool
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SangKien $sangKien): bool
    {
        return true;
    }
}
