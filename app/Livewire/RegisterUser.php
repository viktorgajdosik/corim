<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Institution;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class RegisterUser extends Component
{
    public $name, $email, $organization, $department, $password, $password_confirmation;

    /** @var array<string,string> name => domain */
    public array $orgMap = [];

    public function mount(): void
    {
        // Load institutions (approved table)
        $this->orgMap = Institution::orderBy('name')
            ->get(['name','domain'])
            ->pluck('domain','name')
            ->toArray();
    }

    protected function rules(): array
    {
        return [
            'name'         => ['required','string','max:255'],
            'organization' => ['required', Rule::in(array_keys($this->orgMap))],
            'email'        => ['required','email','max:255','unique:users,email'],
            'department'   => ['required','string'],
            'password'     => ['required','min:8','confirmed'],
        ];
    }

    public function register()
    {
        $validated = $this->validate();

        // Domain enforcement based on selected organization
        $required = $this->orgMap[$this->organization] ?? null;
        $emailLower = Str::lower($this->email);

        if ($required && !Str::endsWith($emailLower, '@'.$required)) {
            $this->addError('email', "For the selected organization, your email must end with @$required.");
            return;
        }

        $user = User::create([
            'name'         => $this->name,
            'email'        => $this->email,
            'organization' => $this->organization,  // still storing name string, as before
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
        return view('livewire.register-user', [
            'institutions' => array_keys($this->orgMap),
        ]);
    }
}
