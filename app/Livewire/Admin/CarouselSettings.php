<?php

namespace App\Livewire\Admin;

use App\Models\Setting;
use Livewire\Component;

class CarouselSettings extends Component
{
    /** @var array<int, array{title:string,subtitle?:string,cta_text?:string,cta_url?:string,enabled?:bool}> */
    public array $slides = [];

    public function mount(): void
    {
        $raw = Setting::get('home_carousel_slides');
        $arr = [];
        if (is_string($raw) && $raw !== '') {
            $arr = json_decode($raw, true) ?: [];
        }
        if (!is_array($arr)) $arr = [];

        // normalize + reindex
        $this->slides = array_values(array_map(function ($s) {
            return [
                'title'    => (string)($s['title'] ?? ''),
                'subtitle' => (string)($s['subtitle'] ?? ''),
                'cta_text' => (string)($s['cta_text'] ?? ''),
                'cta_url'  => (string)($s['cta_url'] ?? ''),
                'enabled'  => (bool)($s['enabled'] ?? true),
            ];
        }, $arr));
    }

    public function add(): void
    {
        $this->slides[] = [
            'title' => 'New slide',
            'subtitle' => '',
            'cta_text' => '',
            'cta_url' => '',
            'enabled' => true,
        ];
    }

    public function remove(int $i): void
    {
        if (!isset($this->slides[$i])) return;
        unset($this->slides[$i]);
        $this->slides = array_values($this->slides);
    }

    public function moveUp(int $i): void
    {
        if ($i <= 0 || !isset($this->slides[$i])) return;
        [$this->slides[$i-1], $this->slides[$i]] = [$this->slides[$i], $this->slides[$i-1]];
    }

    public function moveDown(int $i): void
    {
        if (!isset($this->slides[$i], $this->slides[$i+1])) return;
        [$this->slides[$i+1], $this->slides[$i]] = [$this->slides[$i], $this->slides[$i+1]];
    }

    public function save(): void
    {
        // basic validation
        foreach ($this->slides as $i => $s) {
            $this->slides[$i]['title'] = trim((string)($s['title'] ?? ''));
            $this->slides[$i]['subtitle'] = trim((string)($s['subtitle'] ?? ''));
            $this->slides[$i]['cta_text'] = trim((string)($s['cta_text'] ?? ''));
            $this->slides[$i]['cta_url']  = trim((string)($s['cta_url'] ?? ''));
            $this->slides[$i]['enabled']  = (bool)($s['enabled'] ?? true);
        }

        // reindex before save
        $clean = array_values($this->slides);

        Setting::set('home_carousel_slides', json_encode($clean, JSON_UNESCAPED_UNICODE));
        session()->flash('message', 'Carousel saved.');
        $this->dispatch('$refresh');
    }

    public function render()
    {
        return view('livewire.admin.carousel-settings')
            ->layout('layouts.admin', ['title' => 'Admin · Home carousel']);
    }
}
