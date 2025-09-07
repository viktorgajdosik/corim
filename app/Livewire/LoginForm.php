<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginForm extends Component
{
    public string $email = '';
    public string $password = '';

    public function submit()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    }

    public function login()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Pre-check account status to show a clear message before attempting login
        $user = User::where('email', $this->email)->first();

        if ($user) {
            if ($user->banned_at) {
                $msg = 'Your account is banned.';
                if ($user->ban_reason) $msg .= ' Reason: '.$user->ban_reason;
                $this->addError('email', $msg);
                return;
            }
            if ($user->deactivated_at) {
                $this->addError('email', 'Your account is deactivated.');
                return;
            }
        }

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            session()->regenerate();
            return redirect()->intended('/users/profile');
        }

        $this->addError('email', 'Invalid email or password.');
    }

    public function render()
    {
        return view('livewire.login-form');
    }
}
