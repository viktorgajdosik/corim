<x-head>
    <div class="container-fluid vh-100">
        <div class="row h-100">
            <div class="col-xl-5 d-none d-xl-flex flex-column align-items-center justify-content-center bg-primary text-white">
                <div class="text-wrapper" style="max-width: 400px; text-align: left;">
                    <h2 class="font-weight-bold">Collaborative Research</h2>
                    <p>
                        Create your account to join a dynamic community focused on advancing medical research. Connect with researchers, showcase your projects, and contribute to meaningful progress in healthcare.
                    </p>
                </div>
            </div>
            <div class="col-xl-7 d-flex align-items-center justify-content-center">
                <div class="card login-card" style="background-color: transparent; border: none;">
                    <div class="card-header border-bottom-0 text-center" style="background-color: transparent;">
                        <h2 class="font-weight-bold" style="color: #000000;">Sign Up</h2>
                        <p class="login-subtitle">Create your account.</p>
                    </div>
                    <div class="card-body">
                        <form action="/users" method="POST">
                            @csrf
                            <!-- Name Input -->
                            <div class="form-group">
                                <div class="input-group input-group-lg">
                                    <input type="text" class="form-control custom-input" id="name" name="name" placeholder="Forname, Surname, Titles" value="{{ old('name') }}">
                                </div>
                                @error('name')
                                <p class="text-danger mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email Input -->
                            <div class="form-group">
                                <div class="input-group input-group-lg">
                                    <input type="email" class="form-control custom-input" id="email" name="email" placeholder="Organisation email address" value="{{ old('email') }}">
                                </div>
                                @error('email')
                                <p class="text-danger mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Department Select -->
                            <div class="form-group">
                                <div class="input-group input-group-lg">
                                    <select class="form-control" id="department" name="department">
                                        <option value="" selected disabled>Select a department</option>
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
                                </div>
                                @error('department')
                                <p class="text-danger mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Password Input -->
                            <div class="form-group">
                                <div class="input-group input-group-lg">
                                    <input type="password" class="form-control custom-input-1" id="password" name="password" placeholder="Password">
                                    <div class="input-group-append">
                                        <span class="input-group-text icon-box-1" style="cursor: pointer;" onclick="togglePasswordVisibility('password')">
                                            <i class="fa fa-eye icon"></i>
                                        </span>
                                    </div>
                                </div>
                                @error('password')
                                <p class="text-danger mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Confirm Password Input -->
                            <div class="form-group">
                                <div class="input-group input-group-lg">
                                    <input type="password" class="form-control custom-input-1" id="password_confirmation" name="password_confirmation" placeholder="Confirm Password">
                                    <div class="input-group-append">
                                        <span class="input-group-text icon-box-1" style="cursor: pointer;" onclick="togglePasswordVisibility('password_confirmation')">
                                            <i class="fa fa-eye icon"></i>
                                        </span>
                                    </div>
                                </div>
                                @error('password_confirmation')
                                <p class="text-danger mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Sign Up Button -->
                            <div class="mt-3">
                                <button type="submit" class="btn-lg btn-primary btn-block" id="signupButton">Sign up</button>
                            </div>
                            <div class="mt-2 text-center">
                                Already have an account? <a href="/login">Sign in</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-head>
