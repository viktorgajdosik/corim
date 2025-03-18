<?php

namespace App\Actions\Fortify;

use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    public function update($user, array $input)
    {
        Validator::make($input, [
            'name' => ['nullable', 'min:4', 'max:50'],
            'department' => ['nullable'],
        ])->validate();

        $user->update([
            'name' => $input['name'] ?? $user->name,
            'department' => $input['department'] ?? $user->department,
        ]);

        // Flash success message
        session()->flash('message', 'Your profile has been updated successfully!');
    }
}

