@extends('components.layout')
@section('content')
    <h3>Edit Profile</h3>
    <br>

    <x-card-form>
        <!-- ✅ FORM 1: UPDATE PROFILE INFO (NAME & DEPARTMENT) -->
        <form method="POST" action="{{ route('user-profile-information.update') }}" class="custom-floating-label">
            @csrf
            @method('PUT')

            <!-- Name Input -->
            <div class="form-floating mb-3">
                <input type="text" class="form-control form-control-md text-white border-1 border-secondary bg-dark @error('name') is-invalid @enderror"
                       id="name" name="name" value="{{ old('name', auth()->user()->name) }}" placeholder="Name, Surname, Titles">
                <label for="name">Name, Surname, Titles</label>
                @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Department Select -->
            <div class="form-floating mb-3">
                <select class="form-select form-control-md text-white border-1 border-secondary bg-dark @error('department') is-invalid @enderror"
                        id="department" name="department">
                    <option disabled {{ !auth()->user()->department ? 'selected' : '' }}>Select Your Department</option>
                    @foreach([
                        'Student', 'Anaesthesiology, Resuscitation and Intensive Care Medicine',
                        'Anatomy', 'Clinical Biochemistry', 'Clinical Neurosciences',
                        'Craniofacial Surgery', 'Dentistry', 'Dermatovenerology', 'Emergency Medicine',
                        'Epidemiology and Public Health', 'Forensic Medicine',
                        'Gynecology and Obstetrics', 'Hematooncology', 'Histology and Embryology',
                        'Hyperbaric Medicine', 'Imaging Methods', 'Internal Medicine',
                        'Medical Microbiology', 'Molecular and Clinical Pathology and Medical Genetics',
                        'Nursing and Midwifery', 'Oncology', 'Pediatrics', 'Pharmacology',
                        'Physiology and Pathophysiology', 'Rehabilitation and Sports Medicine',
                        'Surgical Studies'
                    ] as $department)
                        <option value="{{ $department }}" {{ auth()->user()->department == $department ? 'selected' : '' }}>
                            {{ $department }}
                        </option>
                    @endforeach
                </select>
                <label for="department">Department</label>
                @error('department')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Submit Button for Profile Update -->
            <button type="submit" class="btn-primary btn">Update Profile Info</button>
        </form>

        <hr class="my-4">

        <!-- ✅ FORM 2: UPDATE PASSWORD -->
        <form method="POST" action="{{ route('user-password.update') }}" class="custom-floating-label">
            @csrf
            @method('PUT')

            <!-- Old Password -->
            <div class="form-floating mb-3">
                <input type="password" class="form-control form-control-md border-1 border-secondary text-white bg-dark @error('old_password') is-invalid @enderror"
                       id="old_password" name="old_password" placeholder="Old Password">
                <label for="old_password">Old Password</label>
                @error('old_password')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- New Password -->
            <div class="form-floating mb-3">
                <input type="password" class="form-control form-control-md border-1 border-secondary text-white bg-dark @error('password') is-invalid @enderror"
                       id="password" name="password" placeholder="New Password">
                <label for="password">New Password</label>
                @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Confirm New Password -->
            <div class="form-floating mb-3">
                <input type="password" class="form-control form-control-md border-1 border-secondary text-white bg-dark"
                       id="password_confirmation" name="password_confirmation" placeholder="Confirm New Password">
                <label for="password_confirmation">Confirm New Password</label>
            </div>

            <!-- Submit Button for Password Update -->
            <button type="submit" class="btn-primary btn">Update Password</button>
        </form>
    </x-card-form>
    @endsection
