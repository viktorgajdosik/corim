<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class UsersIndex extends Component
{
    use WithPagination;

    public string $search = '';
    /** @var null|bool|string  '1'|'0'|true|false|null|'' */
    public $adminOnly = null;

    protected $queryString = [
        'search'    => ['except' => ''],
        'adminOnly' => ['except' => null], // Livewire will omit when null
    ];

    // Livewire v3 uses Tailwind by default; keep it if you haven't published Bootstrap views.
    // protected $paginationTheme = 'bootstrap';

    public function updatingSearch()     { $this->resetPage(); }
    public function updatedAdminOnly()   { $this->resetPage(); }

    private function boolOrNull($v): ?bool
    {
        if ($v === true || $v === 1 || $v === '1') return true;
        if ($v === false || $v === 0 || $v === '0') return false;
        return null; // includes '', null
    }

    public function getRowsProperty()
    {
        $flag = $this->boolOrNull($this->adminOnly);
        $s = trim($this->search);

        return User::query()
            ->when($s !== '', fn($q) =>
                $q->where(function($w) use ($s) {
                    $w->where('name','like',"%{$s}%")
                      ->orWhere('email','like',"%{$s}%");
                })
            )
            ->when(!is_null($flag), fn($q) => $q->where('is_admin', $flag))
            ->orderBy('created_at','desc')
            ->paginate(15)
            ->withQueryString();
    }

    public function toggleAdmin(int $id): void
    {
        if ($id === auth()->id()) return;
        $u = User::findOrFail($id);
        $u->is_admin = ! $u->is_admin;
        $u->save();
        session()->flash('message','Role updated.');
    }

    public function deleteUser(int $id): void
    {
        if ($id === auth()->id()) return;
        User::findOrFail($id)->delete();
        session()->flash('message','User deleted.');
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.admin.users-index', ['rows' => $this->rows])
            ->layout('layouts.admin')->title('Admin · Users');
    }
}
