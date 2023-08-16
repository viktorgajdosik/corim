<x-layout>
    <h1>Apply for the research work</h1><br>

        <h2>{{$listing['title']}}</h2><br>
        <p>{{$listing['description']}} </p>
        <p class="mb-2"><i class="fa fa-user" data-toggle="tooltip" title="Author"></i> {{$listing['author']}}</p>
        <p><i class="fa solid fa-building" data-toggle="tooltip" title="Department"></i> {{ $listing->department }}</p>


        <br>

        <h5>Available tasks</h5>
        <ul>
            <li>Task 1</li>
            <li>Task 2</li>
            <li>Task 3</li>
        </ul>
        <br>
        <div class="form-group">
            <h5 for="message">Message the Author</h5>
            <textarea class="form-control" id="message" name="message" rows="5"></textarea>
        </div>
        <button type="submit" class="btn btn-primary mb-2">Apply</button>
    <br>
    <a href="/" class="btn btn-secondary">Back</a>


</x-layout>
