@extends('components.layout')
@section('content')
    <div class="row align-items-stretch">
        <!-- Personal Information Section -->
        <div class="col-xl-3 mb-5">
            <h3>Personal Information</h3>
            <br>
            <x-card-form class="personal-info">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h5 class="mb-3 text-white">{{ $user->name }}</h5>
                        <p class="text-white"><i class="me-1 text-white fa fa-envelope"></i> {{ $user->email }}</p>
                        <p class="text-white" style="max-width: 220px; word-break: break-word;">
                            <i class="me-1 text-white fa fa-building"></i>
                            {{ $user->department }}
                        </p>

                    </div>


                <!-- Right-aligned stacked icons -->
<div class="d-flex flex-column align-items-end gap-2 mt-1">
    <!-- Edit profile -->
    <a href="/users/edit-profile" class="text-white" data-bs-toggle="tooltip" title="Edit Profile">
        <i class="fa fa-pencil"></i>
    </a>

    <!-- Download info -->
    <a href="#" class="text-white" data-bs-toggle="tooltip" title="Download Your Info">
        <i class="fa fa-download"></i>
    </a>

    <!-- Delete profile -->
    <form method="POST" action="{{ route('users.delete-profile') }}">
        @csrf
        @method('DELETE')
        <button type="submit"
                class="btn p-0 text-white border-0 bg-transparent"
                data-bs-toggle="tooltip"
                title="Delete Profile"
                onclick="return confirm('Are you sure you want to delete your profile? This action cannot be undone.')">
            <i class="fa fa-trash"></i>
        </button>
    </form>
</div>

                </div>

            </x-card-form>
        </div>

        <!-- Research Participation Section -->
        <div class="col-xl-9 mb-5">
            <h3>Research Participation
                <i class="fa fa-info-circle ml-2 info-icon"
                   data-bs-toggle="popover"
                   data-bs-trigger="hover"
                   data-bs-placement="bottom"
                   data-bs-content="This section displays your participation in the research work created by other authors. You can see your own research listings by clicking 'Management' in the menu.">
                </i>
            </h3>
            <br>
            <div class="scrollable-listings border-0 h-75">
                @if ($user->acceptedApplications->isEmpty())
                    <p class="text-white">You have not participated in other author's research work yet.</p>
                @else
                    @foreach ($user->acceptedApplications as $application)
                        <x-listing-card :listing="$application->listing" />
                    @endforeach
                @endif
            </div>
        </div>
    </div>
    @endsection
