<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = 'bla@fno.cz';

        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->command?->warn("AdminUserSeeder: user '{$email}' not found — no changes made.");
            return;
        }

        if (! $user->is_admin) {
            $user->is_admin = true;
            $user->save();
            $this->command?->info("AdminUserSeeder: '{$email}' is now an admin.");
        } else {
            $this->command?->info("AdminUserSeeder: '{$email}' is already an admin.");
        }
    }
}
