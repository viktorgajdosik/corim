<x-layout>

    <h2>Apply for the research work</h2>
    <br>

    <x-card>
        <h4 class="mb-3"><strong>{{$listing['title']}}</strong></h4>
        <p>{{$listing['description']}} </p>
        <p class="mb-2"><i class="fa fa-user" data-toggle="tooltip" title="Author"></i> <strong>{{$listing['author']}}</strong></p>
        <p><i class="fa solid fa-building" data-toggle="tooltip" title="Department"></i> {{ $listing->department }}</p>
    </x-card>


 <!--   <x-card>
        <h4 class="mb-3"><strong>Available tasks</strong></h4>
            <p><i class="fa fa-flask"></i> Task 1</p>
            <p><i class="fa fa-flask"></i> Task 2</p>
            <p><i class="fa fa-flask"></i> Task 3</p>
    </x-card> -->

    <x-card>
        <div class="form-group">
            <h4 class="mb-3"><strong>Message the Author</strong></h4>
            <textarea class="form-control" id="message" name="message" rows="5"></textarea>
        </div>
        <button type="submit" class="btn btn-primary mb-2">Apply</button>
    </x-card>

    <a href="/" class="btn btn-secondary">Back</a>


</x-layout>
