<div class="register-form">
    <form wire:submit.prevent="register" class="custom-floating-label">
        @csrf

        <!-- Name Input -->
        <div class="form-floating mb-3">
            <input type="text" class="form-control form-control-md bg-dark text-white @error('name') is-invalid @enderror"
                   id="name" wire:model.live="name" placeholder="Forename, Surname, Titles">
            <label for="name">Forename, Surname, Titles</label>
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <!-- Email Input -->
        <div class="form-floating mb-3">
            <input type="email" class="form-control form-control-md bg-dark text-white @error('email') is-invalid @enderror"
                   id="email" wire:model.live="email" placeholder="Organisation email address">
            <label for="email">Organisation email address</label>
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <!-- Department Select -->
        <div class="form-floating mb-3">
            <select class="form-select form-control-md bg-dark text-white @error('department') is-invalid @enderror"
                    id="department" wire:model.live="department" required>
                @foreach([
                    'Select Your Department','Student', 'Anaesthesiology, Resuscitation and Intensive Care Medicine',
                    'Anatomy', 'Clinical Biochemistry', 'Clinical Neurosciences',
                    'Craniofacial Surgery', 'Dentistry', 'Dermatovenerology', 'Emergency Medicine',
                    'Epidemiology and Public Health', 'Forensic Medicine',
                    'Gynecology and Obstetrics', 'Hematooncology', 'Histology and Embryology',
                    'Hyperbaric Medicine', 'Imaging Methods', 'Internal Medicine',
                    'Medical Microbiology', 'Molecular and Clinical Pathology and Medical Genetics',
                    'Nursing and Midwifery', 'Oncology', 'Pediatrics', 'Pharmacology',
                    'Physiology and Pathophysiology', 'Rehabilitation and Sports Medicine',
                    'Surgical Studies'
                ] as $dept)
                    <option value="{{ $dept }}">{{ $dept }}</option>
                @endforeach
            </select>
            <label for="department">Department</label>
            @error('department') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <!-- Password Input -->
        <div class="mb-3">
            <div class="form-floating">
                <input type="password" class="form-control bg-dark text-white @error('password') is-invalid @enderror"
                       id="password" wire:model.live="password" placeholder="Password">
                <label for="password">Password</label>
            </div>
            @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>

        <!-- Confirm Password -->
        <div class="mb-3">
            <div class="form-floating">
                <input type="password" class="form-control bg-dark text-white @error('password_confirmation') is-invalid @enderror"
                       id="password_confirmation" wire:model.live="password_confirmation" placeholder="Confirm Password">
                <label for="password_confirmation">Confirm Password</label>
            </div>
            @error('password_confirmation') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <!-- Sign Up Button with Loading -->
        <div class="mb-3">
            <button type="submit" class="btn btn-sign btn-lg w-100 d-flex align-items-center justify-content-center"
                    wire:loading.attr="disabled">
                <span>Sign up</span>
                <div wire:loading wire:target="register" class="ms-2">
                    <div class="spinner-grow spinner-grow-sm text-dark" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </button>
        </div>

        <div class="mt-2 text-center text-white">
            Already have an account? <a href="{{ route('login') }}">Sign in</a>
        </div>
    </form>
</div>
