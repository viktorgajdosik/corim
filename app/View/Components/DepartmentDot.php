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
            'Anaesthesiology, Resuscitation and Intensive Care Medicine' => '#FF0000',
            'Anatomy' => '#0066FF',
            'Clinical Biochemistry' => '#00CC66',
            'Clinical Neurosciences' => '#00BFFF',
            'Craniofacial Surgery' => '#FFA500',
            'Dentistry' => '#8A2BE2',
            'Dermatovenerology' => '#800000',
            'Emergency Medicine' => '#FF3333',
            'Epidemiology and Public Health' => '#228B22',
            'Forensic Medicine' => '#6A5ACD',
            'Gynecology and Obstetrics' => '#FF1493',
            'Hematooncology' => '#B22222',
            'Histology and Embryology' => '#1E90FF',
            'Hyperbaric Medicine' => '#2F4F4F',
            'Imaging Methods' => '#A9A9A9',
            'Internal Medicine' => '#0000CD',
            'Medical Microbiology' => '#FFD700',
            'Molecular and Clinical Pathology and Medical Genetics' => '#32CD32',
            'Nursing and Midwifery' => '#9932CC',
            'Oncology' => '#DC143C',
            'Pediatrics' => '#00CED1',
            'Pharmacology' => '#A52A2A',
            'Physiology and Pathophysiology' => '#000000',
            'Rehabilitation and Sports Medicine' => '#00FF00',
            'Surgical Studies' => '#4169E1',
        ];

        return $departmentColors[$department] ?? '#999999';
    }

    public function render()
    {
        return view('components.department-dot');
    }
}
