<?php

namespace App\Livewire\Institutions;

use App\Models\InstitutionRequest;
use App\Models\Notification;
use App\Models\User;
use Livewire\Component;

class RegisterInstitution extends Component
{
    public string $name = '';
    public string $org_domain = '';      // e.g. example.edu (no @)
    public ?string $website_url = null;  // must include http/https (kept)
    public string $contact_email = '';
    public ?string $message = null;

    protected function rules(): array
    {
        return [
            'name'          => ['required','string','max:255'],
            'org_domain'    => ['required','string','max:120','regex:/^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,}$/i'],
            'website_url'   => ['nullable','url','max:255'], // keep http/https requirement
            'contact_email' => ['required','email','max:255'],
            'message'       => ['nullable','string','max:3000'],
        ];
    }

    public function submit(): void
    {
        $data = $this->validate();

        // Save request
        $req = InstitutionRequest::create($data + ['status' => 'pending']);

        // Notify admins via your Notification model
        $url = route('admin.institutions');
        foreach (User::where('is_admin', true)->pluck('id') as $aid) {
            Notification::deliver(
                (int)$aid,
                'New institution request',
                $req->name.' ('.$req->org_domain.') submitted a request.',
                $url,
                'institution.request'
            );
        }
        $this->dispatch('notificationsChanged');

        // Reset form
        $this->reset(['name','org_domain','website_url','contact_email','message']);

        // Reuse existing app.js toast mechanism
        // -> listener: applicationDomShouldReflect (state !== 'awaiting' => no DOM wait, just toast)
        $this->dispatch(
            'applicationDomShouldReflect',
            listingId: 0,
            state: 'done',
            flash: ['message' => 'Request submitted. We’ll get back to you soon.', 'type' => 'success']
        );
    }

    public function render()
    {
        return view('livewire.institutions.register-institution')
            ->layout('components.layout')
            ->title('Register institution');
    }
}
