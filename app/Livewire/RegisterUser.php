<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class RegisterUser extends Component
{
    public $name, $email, $organization, $department, $password, $password_confirmation;

    protected function rules(): array
    {
        return [
            'name'         => ['required','string','max:255'],
            'organization' => ['required', Rule::in([
                'Fakultní nemocnice Ostrava',
                'Lékařská fakulta Ostravské univerzity',
            ])],
            'email'        => ['required','email','max:255','unique:users,email'],
            'department'   => ['required','string'],
            'password'     => ['required','min:8','confirmed'],
        ];
    }

    public function register()
    {
        $validated = $this->validate();

        // Map organizations to required email domains
        $orgDomains = [
            'Fakultní nemocnice Ostrava'              => 'fno.cz',
            'Lékařská fakulta Ostravské univerzity'   => 'osu.cz',
        ];

        $required = $orgDomains[$this->organization] ?? null;
        $emailLower = Str::lower($this->email);

        if ($required && !Str::endsWith($emailLower, '@'.$required)) {
            $this->addError('email', "For the selected organization, your email must end with @$required.");
            return;
        }

        $user = User::create([
            'name'         => $this->name,
            'email'        => $this->email,
            'organization' => $this->organization,
            'department'   => $this->department,
            'password'     => Hash::make($this->password),
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect()
            ->route('verification.notice')
            ->with('message', 'Registration successful! Please check your email for verification.');
    }

    public function render()
    {
        return view('livewire.register-user');
    }
}
