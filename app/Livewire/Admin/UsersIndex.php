<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Listing;
use App\Models\Application;
use App\Models\Task;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;
use App\Notifications\AccountStatusChanged;

class UsersIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';
    /** @var null|bool|string  '1'|'0'|true|false|null|'' */
    public $adminOnly = null;

    /** Ban reason bound per user (id => reason text) */
    public array $banReason = [];

    /** ===== Right offcanvas (all-at-once details) ===== */
    public ?int $showUserId = null;
    public array $userDetails = [
        'user'         => null,
        'listings'     => [],
        'applications' => [],
        'tasks'        => [],
    ];

    protected $queryString = [
        'search'    => ['except' => ''],
        'adminOnly' => ['except' => null],
    ];

    public function updatingSearch()   { $this->resetPage(); }
    public function updatedAdminOnly() { $this->resetPage(); }

    private function boolOrNull($v): ?bool
    {
        if ($v === true || $v === 1 || $v === '1') return true;
        if ($v === false || $v === 0 || $v === '0') return false;
        return null;
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

    /* ========== Admin / moderation ========== */

    public function toggleAdmin(int $id): void
    {
        if ($id === auth()->id()) return;
        $u = User::findOrFail($id);
        $u->is_admin = ! $u->is_admin;
        $u->save();

        // Email the user about role change (unchanged behaviour)
        $u->notify(new AccountStatusChanged(
            event: $u->is_admin ? 'granted_admin' : 'revoked_admin',
            changedBy: auth()->user()->name ?? 'Admin'
        ));

        session()->flash('message','Role updated.');
    }

    public function deleteUser(int $id): void
    {
        if ($id === auth()->id()) return;
        User::findOrFail($id)->delete();
        session()->flash('message','User deleted.');
        $this->resetPage();
    }

    /** ===== Moderation actions ===== */

    public function banUser(int $id): void
    {
        if ($id === auth()->id()) return;
        $u = User::findOrFail($id);
        if ($u->banned_at) return;

        $reason = trim($this->banReason[$id] ?? '');
        $u->banned_at = now();
        $u->ban_reason = $reason !== '' ? $reason : null;
        $u->save();

        // Freeze their open listings
        Listing::where('user_id', $u->id)->where('is_open', true)->update(['is_open' => false]);

        // Email the user (includes reason + who changed it)
        $u->notify(new AccountStatusChanged(
            event: 'banned',
            changedBy: auth()->user()->name ?? 'Admin',
            reason: $u->ban_reason
        ));

        $this->revokeSessions($id, silent: true);
        unset($this->banReason[$id]);
        session()->flash('message', 'User banned, reason saved, open listings closed, and sessions revoked.');
    }

    public function unbanUser(int $id): void
    {
        $u = User::findOrFail($id);
        $u->banned_at = null;
        $u->ban_reason = null;
        $u->save();

        $u->notify(new AccountStatusChanged(
            event: 'unbanned',
            changedBy: auth()->user()->name ?? 'Admin'
        ));

        session()->flash('message', 'User unbanned.');
    }

    public function deactivateUser(int $id): void
    {
        if ($id === auth()->id()) return;
        $u = User::findOrFail($id);
        if ($u->deactivated_at) return;

        $u->deactivated_at = now();
        $u->save();

        // Freeze their open listings
        Listing::where('user_id', $u->id)->where('is_open', true)->update(['is_open' => false]);

        $u->notify(new AccountStatusChanged(
            event: 'deactivated',
            changedBy: auth()->user()->name ?? 'Admin'
        ));

        $this->revokeSessions($id, silent: true);
        session()->flash('message', 'User deactivated, open listings closed, and sessions revoked.');
    }

    public function activateUser(int $id): void
    {
        $u = User::findOrFail($id);
        $u->deactivated_at = null;
        $u->save();

        $u->notify(new AccountStatusChanged(
            event: 'activated',
            changedBy: auth()->user()->name ?? 'Admin'
        ));

        session()->flash('message', 'User reactivated.');
    }

    public function revokeSessions(int $id, bool $silent = false): void
    {
        try {
            if (config('session.driver') === 'database') {
                DB::table('sessions')->where('user_id', $id)->delete();
            }
        } catch (\Throwable $e) {
            // ignore if sessions table missing
        }

        User::whereKey($id)->update(['remember_token' => Str::random(60)]);
        if (!$silent) session()->flash('message', 'All sessions revoked for that user.');
    }

    /** ===== Offcanvas: build all details at once (no loading) ===== */
    public function showUser(int $id): void
    {
        $u = User::findOrFail($id);

        $listings = Listing::where('user_id', $id)
            ->latest()->limit(20)
            ->get(['id','title','is_open','created_at'])
            ->map(fn($l)=>[
                'id'        => $l->id,
                'title'     => $l->title,
                'is_open'   => $l->is_open,
                'created_at'=> $l->created_at?->format('Y-m-d'),
            ])->toArray();

        $applications = Application::where('user_id',$id)
            ->with('listing:id,title')
            ->latest()->limit(20)
            ->get()
            ->map(fn($a)=>[
                'id'            => $a->id,
                'listing_id'    => $a->listing_id,
                'listing_title' => $a->listing?->title ?? '—',
                'accepted'      => $a->accepted,
                'created_at'    => $a->created_at?->format('Y-m-d'),
            ])->toArray();

        $tasks = Task::where('assigned_user_id',$id)
            ->with('listing:id,title')
            ->latest()->limit(20)
            ->get(['id','name','status','listing_id','created_at'])
            ->map(fn($t)=>[
                'id'            => $t->id,
                'name'          => $t->name,
                'status'        => $t->status,
                'listing_id'    => $t->listing_id,
                'listing_title' => $t->listing?->title ?? '—',
                'created_at'    => $t->created_at?->format('Y-m-d'),
            ])->toArray();

        $this->userDetails = [
            'user' => [
                'id'             => $u->id,
                'name'           => $u->name,
                'email'          => $u->email,
                'organization'   => $u->organization,
                'department'     => $u->department,
                'created_at'     => $u->created_at?->format('Y-m-d'),
                'banned_at'      => $u->banned_at?->format('Y-m-d H:i'),
                'deactivated_at' => $u->deactivated_at?->format('Y-m-d H:i'),
            ],
            'listings'     => $listings,
            'applications' => $applications,
            'tasks'        => $tasks,
        ];

        $this->showUserId = $id;
        $this->dispatch('show-user-canvas'); // JS opens the Bootstrap offcanvas
    }

    public function render()
    {
        return view('livewire.admin.users-index', ['rows' => $this->rows])
            ->layout('layouts.admin')->title('Admin · Users');
    }
}
