@extends('components.layout')

@section('content')

   {{-- Breadcrumb Navigation --}}
<div class="d-flex justify-content-between align-items-center">
    <x-secondary-heading>Edit Listing</x-secondary-heading>
    <nav class="text-secondary fs-6 mb-3">
        <a href="{{ route('listings.manage') }}" class="text-decoration-none text-secondary">My Listings</a>
        <span class="mx-1">/</span>
        <a href="{{ route('listings.show-manage', ['listing' => $listing->id]) }}" class="text-decoration-none text-secondary">Manage</a>
        <span class="mx-1">/</span>
        <span class="text-white">Edit</span>
    </nav>
</div>

    <x-card-form>
        <form method="POST" action="/listings/{{ $listing->id }}" class="custom-floating-label" x-data="{ loading: false }" @submit="loading = true">
            @csrf
            @method('PUT')

            <!-- Title Input -->
            <div class="form-floating mb-3">
                <input type="text"
                       class="form-control form-control-md text-white bg-dark @error('title') is-invalid @enderror"
                       id="title"
                       name="title"
                       placeholder="Title"
                       value="{{ old('title', $listing->title) }}">
                <label for="title">Title</label>
                @error('title')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-secondary">Minimum 10 characters required.</small>
            </div>

            <!-- Description Input -->
            <div class="form-floating mb-3">
                <textarea class="form-control form-control-md text-white bg-dark @error('description') is-invalid @enderror"
                          id="description"
                          name="description"
                          placeholder="Description"
                          style="height: 200px">{{ old('description', $listing->description) }}</textarea>
                <label for="description">Description</label>
                @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-secondary">Minimum 50 characters required.</small>
            </div>

            <!-- Department Select -->
            <div class="form-floating mb-3">
                <select class="form-select form-control-md text-white bg-dark @error('department') is-invalid @enderror"
                        id="department"
                        name="department">
                    <option value="" disabled>Select a department</option>
                    @php
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
                    @endphp

                    @foreach ($departments as $dept)
                        <option value="{{ $dept }}" {{ $listing->department === $dept ? 'selected' : '' }}>{{ $dept }}</option>
                    @endforeach
                </select>
                <label for="department">Department</label>
                @error('department')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Submit Button with Spinner -->
            <div class="mb-3">
                <button type="submit"
                        class="btn btn-primary w-100 d-flex align-items-center justify-content-center">
                    <span>Edit Offer</span>
                    <div x-show="loading" class="ms-2">
                        <div class="spinner-grow spinner-grow-sm text-dark" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </button>
            </div>
        </form>
    </x-card-form>
@endsection
