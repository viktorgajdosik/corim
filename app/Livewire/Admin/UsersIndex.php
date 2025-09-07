<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Listing;
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

    public function toggleAdmin(int $id): void
    {
        if ($id === auth()->id()) return;
        $u = User::findOrFail($id);
        $u->is_admin = ! $u->is_admin;
        $u->save();

        // Notify user about role change
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

    /** ===== Emails ===== */

    /** Send a password reset link via default broker. */
    public function sendResetLink(int $id): void
    {
        $user = User::findOrFail($id);

        try {
            $status = Password::broker()->sendResetLink(['email' => $user->email]);

            if ($status === Password::RESET_LINK_SENT) {
                session()->flash('message', "Password reset email sent to {$user->email}.");
            } else {
                session()->flash('message', __($status));
            }
        } catch (\Throwable $e) {
            session()->flash('message', 'Could not send reset link. Check password reset table & mail config.');
        }
    }

    /** Resend email verification if not verified. */
    public function resendVerification(int $id): void
    {
        $user = User::findOrFail($id);

        if ($user->email_verified_at) {
            session()->flash('message', 'User is already verified.');
            return;
        }

        try {
            $user->sendEmailVerificationNotification();
            session()->flash('message', "Verification email resent to {$user->email}.");
        } catch (\Throwable $e) {
            session()->flash('message', 'Could not resend verification email. Check mail config.');
        }
    }

    public function render()
    {
        return view('livewire.admin.users-index', ['rows' => $this->rows])
            ->layout('layouts.admin')->title('Admin · Users');
    }
}
