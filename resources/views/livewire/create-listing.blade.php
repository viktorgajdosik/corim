<x-card-form>
    @if (session()->has('message'))
        <div class="alert alert-success text-center">{{ session('message') }}</div>
    @endif

    <form wire:submit.prevent="submit" class="custom-floating-label">
        <!-- Title -->
        <div class="form-floating mb-3">
            <input type="text" class="form-control bg-dark text-white @error('title') is-invalid @enderror"
                   wire:model.live="title" id="title" placeholder="Title">
            <label for="title">Title</label>
            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
            <small class="form-text text-secondary">Minimum 10 characters required.</small>
        </div>

        <!-- Description -->
        <div class="form-floating mb-3">
            <textarea wire:model.live="description"
                      class="form-control bg-dark text-white @error('description') is-invalid @enderror"
                      id="description" placeholder="Description" style="height: 200px;"></textarea>
            <label for="description">Description</label>
            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
            <small class="form-text text-secondary">Minimum 50 characters required.</small>
        </div>

        <!-- Department -->
        <div class="form-floating mb-3">
            <select wire:model.live="department"
                    class="form-select bg-dark text-white @error('department') is-invalid @enderror"
                    id="department">
                <option value="" disabled>Select a department</option>
                <option value="Anaesthesiology, Resuscitation and Intensive Care Medicine">Anaesthesiology, Resuscitation and Intensive Care Medicine</option>
                <option value="Anatomy">Anatomy</option>
                <option value="Clinical Biochemistry">Clinical Biochemistry</option>
                <option value="Clinical Neurosciences">Clinical Neurosciences</option>
                <option value="Craniofacial Surgery">Craniofacial Surgery</option>
                <option value="Dentistry">Dentistry</option>
                <option value="Emergency Medicine">Emergency Medicine</option>
                <option value="Epidemiology and Public Health">Epidemiology and Public Health</option>
                <option value="Forensic Medicine">Forensic Medicine</option>
                <option value="Gynecology and Obstetrics">Gynecology and Obstetrics</option>
                <option value="Hematooncology">Hematooncology</option>
                <option value="Histology and Embryology">Histology and Embryology</option>
                <option value="Hyperbaric Medicine">Hyperbaric Medicine</option>
                <option value="Imaging Methods">Imaging Methods</option>
                <option value="Internal Medicine">Internal Medicine</option>
                <option value="Medical Microbiology">Medical Microbiology</option>
                <option value="Molecular and Clinical Pathology and Medical Genetics">Molecular and Clinical Pathology and Medical Genetics</option>
                <option value="Nursing and Midwifery">Nursing and Midwifery</option>
                <option value="Oncology">Oncology</option>
                <option value="Pediatrics">Pediatrics</option>
                <option value="Pharmacology">Pharmacology</option>
                <option value="Physiology and Pathophysiology">Physiology and Pathophysiology</option>
                <option value="Rehabilitation and Sports Medicine">Rehabilitation and Sports Medicine</option>
                <option value="Surgical Studies">Surgical Studies</option>
            </select>
            <label for="department">Department</label>
            @error('department') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <!-- Submit Button with Spinner (not disabled) -->
        <div class="mb-3">
            <button type="submit"
                    class="btn btn-primary w-100 d-flex align-items-center justify-content-center">
                <span>Create Offer</span>
                <div wire:loading wire:target="submit" class="ms-2">
                    <div class="spinner-grow spinner-grow-sm text-dark" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </button>
        </div>
    </form>
</x-card-form>
