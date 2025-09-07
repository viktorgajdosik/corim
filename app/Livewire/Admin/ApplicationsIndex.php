<?php

namespace App\Livewire\Admin;

use App\Models\Application;
use Livewire\Component;
use Livewire\WithPagination;

class ApplicationsIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';
    public ?string $status = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => null],
    ];

    public function updatingSearch(){ $this->resetPage(); }
    public function updatedStatus(){ $this->resetPage(); }

    public function accept(int $id): void {
        $a = Application::findOrFail($id);
        $a->accepted = true;
        $a->save();
        session()->flash('message','Application accepted.');
    }

    public function deny(int $id): void {
        $a = Application::findOrFail($id);
        $a->accepted = false;
        $a->save();
        session()->flash('message','Application set to awaiting/denied.');
    }

    public function getRowsProperty() {
        $s = trim($this->search);

        return Application::query()
            ->with(['user:id,name','listing:id,title'])
            ->when($s !== '', function($q) use ($s){
                $q->where(function($w) use ($s){
                    $w->whereHas('user', fn($wu)=>$wu->where('name','like',"%{$s}%"))
                      ->orWhereHas('listing', fn($wl)=>$wl->where('title','like',"%{$s}%"));
                });
            })
            ->when($this->status === 'accepted', fn($q)=>$q->where('accepted',true))
            ->when($this->status === 'awaiting', fn($q)=>$q->where('accepted',false))
            ->orderBy('created_at','desc')
            ->paginate(15)
            ->withQueryString();
    }

    public function render() {
        return view('livewire.admin.applications-index', ['rows'=>$this->rows])
            ->layout('layouts.admin')->title('Admin · Applications');
    }
}
