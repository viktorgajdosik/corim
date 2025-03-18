<?php

namespace App\Actions\Fortify;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\UpdatesUserPasswords;

class UpdateUserPassword implements UpdatesUserPasswords
{
    public function update($user, array $input)
    {
        $validator = Validator::make($input, [
            'old_password' => ['required', 'min:8'],
            'password' => ['required', 'confirmed', 'min:8', 'different:old_password'],
        ], [
            'old_password.required' => 'Your old password is required.',
            'old_password.min' => 'The old password must be at least 8 characters.',
            'password.required' => 'You must provide a new password.',
            'password.confirmed' => 'The new password confirmation does not match.',
            'password.min' => 'The new password must be at least 8 characters.',
            'password.different' => 'The new password must be different from the old password.',
        ]);

        // Validate the input
        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }

        // Verify the old password
        if (!Hash::check($input['old_password'], $user->password)) {
            throw ValidationException::withMessages([
                'old_password' => 'The old password is incorrect.',
            ]);
        }

        // Update password
        $user->forceFill([
            'password' => Hash::make($input['password']),
        ])->save();

        // Flash success message
        session()->flash('message', 'Your password has been updated successfully!');
    }
}
