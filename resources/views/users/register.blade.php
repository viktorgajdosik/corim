<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script src="//unpkg.com/alpinejs" defer></script>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon/apple-touch-icon.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon/favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon/favicon-16x16.png') }}">
        <link rel="manifest" href="{{ asset('favicon/site.webmanifest') }}">
        <link rel="mask-icon" href="{{ asset('favicon/safari-pinned-tab.svg') }}" color="#5bbad5">
        <meta name="msapplication-TileColor" content="#da532c">
        <meta name="theme-color" content="#ffffff">
        <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
        <script src="{{asset('js/script.js') }}"></script>

        <title>CORIM</title>
    </head>
    <body>
        <div class="container mt-5">
<h2>Sign up</h2>
<br>
<x-card>
<form action="/users" method="POST">
    @csrf
    <div class="form-group">
        <label for="name">Forname, Surname, Titles</label>
        <input type="text" class="form-control border-0 bg-light" id="name" name="name" placeholder="Forname, Surname, Titles" value="{{old('name')}}">
        @error('name')
        <p class="text-danger mt-1">{{$message}}</p>
        @enderror
    </div>
    <div class="form-group">
        <label for="email">Organisation email address</label>
        <input type="email" class="form-control border-0 bg-light" id="email" name="email" placeholder="Organisation email address" value="{{old('email')}}">
        @error('email')
        <p class="text-danger mt-1">{{$message}}</p>
        @enderror
    </div>
    <div class="form-group">
        <label for="department">Department</label>
        <select class="form-control border-0 bg-light" id="department" name="department">
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
        @error('department')
        <p class="text-danger mt-1">{{$message}}</p>
        @enderror
    </div>
    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" class="form-control border-0 bg-light" id="password" name="password" placeholder="Password" value="{{old('password')}}">
        @error('password')
        <p class="text-danger mt-1">{{$message}}</p>
        @enderror
    </div>
    <div class="form-group">
        <label for="password_confirmation">Confirm Password</label>
        <input type="password" class="form-control border-0 bg-light" id="password_confirmation" name="password_confirmation" placeholder="Confirm Password" value="{{old('password_confirmation')}}">
        @error('password_confirmation')
        <p class="text-danger mt-1">{{$message}}</p>
        @enderror
    </div>
    <button type="submit" class="btn btn-primary" id="signupButton" disabled>Sign up</button>
</form>
<div class="mt-3">
    Already have an account? <a href="/login">Sign in</a>
</div>
</x-card>
</div>


<x-flash-message />


</body>
</html>
