<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class UpdatePasswordForm extends Component
{
    public User $user;

    public string $old_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function mount(User $user): void
    {
        abort_if($user->id !== Auth::id(), 403);
        $this->user = $user;
    }

    protected function rules(): array
    {
        return [
            'old_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function save(): void
    {
        $this->validate();

        $this->user->password = Hash::make($this->password);
        $this->user->save();

        // Clear fields
        $this->old_password = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->resetValidation();

        // updated_at changed after save
        $this->user->refresh();
        $ts = $this->user->updated_at?->getTimestamp();

        // refresh wrapper and signal DOM anchor
        $this->dispatch('$refresh')->to(EditProfilePanel::class);
        $this->dispatch('profileUpdated')->to(EditProfilePanel::class);

        $this->dispatch(
            'profileDomShouldReflect',
            userId: $this->user->id,
            updatedAt: $ts,
            flash: ['message' => 'Password updated.', 'type' => 'success']
        );
    }

    public function render()
    {
        return view('livewire.update-password-form');
    }
}

