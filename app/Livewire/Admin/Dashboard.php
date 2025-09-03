<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Listing;
use App\Models\Application;
use Livewire\Component;

class Dashboard extends Component
{
    public int $users = 0;
    public int $listings = 0;
    public int $listingsOpen = 0;
    public int $applications = 0;
    public int $applicationsAccepted = 0;

    public function mount(): void {
        $this->users = User::count();
        $this->listings = Listing::count();
        $this->listingsOpen = Listing::where('is_open', true)->count();
        $this->applications = Application::count();
        $this->applicationsAccepted = Application::where('accepted', true)->count();
    }

    public function render() {
        return view('livewire.admin.dashboard')
            ->layout('layouts.admin', ['title' => 'Admin · Dashboard']);
    }
}
