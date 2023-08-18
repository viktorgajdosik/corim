<x-layout>

    <h2>Listing Management</h2>
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
    <br>

    <h2>Participants Applications</h2>
    <br>

    <x-card>
    <h4><strong> Jane Doe</strong></h4>
    <br>
	<p><i class="fa fa-building"></i> Student</p>
    <p><i class="fa fa-envelope"></i> student@osu.cz</p>
	<p><i class="fa fa-edit"></i> Message</p>
    <br>
    <div class="btn-group">
	<button class="btn btn-success"><i class="fa fa-check"></i> Accept</button>
	<button class="btn btn-danger ml-2"><i class="fa fa-times"></i> Deny</button>
    </div>
    </x-card>
	<br>

	<h2>Current Participants</h2>
    <br>
    <x-card>
        <h4><strong> Jane Doe</strong></h4>
        <br>
        <p><i class="fa fa-building"></i> Student</p>
        <p><i class="fa fa-envelope"></i> student@osu.cz</p>
        <br>
        <div class="btn-group">
        <button class="btn btn-danger"><i class="fa fa-trash"></i> Remove</button>
        </div>
        </x-card>
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
