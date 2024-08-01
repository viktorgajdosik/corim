<x-search></x-search>
<x-layout>
<div class="row">

    <div class="col-md-6 mb-5">

        <h3 class="font-weight-bold">Personal Information</h3>
        <br>
        <x-card>
        <p><strong>Name:</strong> {{ $user->name }} </p>
        <p><strong>Email:</strong>  {{ $user->email }} </p>
        <p><strong>Department:</strong> {{ $user->department }}  </p>
        <p><strong>Research points:</strong> </p>
         <a href="/users/edit-profile" class="btn btn-primary mb-2"><i class="fa fa-pencil"></i> Edit profile</a><br>
         <a href="" class="btn btn-secondary mb-2">Download Information</a>
        <form method="POST" action="/users/profile">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete your profile?')">Delete Profile</button>
        </form>

        </x-card>

    </div>
    <div class="col-md-6 mb-5">
        <h3 class="font-weight-bold">Research Participation<i
            class="fa fa-info-circle ml-2 info-icon"
            data-toggle="popover"
            data-trigger="hover"
            data-placement="bottom"
            data-content="This section displays your participation in the research work created by other authors. You can see your own research listings by clicking 'Management' in the menu."
            ></i></h3>

        <br>
        <div class="scrollable-listings">
            @if ($user->acceptedApplications->isEmpty())
                <p>You have not participated in other author's research work yet.</p>
            @else
                @foreach ($user->acceptedApplications as $application)
                    <x-card>
                        <h4 class="mb-3">{{ $application->listing->title }}</h4>
                        <p class="mb-2">
                            <i class="fa fa-user" data-toggle="tooltip" title="Author"></i>
                            {{ $application->listing->author }}
                        </p>
                        <p>
                            <i class="fa solid fa-building" data-toggle="tooltip" title="Department"></i>
                            {{ $application->listing->department }}
                        </p>
                    </x-card>
                @endforeach
            @endif
        </div>
    </div>
</div>
</x-layout>

