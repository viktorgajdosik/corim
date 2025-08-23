<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class EditProfileForm extends Component
{
    public User $user;
    public string $name = '';
    public ?string $department = null;

    public function mount(User $user): void
    {
        abort_if($user->id !== Auth::id(), 403);
        $this->user = $user;
        $this->resetForm();
    }

    protected function rules(): array
    {
        return [
            'name'       => ['required', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function save(): void
    {
        $this->validate();

        $this->user->name = $this->name;
        $this->user->department = $this->department;
        $this->user->save();

        // refresh to get new updated_at
        $this->user->refresh();
        $ts = $this->user->updated_at?->getTimestamp();

        // re-render wrapper so data-updated-at changes in DOM
        $this->dispatch('$refresh')->to(EditProfilePanel::class);
        $this->dispatch('profileUpdated')->to(EditProfilePanel::class);

        // tell browser to stop spinner only after DOM shows the new updated_at
        $this->dispatch(
            'profileDomShouldReflect',
            userId: $this->user->id,
            updatedAt: $ts,
            flash: ['message' => 'Profile updated successfully.', 'type' => 'success']
        );
    }

    public function resetForm(): void
    {
        $this->user->refresh();
        $this->name = (string) ($this->user->name ?? '');
        $this->department = $this->user->department;
        $this->resetValidation();
    }

    public function render()
    {
        $departments = [
            'Student', 'Anaesthesiology, Resuscitation and Intensive Care Medicine',
            'Anatomy', 'Clinical Biochemistry', 'Clinical Neurosciences',
            'Craniofacial Surgery', 'Dentistry', 'Dermatovenerology', 'Emergency Medicine',
            'Epidemiology and Public Health', 'Forensic Medicine',
            'Gynecology and Obstetrics', 'Hematooncology', 'Histology and Embryology',
            'Hyperbaric Medicine', 'Imaging Methods', 'Internal Medicine',
            'Medical Microbiology', 'Molecular and Clinical Pathology and Medical Genetics',
            'Nursing and Midwifery', 'Oncology', 'Pediatrics', 'Pharmacology',
            'Physiology and Pathophysiology', 'Rehabilitation and Sports Medicine',
            'Surgical Studies',
        ];

        return view('livewire.edit-profile-form', compact('departments'));
    }
}
