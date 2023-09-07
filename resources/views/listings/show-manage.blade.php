<x-layout>

    <h2>Listing Management<i
        class="fa fa-info-circle ml-2 info-icon"
        data-toggle="popover"
        data-trigger="hover"
        data-placement="right"
        data-content="This section displays your research listing and enables you to edit or delete it."
        ></i></h2>
    <br>

    <x-card>
		<h4><strong>{{$listing['title']}}</strong></h4>
        <br>
	    <p>{{$listing['description']}}</p>
        <p><i class="fa solid fa-building" data-toggle="tooltip" title="Department"></i> {{$listing['department']}}</p>
        <br>
        <div class="btn-group">
            <a href="/listings/{{$listing->id}}/edit" class="btn btn-primary"><i class="fa fa-pencil"></i> Edit</a>
            <form method="POST" action="/listings/{{$listing->id}}">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger ml-2" onclick="return confirm('Are you sure you want to delete this offer?')"><i class="fa fa-trash"></i> Delete</button>
            </form>
        </div>
    </x-card>
    <br><br>

    <h2>Applications<i
        class="fa fa-info-circle ml-2 info-icon"
        data-toggle="popover"
        data-trigger="hover"
        data-placement="bottom"
        data-content="This section displays applications from users who would like to participate in this research work. You can accept or deny them."
        ></i></h2>
    <br>
   <!-- Loop through applications -->


    @forelse ($listing->applications->where('accepted', false) as $application)
    <x-card>
        <h4><strong>{{ $application->user->name }}</strong></h4>
        <br>
        <p><i class="fa fa-building"></i> {{ $application->user->department }}</p>
        <p><i class="fa fa-envelope"></i> {{ $application->user->email }}</p>
        <p><i class="fa fa-edit"></i> {{ $application->message }}</p>
        <br>
        <div class="btn-group">
            <form method="POST" action="{{ route('listings.accept', ['application' => $application->id]) }}">
                @csrf
                <button class="btn btn-success" onclick="return confirm('Accept this application?')">
                    <i class="fa fa-check"></i> Accept
                </button>
            </form>
            <form method="POST" action="{{ route('listings.deny', ['application' => $application->id]) }}">
                @csrf
                <button class="btn btn-danger ml-2" onclick="return confirm('Deny this application?')">
                    <i class="fa fa-times"></i> Deny
                </button>
            </form>
        </div>
    </x-card>
    @empty
        <p>Currently no applications.</p>
    @endforelse


	<br><br>

	<h2>Current Participants<i
        class="fa fa-info-circle ml-2 info-icon"
        data-toggle="popover"
        data-trigger="hover"
        data-placement="bottom"
        data-content="This section displays users with accepted application. You can remove users from this research work here."
        ></i></h2>
    <br>

@if ($listing->applications->where('accepted', true)->isEmpty())
    <p>Currently no participants.</p>
@else
    @foreach ($listing->applications->where('accepted', true) as $application)
        <x-card>
            <h4><strong>{{ $application->user->name }}</strong></h4>
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

       <!-- <h2>Tasks Management</h2>
        <br>
    <x-card>
		<h4><strong>Create Task</strong></h4>
        <br>
        <form>
	    <div class="form-group">
		<label for="task-name">Task Name</label>
		<input type="text" class="form-control" id="task-name">
	    </div>
	    <div class="form-group">
		<label for="task-details">Task Details</label>
		<textarea class="form-control" id="task-details" rows="5"></textarea>
	    </div>
	    <div class="form-group upload">
        <label for="task-file">
        <i class="fa fa-file"></i> Upload File
        </label>
        <input type="file" class="form-control-file" id="task-file" name="task-file">
        </div>
	    <div class="form-group">
        <label for="students">Select Students</label>
        <div class="form-check">
        <input class="form-check-input" type="checkbox" value="john" id="john">
        <label class="form-check-label" for="john">
        John Doe
        </label>
        </div>
        <div class="form-check">
        <input class="form-check-input" type="checkbox" value="jane" id="jane">
        <label class="form-check-label" for="jane">
        Jane Doe
        </label>
        </div>
        </div>
        <div class="btn-group">
            <button type="submit" class="btn btn-primary"> Create Task</button>
            </div>
        </form>
    </x-card>
    <br>


<h2>Assigned Tasks</h2>
<br>

<x-card>
    <h4><strong> Research relevant papers</strong></h4>
    <br>
    <p><i class="fa fa-user"></i> John Doe</p>
	<p><i class="fa fa-building"></i> Student</p>
    <p><i class="fa fa-envelope"></i> student@osu.cz</p>
    <p><i class="fa fa-gears"></i> Status</p>
    <p><i class="fa fa-file"></i> File</p>
    <br>
    <div class="btn-group">
	<button class="btn btn-secondary">Show / Edit</button>
    </div>
    </x-card>
	<br>

    <x-card>
        <h4><strong> Research relevant papers</strong></h4>
        <br>
        <p><i class="fa fa-user"></i> John Doe</p>
        <p><i class="fa fa-building"></i> Student</p>
        <p><i class="fa fa-envelope"></i> student@osu.cz</p>
        <p><i class="fa fa-gears"></i> Status</p>
        <p><i class="fa fa-file"></i> File</p>
        <br>
        <div class="btn-group">
        <button class="btn btn-success">Approve</button>
        <button class="btn btn-secondary ml-2">Request Modification</button>
        </div>
        </x-card>
        <br> -->

</x-layout>
