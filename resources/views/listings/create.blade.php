<x-search/>
<x-layout>
    <h3>Create Research Listing
        <i class="fa fa-info-circle ml-2 info-icon"
           data-bs-toggle="popover"
           data-bs-trigger="hover"
           data-bs-placement="bottom"
           data-bs-content="This section enables you to list your research and include all the necessary information for potential participants. Note that you can always edit the listing later after creation.">
        </i>
    </h3>
    <br>
    <x-card>
        <form method="POST" action="/listings">
            @csrf

            <!-- Title Input with Floating Label -->
            <div class="form-floating mb-3">
                <input type="text" class="form-control form-control-lg border-0 bg-light @error('title') is-invalid @enderror" id="title" name="title" placeholder="Enter title" value="{{ old('title') }}">
                <label for="title">Title</label>
                @error('title')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Minimum 10 characters required.</small>
            </div>

            <!-- Description Input with Floating Label -->
            <div class="form-floating mb-3">
                <textarea class="form-control form-control-lg border-0 bg-light @error('description') is-invalid @enderror" id="description" name="description" placeholder="Enter description" style="height: 200px">{{ old('description') }}</textarea>
                <label for="description">Description</label>
                @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Minimum 50 characters required.</small>
            </div>

            <!-- Department Select with Floating Label -->
            <div class="form-floating mb-3">
                <select class="form-select form-control-lg border-0 bg-light @error('department') is-invalid @enderror" id="department" name="department">
                    <option value="" selected disabled>Select a department</option>
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
                @error('department')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary btn-lg">Create Offer</button>
        </form>
    </x-card>
</x-layout>
