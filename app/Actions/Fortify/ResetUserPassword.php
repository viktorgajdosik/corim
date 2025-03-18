<?php

namespace App\Actions\Fortify;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\ResetsUserPasswords;

class ResetUserPassword implements ResetsUserPasswords
{
    /**
     * Validate and reset the user's password.
     */
    public function reset($user, array $input)
    {
        Validator::make($input, [
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ])->validate();

        // Update user password
        $user->forceFill([
            'password' => Hash::make($input['password']),
        ])->save();

        // Flash success message
        session()->flash('message', 'Your password has been reset successfully!');
    }
}
