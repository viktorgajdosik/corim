<x-layout>

    <h2>Manage Listing</h2>
    <br>
		<h5>Listing Details</h5>
    <br>
    <table class="table">
    <tbody>
		<tr>
		<td><strong>{{$listing['title']}}</strong></td>
		</tr>
		<tr>
			<td>{{$listing['description']}}</td>
		</tr>
        <tr>
			<td>{{$listing['department']}}</td>
		</tr>
		<tr>
            <td class="btn-group">
                <a href="/listings/{{$listing->id}}/edit" class="btn btn-primary"><i class="fa fa-pencil"></i> Edit</a>
                <form method="POST" action="/listings/{{$listing->id}}">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger ml-2" onclick="return confirm('Are you sure you want to delete this offer?')"><i class="fa fa-trash"></i> Delete</button>
                </form>
            </td>
        </tr>
		</tbody>
    </table>

    <br>
 <!--<h2>Manage Participants</h2>

		<br>
		<h5>Participant Applications</h5>
    <br>

	<table class="table">
	<tbody>
		<tr>
		<td><strong>Jane Doe</strong></td>
		</tr>
		<tr>
			<td>Student</td>
		</tr>
		<tr>
			<td>Message</td>
		</tr>
		<tr>
		<td>
						<a href="" class="btn btn-success">Accept</a>
						<a href="" class="btn btn-danger">Deny</a>
					</td>
		</tr>
		</tbody>
	</table>

		<br>
		<h5>Current Participants</h5>
    <br>
		<table class="table">
	<tbody>
		<tr>
		<td><strong>Jane Doe</strong></td>
		</tr>
		<tr>
			<td>Student
            </td>
		</tr>
		<tr>
		<td>
						<a href="" class="btn btn-danger">Remove</a>
					</td>
		</tr>
		</tbody>
	</table>

		<br>
        <h2>Manage Tasks</h2>
        <br>
		<h5>Create Task</h5>
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
    <div class="form-check">
      <input class="form-check-input" type="checkbox" value="bob" id="bob">
      <label class="form-check-label" for="bob">
        Bob Smith
      </label>
    </div>
    <div class="form-check">
      <input class="form-check-input" type="checkbox" value="susan" id="susan">
      <label class="form-check-label" for="susan">
        Susan Johnson
      </label>
    </div>
  </div>

	<button type="submit" class="btn btn-primary">Create Task</button>
</form>
<br><br>
<h5>Assigned Tasks</h5>
<br>
<table class="table">
	<tbody>
		<tr>
		<td><strong>Research Paper</strong></td>
		</tr>
		<tr>
		<td>John Doe</td>
		</tr>
		<tr>
			<td>Status</td>
		</tr>
		<tr>
			<td>File / Link</td>
		</tr>
		<tr>
			<td><a href="" class="btn btn-secondary">Show and edit</a></td>
		</tr>
	</tbody>
	</table>
	<table class="table">
	<tbody>
	<tr>
		<td><strong>Research Paper</strong></td>
		</tr>
		<tr>
		<td>John Doe</td>
		</tr>
		<tr>
			<td>Status</td>
		</tr>
		<tr>
			<td>File / Link</td>
		</tr>
		<tr>
		<td>
				<a href="" class="btn btn-success">Approve</a>
				<a href="" class="btn btn-primary">Request modification</a>
				<a href="" class="btn btn-secondary">Show and edit</a>
			</td>
		</tr>
		</tbody>
	</table>
		<br>
-->
</x-layout>
