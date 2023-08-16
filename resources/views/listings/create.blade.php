<x-layout>
    <h1>Create Research Offer</h1>
    <br>
    <form method="POST" action="/listings">
        @csrf
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" class="form-control" id="title" name="title" placeholder="Enter title" required value="{{old('title')}}" >
            @error('title')
            <p class="text-danger mt-1">{{$message}}</p>
            @enderror
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control" id="description" name="description" rows="5" placeholder="Enter description. Note that you can always edit the whole listing after creating it." >{{old('description')}}</textarea>
            @error('description')
            <p class="text-danger mt-1">{{$message}}</p>
            @enderror
        </div>
        <div class="form-group">
            <label for="department">Department</label>
            <select class="form-control" id="department" name="department">
                <option value="" selected disabled>Select a department</option>
                <option value="aro">Anaesthesiology, Resuscitation and Intensive Care Medicine</option>
                <option value="anatomy">Anatomy</option>
                <option value="biochemistry">Clinical Biochemistry</option>
                <option value="neurosciences">Clinical Neurosciences</option>
                <option value="craniofacial">Craniofacial Surgery</option>
                <option value="dentistry">Dentistry</option>
                <option value="epidemiology">Epidemiology and Public Health</option>
                <option value="forensic">Forensic Medicine</option>
                <option value="gynecology">Gynecology and Obstetrics</option>
                <option value="hematooncology">Hematooncology</option>
                <option value="histology">Histology and Embryology</option>
                <option value="imaging">Imaging Methods</option>
                <option value="internal">Internal Medicine</option>
                <option value="microbiology">Medical Microbiology</option>
                <option value="nursing">Nursing and Midwifery</option>
                <option value="oncology">Oncology</option>
                <option value="pediatrics">Pediatrics</option>
                <option value="rehabilitation">Rehabilitation and Sports Medicine</option>
                <option value="surgery">Surgical Studies</option>
                <option value="pharmacology">Pharmacology</option>
                <option value="emergency">Emergency Medicine</option>
                <option value="pathology">Molecular and Clinical Pathology and Medical Genetics</option>
                <option value="physiology">Physiology and Pathophysiology</option>
                <option value="hyperbaric">Hyperbaric Medicine</option>
            </select>
            @error('department')
            <p class="text-danger mt-1">{{$message}}</p>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary">Create Offer</button>
    </form>
</x-layout>
