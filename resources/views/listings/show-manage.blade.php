<x-search></x-search>
<x-layout>

    <h3 class="font-weight-bold">Listing Management<i
        class="fa fa-info-circle ml-2 info-icon"
        data-toggle="popover"
        data-trigger="hover"
        data-placement="bottom"
        data-content="This section displays your research listing and enables you to edit or delete it."
        ></i></h3>
    <br>

    <x-card>
        <div class="date-container">
            <p class="date-created">
                <i class="fa fa-calendar" data-toggle="tooltip" title="Date Created"></i> {{ $listing->created_at->format('d/m/Y') }}
            </p>
        </div>
        <h4 class="listing-title mb-3">{{ $listing->title }}</h4>
            <span>
                <i class="fa fa-user" data-toggle="tooltip" title="Author"></i> {{ $listing->author }}
            </span>
            <span> | </span>
            <span>
                <i class="fa solid fa-building" data-toggle="tooltip" title="Department"></i> {{ $listing->department }}
            </span>
        <p class="text-secondary description mt-3 mb-3">{{ Str::limit($listing->description, 200) }}</p>

        <button type="button" class="btn btn-primary mb-2" onclick="window.location.href='/listings/{{$listing->id}}/edit'">
            <i class="fa fa-pencil"></i> Edit
        </button>
            <form method="POST" action="/listings/{{$listing->id}}">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this offer?')"><i class="fa fa-trash"></i> Delete</button>
            </form>
    </x-card>

    <br>
    <h3 class="font-weight-bold">Applications<i
        class="fa fa-info-circle ml-2 info-icon"
        data-toggle="popover"
        data-trigger="hover"
        data-placement="bottom"
        data-content="This section displays applications from users who would like to participate in this research work. You can accept or deny them."
        ></i></h3>
    <br>
   <!-- Loop through applications -->


    @forelse ($listing->applications->where('accepted', false) as $application)
    <x-card>
        <h4>{{ $application->user->name }}</h4>
        <br>
        <p><i class="fa fa-building"></i> {{ $application->user->department }}</p>
        <p><i class="fa fa-envelope"></i> {{ $application->user->email }}</p>
        <p><i class="fa fa-edit"></i> {{ $application->message }}</p>
        <br>

        <form method="POST" action="{{ route('listings.accept', ['application' => $application->id]) }}" class="m-0">
                @csrf
                <button class="btn btn-success mb-2" onclick="return confirm('Accept this application?')">
                    <i class="fa fa-check"></i> Accept
                </button>
            </form>
            <form method="POST" action="{{ route('listings.deny', ['application' => $application->id]) }}" class="m-0">
                @csrf
                <button class="btn btn-danger" onclick="return confirm('Deny this application?')">
                    <i class="fa fa-times"></i> Deny
                </button>
            </form>

    </x-card>
    @empty
        <p>Currently no applications.</p>
    @endforelse


	<br><br>

	<h3 class="font-weight-bold">Current Participants<i
        class="fa fa-info-circle ml-2 info-icon"
        data-toggle="popover"
        data-trigger="hover"
        data-placement="bottom"
        data-content="This section displays users with accepted applications. Here you can remove users from this research work."
        ></i></h3>
    <br>

@if ($listing->applications->where('accepted', true)->isEmpty())
    <p>Currently no participants.</p>
@else
    @foreach ($listing->applications->where('accepted', true) as $application)
        <x-card>
            <h4>{{ $application->user->name }}</h4>
            <br>
            <p><i class="fa fa-building"></i> {{ $application->user->department }}</p>
            <p><i class="fa fa-envelope"></i> {{ $application->user->email }}</p>
            <br>
            <form method="POST" action="{{ route('listings.deny', ['application' => $application->id]) }}">
                @csrf
                <button class="btn btn-danger" onclick="return confirm('Remove this participant?')">
                    <i class="fa fa-trash"></i> Remove
                </button>
            </form>
        </x-card>
    @endforeach
