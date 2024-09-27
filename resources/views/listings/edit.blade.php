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
                <input type="text" class="form-control form-control-lg border-0 bg-light @error('title') is-invalid @enderror" id="title" name="title" placeholder="Title" value="{{ $listing->title }}">
                <label for="title">Title</label>
                @error('title')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Minimum 10 characters required.</small>
            </div>

            <!-- Description Input with Floating Label -->
            <div class="form-floating mb-3">
                <textarea class="form-control form-control-lg border-0 bg-light @error('description') is-invalid @enderror" id="description" name="description" placeholder="Description" style="height: 200px">{{ $listing->description }}</textarea>
                <label for="description">Description</label>
                @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Minimum 50 characters required.</small>
            </div>

            <!-- Department Select with Floating Label -->
            <div class="form-floating mb-3">
                <select class="form-select form-control-lg border-0 bg-light @error('department') is-invalid @enderror" id="department" name="department" aria-label="Department">
                    <option selected disabled>{{ $listing->department }}</option>
                    <option value="Anaesthesiology, Resuscitation and Intensive Care Medicine">Anaesthesiology, Resuscitation and Intensive Care Medicine</option>
                    <option value="Anatomy">Anatomy</option>
                    <!-- Add other department options here -->
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
