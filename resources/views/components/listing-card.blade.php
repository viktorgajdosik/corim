@php
    $isAuthor = auth()->check() && auth()->id() === $listing->user_id;

    // Color map for departments
    $departmentColors = [
    'Anaesthesiology, Resuscitation and Intensive Care Medicine' => 'background-color: #FF0000;', // Red
    'Anatomy' => 'background-color: #0066FF;',                  // Blue
    'Clinical Biochemistry' => 'background-color: #00CC66;',     // Green
    'Clinical Neurosciences' => 'background-color: #00BFFF;',    // Deep Sky Blue
    'Craniofacial Surgery' => 'background-color: #FFA500;',      // Orange
    'Dentistry' => 'background-color: #8A2BE2;',                 // Blue Violet
    'Dermatovenerology' => 'background-color: #800000;',         // Maroon
    'Emergency Medicine' => 'background-color: #FF3333;',        // Bright Red
    'Epidemiology and Public Health' => 'background-color: #228B22;', // Forest Green
    'Forensic Medicine' => 'background-color: #6A5ACD;',         // Slate Blue
    'Gynecology and Obstetrics' => 'background-color: #FF1493;', // Deep Pink
    'Hematooncology' => 'background-color: #B22222;',            // Firebrick
    'Histology and Embryology' => 'background-color: #1E90FF;',  // Dodger Blue
    'Hyperbaric Medicine' => 'background-color: #2F4F4F;',       // Dark Slate Gray
    'Imaging Methods' => 'background-color: #A9A9A9;',           // Dark Gray
    'Internal Medicine' => 'background-color: #0000CD;',         // Medium Blue
    'Medical Microbiology' => 'background-color: #FFD700;',      // Gold
    'Molecular and Clinical Pathology and Medical Genetics' => 'background-color: #32CD32;', // Lime Green
    'Nursing and Midwifery' => 'background-color: #9932CC;',     // Dark Orchid
    'Oncology' => 'background-color: #DC143C;',                  // Crimson
    'Pediatrics' => 'background-color: #00CED1;',                // Dark Turquoise
    'Pharmacology' => 'background-color: #A52A2A;',              // Brown
    'Physiology and Pathophysiology' => 'background-color: #000000;', // Black
    'Rehabilitation and Sports Medicine' => 'background-color: #00FF00;', // Lime
    'Surgical Studies' => 'background-color: #4169E1;',          // Royal Blue
];


$departmentDotStyle = $departmentColors[$listing->department] ?? 'background-color: #999;';

@endphp

<a href="{{ $isAuthor ? route('listings.show-manage', $listing) : route('listings.show', $listing) }}"
   class="text-decoration-none text-white">
    <div class="listing-card mb-3 p-3 position-relative">

<!-- Title and Date -->
<div class="d-flex justify-content-between align-items-start mb-2">
    <p class="fw-bolder listing-title fs-6 mb-0 text-truncate"
       style="max-width: 75%; word-break: break-word;">
        {{ $listing->title }}
    </p>
    <small class="date text-nowrap ms-2">
        {{ $listing->created_at->format('d/m/Y') }}
    </small>
</div>

       <!-- Author + Department -->
       <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center gap-2 gap-sm-3">
    <small class="text-white">
        <i class="fa fa-user me-1"></i> {{ $listing->author }}
    </small>

    <div class="d-flex align-items-center">
        <span class="me-2 rounded-circle" style="width: 10px; height: 10px; display: inline-block; {{ $departmentDotStyle }}"></span>
        <small class="text-white">{{ $listing->department }}</small>
    </div>
</div>

        <!-- Description -->
        <small><p class="description mt-2 mb-1 text-justify">
            {{ Str::limit($listing->description, 230) }}
        </p></small>
    </div>
</a>


