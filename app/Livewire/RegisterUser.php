<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;

class RegisterUser extends Component
{
    public $name, $email, $department, $password, $password_confirmation;

    // Dynamic validation as the user types
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users|max:255',
            'department' => 'required|string',
            'password' => 'required|min:8|confirmed',
        ]);
    }

    public function register()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users|max:255',
            'department' => 'required|string',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'department' => $this->department,
            'password' => Hash::make($this->password),
        ]);

        event(new Registered($user)); // Fire event to send email verification

        Auth::login($user);

        return redirect()->route('verification.notice')->with('message', 'Registration successful! Please check your email for verification.');
    }

    public function render()
    {
        return view('livewire.register-user');
    }
}
