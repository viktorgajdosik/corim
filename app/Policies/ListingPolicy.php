<?php

namespace App\Policies;

use App\Models\Listing;
use App\Models\User;

class ListingPolicy
{
    // Author-only actions (edit, update, destroy, show-manage)
    public function authorOnly(User $user, Listing $listing): bool
    {
        return $listing->user_id === $user->id;
    }

    // Non-author viewing (the public "show" page for participants)
    public function notAuthor(User $user, Listing $listing): bool
    {
        return $listing->user_id !== $user->id;
    }
}
