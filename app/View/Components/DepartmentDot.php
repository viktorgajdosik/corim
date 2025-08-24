<?php

namespace App\View\Components;

use Illuminate\View\Component;

class DepartmentDot extends Component
{
    public string $department;
    public string $color;

    public function __construct(string $department)
    {
        $this->department = $department;
        $this->color = $this->resolveColor($department);
    }

    private function resolveColor(string $department): string
    {
        $departmentColors = [
            // Acute / critical
            'Anaesthesiology, Resuscitation and Intensive Care Medicine' => '#C62828', // critical red

            // Basic sciences
            'Anatomy'                        => '#D7CCC8', // bone/tissue ivory
            'Clinical Biochemistry'          => '#00897B', // teal chemistry / reagents
            'Histology and Embryology'       => '#6A1B9A', // microscope violets
            'Physiology and Pathophysiology' => '#455A64', // slate for systems/graphs

            // Neuro / imaging
            'Clinical Neurosciences'         => '#5C6BC0', // indigo neural
            'Imaging Methods'                => '#B0BEC5', // radiology blue–grey

            // Surgery & peri-op
            'Craniofacial Surgery'           => '#FF7043', // reconstructive orange
            'Surgical Studies'               => '#2E8B57', // surgical green

            // Frontline care
            'Emergency Medicine'             => '#FF6D00', // urgency amber
            'Internal Medicine'              => '#0D47A1', // trust navy
            'Nursing and Midwifery'          => '#81D4FA', // calm scrubs blue

            // Specialty clinics
            'Dermatovenerology'              => '#F4A261', // skin peach
            'Dentistry'                      => '#00BFA5', // clean mint
            'Gynecology and Obstetrics'      => '#D81B60', // maternal pink (deeper)
            'Pediatrics'                     => '#00BCD4', // playful cyan
            'Rehabilitation and Sports Medicine' => '#2E7D32', // recovery green

            // Lab & pathogens
            'Medical Microbiology'           => '#7CB342', // microbe green
            'Molecular and Clinical Pathology and Medical Genetics' => '#EC407A', // H&E pink (Pathology)

            // Oncology / blood / forensic
            'Oncology'                       => '#FBC02D', // gold ribbon family
            'Hematooncology'                 => '#8B0000', // deep blood red
            'Forensic Medicine'              => '#8E24AA', // forensic purple

            // Population & environment
            'Epidemiology and Public Health' => '#9E9D24', // olive-lime maps
            'Hyperbaric Medicine'            => '#004D40', // deep sea teal

            // Misc
            'Pharmacology'                   => '#8D6E63', // tablet brown
            'Student'                        => '#FF5BFA', // bright magenta
        ];

        return $departmentColors[$department] ?? '#999999'; // fallback neutral
    }

    public function render()
    {
        return view('components.department-dot');
    }
}
