<x-layout>
<div class="row">
    <div class="col-md-6">
        <h2>Personal Information</h2>
        <br>
        <p><strong>Name:</strong> {{ $user->name }} </p>
        <p><strong>Email:</strong>  {{ $user->email }} </p>
        <p><strong>Department:</strong> {{ $user->department }}  </p>
        <p><strong>Research points:</strong> </p>
         <a href="/users/edit-profile"><i class="fa fa-edit"></i> Edit profile information</a>
        <br><br><br>
        <a href="">Download My Information</a>
        <br>
        <form method="POST" action="/users/profile">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-link text-danger delete-profile-button p-0" onclick="return confirm('Are you sure you want to delete your profile?')">Delete Profile</button>
        </form>
    </form>
    </div>
    <div class="col-md-6">
        <h2>Research Work Participation</h2>
    <br>

     <p>You have not participated in other author's research work yet.</p>

    </div>
</div>
</x-layout>
