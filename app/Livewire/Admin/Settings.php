<?php

namespace App\Livewire\Admin;

use App\Models\Setting;
use Livewire\Component;

class Settings extends Component
{
    public string $site_name = '';
    public string $support_email = '';
    public bool $registration_open = true;

    public bool $saved = false;

    public function mount(): void {
        $data = Setting::many([
            'site_name' => config('app.name'),
            'support_email' => config('mail.from.address'),
            'registration_open' => '1',
        ]);
        $this->site_name = (string)($data['site_name'] ?? '');
        $this->support_email = (string)($data['support_email'] ?? '');
        $this->registration_open = (bool)($data['registration_open'] ?? '1');
    }

    public function save(): void {
        $this->validate([
            'site_name' => 'required|string|max:120',
            'support_email' => 'required|email',
            'registration_open' => 'boolean',
        ]);
        Setting::set('site_name', $this->site_name);
        Setting::set('support_email', $this->support_email);
        Setting::set('registration_open', $this->registration_open ? '1' : '0');
        $this->saved = true;
        session()->flash('message','Settings saved.');
    }

    public function render() {
        return view('livewire.admin.settings')
            ->layout('layouts.admin', ['title' => 'Admin · Settings']);
    }
}
