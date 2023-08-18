<x-layout>
    <h1>Apply for the research work</h1><br>

        <h3 class="mb-3">{{$listing['title']}}</h3>
        <p>{{$listing['description']}} </p>
        <p class="mb-2"><i class="fa fa-user" data-toggle="tooltip" title="Author"></i> <strong>{{$listing['author']}}</strong></p>
        <p><i class="fa solid fa-building" data-toggle="tooltip" title="Department"></i> {{ $listing->department }}</p>


        <br>

        <h4 class="mb-3">Available tasks</h4>
            <p><i class="fa fa-flask"></i> Task 1</p>
            <p><i class="fa fa-flask"></i> Task 2</p>
            <p><i class="fa fa-flask"></i> Task 3</p>

        <br>
        <div class="form-group">
            <h4 class="mb-3">Message the Author</h4>
            <textarea class="form-control" id="message" name="message" rows="5"></textarea>
        </div>
        <button type="submit" class="btn btn-primary mb-2">Apply</button>
    <br>
    <a href="/" class="btn btn-secondary">Back</a>


</x-layout>
