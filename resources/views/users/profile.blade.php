
<x-layout>
    <div class="row align-items-stretch">
        <!-- Personal Information Section -->
        <div class="col-xl-3 mb-5">
            <h3>Personal Information</h3>
            <br>
            <x-card class="personal-info">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h5 class="mb-3 text-white">{{ $user->name }} </h5>
                        <p class="text-white"><i class="me-1 text-white fa fa-envelope"></i> {{ $user->email }} </p>
                        <p class="text-white"><i class="me-1 text-white fa fa-building"></i> {{ $user->department }} </p>
                        <p class="text-white"><i class="me-1 text-white fa fa-star"
                            data-bs-toggle="popover"
                            data-bs-trigger="hover"
                            data-bs-placement="bottom"
                            data-bs-content="You receive research points after successfully completing individual research tasks."></i> 35</p>
                    </div>
                    <a href="/users/edit-profile" class="btn btn-tertiary p-0">
                        <i class="fa fa-pencil"></i>
                    </a>
                </div>
            </x-card>
            <div class="mt-3">
                <a href="" class="btn btn-primary mb-2">Download Information</a>
                <form method="POST" action="/users/profile">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger d-inline" onclick="return confirm('Are you sure you want to delete your profile?')">Delete Profile</button>
                </form>
            </div>
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
            <div class="scrollable-listings h-75">
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
</x-layout>
