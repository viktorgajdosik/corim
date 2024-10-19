
<x-layout>
    <h3>Edit Profile</h3>
    <br>

    <x-card-form>
        <form method="POST" action="/users/profile">
            @csrf
            @method('PUT')

            <!-- Name Input with Floating Label -->
            <div class="form-floating mb-3">
                <input type="text" class="form-control form-control-md border-0 bg-white @error('name') is-invalid @enderror" id="name" name="name" value="{{ $user->name }}" placeholder="Name, Surname, Titles">
                <label for="name">Name, Surname, Titles</label>
                @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Department Select with Floating Label -->
            <div class="form-floating mb-3">
                <select class="form-select form-control-md border-0 bg-white" id="department" name="department" aria-label="Department">
                    <option selected disabled>{{ $user->department }}</option>
                    <option value="Student">Student</option>
                    <option value="Anaesthesiology, Resuscitation and Intensive Care Medicine">Anaesthesiology, Resuscitation and Intensive Care Medicine</option>
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

            <!-- Old Password Input (No "required") -->
            <div class="form-floating mb-3">
                <input type="password" class="form-control form-control-md border-0 bg-white @error('old_password') is-invalid @enderror" id="old_password" name="old_password" placeholder="Old Password">
                <label for="old_password">Old Password</label>
                @error('old_password')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- New Password Input (No "required") -->
            <div class="form-floating mb-3">
                <input type="password" class="form-control form-control-md border-0 bg-white @error('password') is-invalid @enderror" id="password" name="password" placeholder="New Password">
                <label for="password">New Password</label>
                @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Confirm New Password Input -->
            <div class="form-floating mb-3">
                <input type="password" class="form-control form-control-md border-0 bg-white" id="password_confirmation" name="password_confirmation" placeholder="Confirm New Password">
                <label for="password_confirmation">Confirm New Password</label>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-secondary btn-lg">Update Profile</button>
        </form>
    </x-card-form>
</x-layout>