@endif
        <br>

   <h3 class="font-weight-bold">Tasks Management<i
    class="fa fa-info-circle ml-2 info-icon"
    data-toggle="popover"
    data-trigger="hover"
    data-placement="bottom"
    data-content="Here you can create tasks for this research listing and assign them to chosen participants."
    ></i></h3>
        <br>
    <x-card>
		<h4>Create Task</h4>
        <br>
        <form>
	    <div class="form-group">
		<label for="task-name">Task Name</label>
		<input type="text" class="form-control bg-light border-0" id="task-name">
	    </div>
	    <div class="form-group">
		<label for="task-details">Task Details</label>
		<textarea class="form-control bg-light border-0" id="task-details" rows="5"></textarea>
	    </div>
	    <div class="form-group upload">
        <label for="task-file">
        <i class="fa fa-file"></i> Upload File
        </label>
        <input type="file" class="form-control-file" id="task-file" name="task-file">
        </div>
        <div class="form-group">
            <label for="students">Assign task</label>
            @if ($listing->applications()->where('accepted', true)->exists())
                @foreach ($listing->applications()->where('accepted', true)->with('user')->get() as $applicant)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="assigned_users[]" value="{{ $applicant->user->id }}" id="{{ $applicant->user->id }}">
                        <label class="form-check-label" for="{{ $applicant->user->id }}">
                            {{ $applicant->user->name }}
                        </label>
                    </div>
                @endforeach
            @else
                <p>No users have accepted applications for this listing yet.</p>
            @endif
        </div>
            <button type="submit" class="btn btn-primary"> Create Task</button>
        </form>
    </x-card>
    <br>


<h3 class="font-weight-bold">Assigned Tasks<i
    class="fa fa-info-circle ml-2 info-icon"
    data-toggle="popover"
    data-trigger="hover"
    data-placement="bottom"
    data-content="This section displays assigned tasks that have not been submitted for your revision yet."
    ></i></h3>
<br>

<x-card>
    <h4>Research relevant papers</h4>
    <br>
    <p><i class="fa fa-user"></i> John Doe</p>
	<p><i class="fa fa-building"></i> Student</p>
    <p><i class="fa fa-envelope"></i> student@osu.cz</p>
    <p><i class="fa fa-gears"></i> Status</p>
    <p><i class="fa fa-file"></i> File</p>
    <br>
    </x-card>
	<br>

<h3 class="font-weight-bold">Submitted Tasks<i
    class="fa fa-info-circle ml-2 info-icon"
    data-toggle="popover"
    data-trigger="hover"
    data-placement="bottom"
    data-content="This section displays the task that have been submitted to you by the participant. Review the work and approve it or request modification."
    ></i></h3>
<br>

    <x-card>
        <h4>Research relevant papers</h4>
        <br>
        <p><i class="fa fa-user"></i> John Doe</p>
        <p><i class="fa fa-building"></i> Student</p>
        <p><i class="fa fa-envelope"></i> student@osu.cz</p>
        <p><i class="fa fa-gears"></i> Status</p>
        <p><i class="fa fa-file"></i> File</p>
        <br>
        <button class="btn btn-success mb-2">Approve</button><br>
        <button class="btn btn-secondary">Request Modification</button>

        </x-card>
        <br>

    <h3 class="font-weight-bold">Finished Tasks<i
        class="fa fa-info-circle ml-2 info-icon"
        data-toggle="popover"
        data-trigger="hover"
        data-placement="bottom"
        data-content="This section displays the finished tasks with the work you have approved."
        ></i></h3>
<br>

    <x-card>
        <h4>Research relevant papers</h4>
        <br>
        <p><i class="fa fa-user"></i> John Doe</p>
        <p><i class="fa fa-building"></i> Student</p>
        <p><i class="fa fa-envelope"></i> student@osu.cz</p>
        <p><i class="fa fa-gears"></i> Status</p>
        <p><i class="fa fa-file"></i> File</p>
        <br>
        </x-card>

</x-layout>
