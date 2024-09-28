<x-search/>
<x-layout>
    <h3>Edit Listing</h3>
    <br>

    <x-card>
        <form method="POST" action="/listings/{{$listing->id}}">
            @csrf
            @method('PUT')

            <!-- Title Input with Floating Label -->
            <div class="form-floating mb-3">
                <input type="text" class="form-control form-control-md border-0 bg-light @error('title') is-invalid @enderror" id="title" name="title" placeholder="Title" value="{{ $listing->title }}">
                <label for="title">Title</label>
                @error('title')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Minimum 10 characters required.</small>
            </div>

            <!-- Description Input with Floating Label -->
            <div class="form-floating mb-3">
                <textarea class="form-control form-control-md border-0 bg-light @error('description') is-invalid @enderror" id="description" name="description" placeholder="Description" style="height: 200px">{{ $listing->description }}</textarea>
                <label for="description">Description</label>
                @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Minimum 50 characters required.</small>
            </div>

            <!-- Department Select with Floating Label -->
            <div class="form-floating mb-3">
                <select class="form-select form-control-md border-0 bg-light @error('department') is-invalid @enderror" id="department" name="department" aria-label="Department">
                    <option selected disabled>{{ $listing->department }}</option>
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
            <button type="submit" class="btn btn-primary btn-lg">Edit Offer</button>
        </form>
    </x-card>
</x-layout>
