<x-layout>

    <h2>Edit Profile</h2>
    <br>

    <x-card>
        <form method="POST" action="/users/profile">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="name">Name, Surname, Titles</label>
                <input type="text" class="form-control border-0 bg-light" id="name" name="name" value="{{$user->name}}">
                @error('name')
                <p class="text-danger mt-1">{{$message}}</p>
                @enderror
            </div>
            <div class="form-group">
                <label for="department">Department</label>
                <select class="form-control border-0 bg-light" id="department" name="department">
                    <option selected="selected" selected disabled>
                        {{$user->department}}
                        </option>
                        <option value="Student">Student</option>
                        <option value="Anaesthesiology, Resuscitation and Intensive Care Medicine">Anaesthesiology, Resuscitation and Intensive Care Medicine</option>
                        <option value="Anatomy">Anatomy</option>
                        <option value="Clinical Biochemistry">Clinical Biochemistry</option>
                        <option value="Clinical Neurosciences">Clinical Neurosciences</option>
                        <option value="Craniofacial Surgery">Craniofacial Surgery</option>
                        <option value="Dentistry">Dentistry</option>
                        <option value="Epidemiology and Public Health">Epidemiology and Public Health</option>
                        <option value="Forensic Medicine">Forensic Medicine</option>
                        <option value="Gynecology and Obstetrics">Gynecology and Obstetrics</option>
                        <option value="Hematooncology">Hematooncology</option>
                        <option value="Histology and Embryology">Histology and Embryology</option>
                        <option value="Imaging Methods">Imaging Methods</option>
                        <option value="Internal Medicine">Internal Medicine</option>
                        <option value="Medical Microbiology">Medical Microbiology</option>
                        <option value="Nursing and Midwifery">Nursing and Midwifery</option>
                        <option value="Oncology">Oncology</option>
                        <option value="Pediatrics">Pediatrics</option>
                        <option value="Rehabilitation and Sports Medicine">Rehabilitation and Sports Medicine</option>
                        <option value="Surgical Studies">Surgical Studies</option>
                        <option value="Pharmacology">Pharmacology</option>
                        <option value="Emergency Medicine">Emergency Medicine</option>
                        <option value="Molecular and Clinical Pathology and Medical Genetics">Molecular and Clinical Pathology and Medical Genetics</option>
                        <option value="Physiology and Pathophysiology">Physiology and Pathophysiology</option>
                        <option value="Hyperbaric Medicine">Hyperbaric Medicine</option>
                </select>
                @error('department')
                <p class="text-danger mt-1">{{$message}}</p>
                @enderror
            </div>
            <div class="form-group">
                <label for="old_password">Old Password</label>
                <input type="password" class="form-control border-0 bg-light" id="old_password" name="old_password" placeholder="Old Password">
                @error('old_password')
                <p class="text-danger mt-1">{{$message}}</p>
                @enderror
            </div>
            <div class="form-group">
                <label for="password">New Password</label>
                <input type="password" class="form-control border-0 bg-light" id="password" name="password" placeholder="New Password">
                @error('password')
                <p class="text-danger mt-1">{{$message}}</p>
                @enderror
            </div>
            <div class="form-group">
                <label for="password_confirmation">Confirm New Password</label>
                <input type="password" class="form-control border-0 bg-light" id="password_confirmation" name="password_confirmation" placeholder="Confirm New Password">
            </div>
            <button type="submit" class="btn btn-primary">Update Profile</button>
        </form>
    </x-card>
</x-layout>
