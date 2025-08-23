<?php

namespace App\Livewire;

use App\Models\Listing;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class EditListingForm extends Component
{
    public Listing $listing;

    public string $title = '';
    public string $description = '';
    public ?string $department = null;

    public function mount(Listing $listing): void
    {
        // authorize owner
        abort_if($listing->user_id !== Auth::id(), 403, 'Unauthorized Action');

        $this->listing = $listing;
        $this->resetForm();
    }

    protected function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'min:10', 'max:500'],
            'description' => ['required', 'string', 'min:50', 'max:5000'],
            'department'  => ['nullable', 'string'],
        ];
    }

    public function save(): void
    {
        $this->validate();

        $this->listing->title       = $this->title;
        $this->listing->description = $this->description;
        $this->listing->department  = $this->department;
        $this->listing->save();

        // refresh to get new updated_at
        $this->listing->refresh();
        $ts = $this->listing->updated_at?->getTimestamp();

        // re-render wrapper so data-updated-at changes in DOM
        $this->dispatch('$refresh')->to(EditListingPanel::class);
        $this->dispatch('listingUpdated')->to(EditListingPanel::class);

        // tell browser to stop spinner only after DOM shows the new updatedAt
        $this->dispatch(
            'listingDomShouldReflect',
            listingId: $this->listing->id,
            updatedAt: $ts,
            flash: ['message' => 'Listing updated successfully.', 'type' => 'success']
        );
    }

    public function resetForm(): void
    {
        // Optionally refresh to ensure we reset to the latest persisted values
        $this->listing->refresh();

        $this->title = (string) ($this->listing->title ?? '');
        $this->description = (string) ($this->listing->description ?? '');
        $this->department = $this->listing->department;

        // Clear any validation errors when resetting
        $this->resetValidation();
    }

    public function render()
    {
        $departments = [
            "Anaesthesiology, Resuscitation and Intensive Care Medicine",
            "Anatomy", "Clinical Biochemistry", "Clinical Neurosciences", "Craniofacial Surgery",
            "Dentistry", "Emergency Medicine", "Epidemiology and Public Health", "Forensic Medicine",
            "Gynecology and Obstetrics", "Hematooncology", "Histology and Embryology", "Hyperbaric Medicine",
            "Imaging Methods", "Internal Medicine", "Medical Microbiology",
            "Molecular and Clinical Pathology and Medical Genetics", "Nursing and Midwifery", "Oncology",
            "Pediatrics", "Pharmacology", "Physiology and Pathophysiology",
            "Rehabilitation and Sports Medicine", "Surgical Studies"
        ];

        return view('livewire.edit-listing-form', compact('departments'));
    }
}
