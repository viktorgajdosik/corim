<?php

namespace App\Livewire\Admin;

use App\Models\Institution;
use App\Models\InstitutionRequest;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Notifications\InstitutionRequestDecided;
use Illuminate\Support\Facades\Notification as MailerNotification; // <-- Facade alias to avoid model name clash

class InstitutionsIndex extends Component
{
    use WithPagination;

    public string $search = '';

    protected $queryString = ['search' => ['except' => '']];

    public function updatingSearch(){ $this->resetPage(); }

    public function approve(int $requestId): void
    {
        $req = InstitutionRequest::findOrFail($requestId);
        if ($req->status !== 'pending') return;

        // Create institution
        Institution::firstOrCreate(
            ['name' => $req->name],
            [
                'domain'        => $req->org_domain,
                'website_url'   => $req->website_url,
                'contact_email' => $req->contact_email,
            ]
        );

        $req->update([
            'status'     => 'approved',
            'decided_at' => now(),
            'decided_by' => Auth::id(),
        ]);

        // Email the requester
        $decider = auth()->user()?->name ?? 'Admin';
        MailerNotification::route('mail', $req->contact_email)
            ->notify(new InstitutionRequestDecided(
                decision: 'approved',
                name: $req->name,
                domain: $req->org_domain,
                website: $req->website_url,
                decidedBy: $decider
            ));

        session()->flash('message', 'Institution approved and added to registration form.');
    }

    public function decline(int $requestId): void
    {
        $req = InstitutionRequest::findOrFail($requestId);
        if ($req->status !== 'pending') return;

        $req->update([
            'status'     => 'declined',
            'decided_at' => now(),
            'decided_by' => Auth::id(),
        ]);

        // Email the requester
        $decider = auth()->user()?->name ?? 'Admin';
        MailerNotification::route('mail', $req->contact_email)
            ->notify(new InstitutionRequestDecided(
                decision: 'declined',
                name: $req->name,
                domain: $req->org_domain,
                website: $req->website_url,
                decidedBy: $decider
            ));

        session()->flash('message', 'Institution request declined.');
    }

    public function removeInstitution(int $institutionId): void
    {
        Institution::findOrFail($institutionId)->delete();
        session()->flash('message', 'Institution removed.');
    }

    public function getPendingProperty()
    {
        return InstitutionRequest::query()
            ->when($this->search !== '', fn($q) =>
                $q->where(function($w){
                    $s = $this->search;
                    $w->where('name','like',"%{$s}%")
                      ->orWhere('org_domain','like',"%{$s}%")
                      ->orWhere('contact_email','like',"%{$s}%");
                })
            )
            ->where('status','pending')
            ->latest()
            ->paginate(10);
    }

    public function getInstitutionsProperty()
    {
        return Institution::query()
            ->when($this->search !== '', fn($q) =>
                $q->where(function($w){
                    $s = $this->search;
                    $w->where('name','like',"%{$s}%")
                      ->orWhere('domain','like',"%{$s}%");
                })
            )
            ->orderBy('name')
            ->paginate(10);
    }

    public function render()
    {
        return view('livewire.admin.institutions-index', [
            'pending'      => $this->pending,
            'institutions' => $this->institutions,
        ])->layout('layouts.admin', ['title' => 'Admin · Institutions']);
    }
}
