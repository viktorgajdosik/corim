<?php

namespace App\Livewire\Admin;

use App\Models\Listing;
use Livewire\Component;
use Livewire\WithPagination;

class ListingsIndex extends Component
{
    use WithPagination;

    public string $search = '';
    /** @var null|bool|string */
    public $isOpen = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'isOpen' => ['except' => null],
    ];

    public function updatingSearch(){ $this->resetPage(); }
    public function updatedIsOpen(){ $this->resetPage(); }

    private function boolOrNull($v): ?bool
    {
        if ($v === true || $v === 1 || $v === '1') return true;
        if ($v === false || $v === 0 || $v === '0') return false;
        return null;
    }

    public function toggleOpen(int $id): void {
        $l = Listing::findOrFail($id);
        $l->is_open = ! $l->is_open;
        $l->save();
        session()->flash('message','Listing state updated.');
    }

    public function deleteListing(int $id): void {
        Listing::findOrFail($id)->delete();
        session()->flash('message','Listing deleted.');
        $this->resetPage();
    }

    public function getRowsProperty() {
        $flag = $this->boolOrNull($this->isOpen);
        $s = trim($this->search);

        return Listing::query()
            ->when($s !== '', fn($q) =>
                $q->where(function($w) use ($s){
                    $w->where('title','like',"%{$s}%")
                      ->orWhere('author','like',"%{$s}%");
                })
            )
            ->when(!is_null($flag), fn($q)=>$q->where('is_open',$flag))
            ->orderBy('created_at','desc')
            ->paginate(15)
            ->withQueryString();
    }

    public function render() {
        return view('livewire.admin.listings-index', ['rows'=>$this->rows])
            ->layout('layouts.admin')->title('Admin · Listings');
    }
}
