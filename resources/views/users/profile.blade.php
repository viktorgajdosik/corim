@extends('components.layout')

@section('content')
<div class="row align-items-stretch">
    <!-- Personal Information Section -->
    <div class="col-xl-3">
        <x-secondary-heading>Personal Information</x-secondary-heading>
        <section
            x-data="{ modalOpen: false, countdown: 5, timer: null, confirming: false }"
            class="relative text-white"
            x-cloak>
            <x-card-form>

                <!-- Content shown only when modal is closed -->
                <template x-if="!modalOpen">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <x-card-heading>{{ $user->name }}</x-card-heading>
                            <x-text><i class="me-1 text-white fa fa-envelope"></i> {{ $user->email }}</x-text>
                            <x-text style="max-width: 220px; word-break: break-word;">
                                <i class="me-1 text-white fa fa-building"></i> {{ $user->department }}
                            </x-text>
                        </div>

                        <!-- Right-aligned stacked icons -->
                        <div class="d-flex flex-column align-items-end gap-3 mt-1">
                            <!-- Edit profile -->
                            <a href="/users/edit-profile" class="text-white" data-bs-toggle="tooltip" title="Edit Profile">
                                <i class="fa fa-pencil"></i>
                            </a>

                            <!-- Download info -->
                            <a href="#" class="text-white" data-bs-toggle="tooltip" title="Download Your Info">
                                <i class="fa fa-download"></i>
                            </a>

                            <!-- Trigger Delete Profile -->
                            <button
                                type="button"
                                class="btn p-0 text-white border-0 bg-transparent"
                                data-bs-toggle="tooltip"
                                title="Delete Profile"
                                @click="modalOpen = true">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </template>

                <!-- Modal shown only when modalOpen is true -->
                <template x-if="modalOpen">
                    <div class="p-3 border border-danger bg-transparent rounded">
                        <!-- First Step: Confirm -->
                        <div x-show="!confirming">
                            <p class="text-danger">Are you sure you want to delete your profile? This action is irreversible.</p>
                            <div class="d-flex gap-2 justify-content-end">
                                <button class="btn btn-outline-light rounded-pill btn-sm" @click="modalOpen = false">Cancel</button>
                                <button class="btn btn-outline-danger rounded-pill btn-sm" @click="
                                    confirming = true;
                                    countdown = 5;
                                    timer = setInterval(() => {
                                        if (countdown > 1) {
                                            countdown--;
                                        } else {
                                            clearInterval(timer);
                                            $refs.form.submit();
                                        }
                                    }, 1000);
                                ">Yes, Delete</button>
                            </div>
                        </div>

                        <!-- Countdown Step -->
                        <div x-show="confirming">
                            <p class="text-danger">Deleting in <strong x-text="countdown"></strong> seconds...</p>
                            <div class="d-flex justify-content-end">
                                <button class="btn btn-outline-light rounded-pill btn-sm" @click="
                                    clearInterval(timer);
                                    confirming = false;
                                    countdown = 5;
                                    modalOpen = false;
                                ">Cancel Deletion</button>
                            </div>
                        </div>

                        <!-- Hidden Delete Form -->
                        <form method="POST" action="{{ route('users.delete-profile') }}" x-ref="form" class="d-none">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </template>
            </x-card-form>
        </section>
    </div>

    <!-- Research Participation Section -->
    <div class="col-xl-9">
        <x-secondary-heading>Research Participation
            <i class="fa fa-info-circle ml-2 info-icon"
               data-bs-toggle="popover"
               data-bs-trigger="hover"
               data-bs-placement="bottom"
               data-bs-content="This section displays your participation in the research work created by other authors. You can see your own research listings by clicking 'Management' in the menu.">
            </i>
        </x-secondary-heading>
        <div class="scrollable-listings border-0 h-75">
            @if ($user->acceptedApplications->isEmpty())
                <x-text>You have not participated in other author's research work yet.</x-text>
            @else
                @foreach ($user->acceptedApplications as $application)
                    <x-listing-card :listing="$application->listing" />
                @endforeach
            @endif
        </div>
    </div>
</div>
@endsection
