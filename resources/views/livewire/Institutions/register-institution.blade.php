<section>
  <x-card-form>
    <x-primary-heading class="mb-2">Register institution</x-primary-heading>
    <x-text class="text-muted-60 mb-3">
      Fill this form to request adding your institution to CORIM.
    </x-text>

    <form wire:submit.prevent="submit" class="custom-floating-label row g-3">

      <!-- Organisation name -->
      <div class="col-md-6">
        <div class="form-floating">
          <input type="text"
                 id="inst_name"
                 placeholder="Organisation name"
                 class="form-control form-control-md bg-dark text-white @error('name') is-invalid @enderror"
                 wire:model.live="name">
          <label for="inst_name">Organisation name</label>
          @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
      </div>

      <!-- Org email domain (no @) -->
      <div class="col-md-6">
        <div class="form-floating">
          <input type="text"
                 id="org_domain"
                 placeholder="example.edu"
                 class="form-control form-control-md bg-dark text-white @error('org_domain') is-invalid @enderror"
                 wire:model.live="org_domain">
          <label for="org_domain">Org email domain (without @)</label>
          @error('org_domain') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <small class="text-secondary d-block mt-1">
          Users from this institution will register with emails ending in
          @<span class="text-white-50">{{ $org_domain ?: 'example.edu' }}</span>
        </small>
      </div>

      <!-- Website (keeps http/https requirement) -->
      <div class="col-md-6">
        <div class="form-floating">
          <input type="url"
                 id="website_url"
                 placeholder="https://institution.example"
                 class="form-control form-control-md bg-dark text-white @error('website_url') is-invalid @enderror"
                 wire:model.live="website_url">
          <label for="website_url">Website (https://institution.example)</label>
          @error('website_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
      </div>

      <!-- Contact email -->
      <div class="col-md-6">
        <div class="form-floating">
          <input type="email"
                 id="contact_email"
                 placeholder="Contact email"
                 class="form-control form-control-md bg-dark text-white @error('contact_email') is-invalid @enderror"
                 wire:model.live="contact_email">
          <label for="contact_email">Contact email</label>
          @error('contact_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
      </div>

      <!-- Message -->
      <div class="col-12">
        <div class="form-floating">
          <textarea
            id="message"
            class="form-control form-control-md bg-dark text-white @error('message') is-invalid @enderror"
            placeholder="Tell us anything important…"
            style="height: 120px"
            wire:model.live="message"></textarea>
          <label for="message">Message (optional)</label>
          @error('message') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
      </div>

      <!-- Submit -->
      <div class="col-12">
        <button class="btn btn-primary d-inline-flex align-items-center" type="submit" wire:loading.attr="disabled">
          <span>Submit request</span>
          <span class="ms-2" wire:loading wire:target="submit">
            <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
          </span>
        </button>
      </div>
    </form>
  </x-card-form>
</section>
