<x-search></x-search>
<x-layout>
    <h3 class="font-weight-bold">Create Research Listing<i
        class="fa fa-info-circle ml-2 info-icon"
        data-toggle="popover"
        data-trigger="hover"
        data-placement="bottom"
        data-content="This section enables you to list you research and include all the necessary information for potential participants. Note that you can always edit the listing later after created."
        ></i></h3>
    <br>
    <x-card>
    <form method="POST" action="/listings">
        @csrf
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" class="form-control border-0 bg-light" id="title" name="title" placeholder="Enter title. Min. 10 characters." required value="{{old('title')}}" >
            @error('title')
            <p class="text-danger mt-1">{{$message}}</p>
            @enderror
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control border-0 bg-light" id="description" name="description" rows="5" placeholder="Enter description. Min. 50 characters." >{{old('description')}}</textarea>
            @error('description')
            <p class="text-danger mt-1">{{$message}}</p>
            @enderror
        </div>
        <div class="form-group">
            <label for="department">Department</label>
            <select class="form-control border-0 bg-light" id="department" name="department">
                <option value="" selected disabled>Select a department</option>
                <option value="Anaesthesiology, Resuscitation and Intensive Care Medicine">Anaesthesiology, Resuscitation and Intensive Care Medicine</option>
                <option value="Anatomy">Anatomy</option>
                <option value="Clinical Biochemistry">Clinical Biochemistry</option>
                <option value="Clinical Neurosciences">Clinical Neurosciences</option>
                <option value="Craniofacial Surgery">Craniofacial Surgery</option>
                <option value="Dentistry">Dentistry</option>
                <option value="Epidemiology and Public Health">Epidemiology and Public Health</option>
                <option value="Forensic Medicine">Forensic Medicine</option>
                <option value="Gynecology and Obstetrics">Gynecology and Obstetrics</option>
                <option value="Hematooncology">Hematooncology</option>
                <option value="Histology and Embryology">Histology and Embryology</option>
                <option value="Imaging Methods">Imaging Methods</option>
                <option value="Internal Medicine">Internal Medicine</option>
                <option value="Medical Microbiology">Medical Microbiology</option>
                <option value="Nursing and Midwifery">Nursing and Midwifery</option>
                <option value="Oncology">Oncology</option>
                <option value="Pediatrics">Pediatrics</option>
                <option value="Rehabilitation and Sports Medicine">Rehabilitation and Sports Medicine</option>
                <option value="Surgical Studies">Surgical Studies</option>
                <option value="Pharmacology">Pharmacology</option>
                <option value="Emergency Medicine">Emergency Medicine</option>
                <option value="Molecular and Clinical Pathology and Medical Genetics">Molecular and Clinical Pathology and Medical Genetics</option>
                <option value="Physiology and Pathophysiology">Physiology and Pathophysiology</option>
                <option value="Hyperbaric Medicine">Hyperbaric Medicine</option>
            </select>
            @error('department')
            <p class="text-danger mt-1">{{$message}}</p>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary" id="createOfferButton" disabled>Create Offer</button>
    </form>
    </x-card>
</x-layout>
