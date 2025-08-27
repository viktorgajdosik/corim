<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Listing;
use App\Models\Notification;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OrgDemoSeeder extends Seeder
{
    public function run(): void
    {
        Model::unguard();

        // ---- Config: tweak volumes here ----
        $authorsPerOrg     = 5;
        $studentsPerOrg    = 12;
        $minListingsPerAU  = 2;
        $maxListingsPerAU  = 4;
        $minAppsPerListing = 2;
        $maxAppsPerListing = 8;
        $maxTasksPerAccept = 3;   // per accepted participant per listing
        $daysBack          = 420; // spread data across last ~14 months

        // Department list (match your app)
        $departments = [
            'Student',
            'Anaesthesiology, Resuscitation and Intensive Care Medicine',
            'Anatomy', 'Clinical Biochemistry', 'Clinical Neurosciences',
            'Craniofacial Surgery', 'Dentistry', 'Dermatovenerology',
            'Emergency Medicine', 'Epidemiology and Public Health', 'Forensic Medicine',
            'Gynecology and Obstetrics', 'Hematooncology', 'Histology and Embryology',
            'Hyperbaric Medicine', 'Imaging Methods', 'Internal Medicine',
            'Medical Microbiology', 'Molecular and Clinical Pathology and Medical Genetics',
            'Nursing and Midwifery', 'Oncology', 'Pediatrics', 'Pharmacology',
            'Physiology and Pathophysiology', 'Rehabilitation and Sports Medicine',
            'Surgical Studies',
        ];

        // Two orgs with their enforced email domains (your register flow enforces this)
        $orgs = [
            ['name' => 'Fakultní nemocnice Ostrava',               'domain' => 'fno.cz'],
            ['name' => 'Lékařská fakulta Ostravské univerzity',    'domain' => 'osu.cz'],
        ];

        // Keep references
        $authors   = collect();
        $students  = collect();
        $listings  = collect();
        $apps      = collect();
        $tasks     = collect();

        foreach ($orgs as $orgIndex => $org) {

            // ---- Authors (listing creators) ----
            for ($i = 1; $i <= $authorsPerOrg; $i++) {
                $dept = collect($departments)->reject(fn($d) => $d === 'Student')->random();
                $u = User::create([
                    'name'              => "Author {$i} ({$org['name']})",
                    'email'             => "author{$orgIndex}_{$i}@{$org['domain']}",
                    'department'        => $dept,
                    'organization'      => $org['name'],
                    'email_verified_at' => now(),
                    'password'          => bcrypt('password'),
                ]);
                $authors->push($u);
            }

            // ---- Students (participants) ----
            for ($i = 1; $i <= $studentsPerOrg; $i++) {
                $dept = collect($departments)->random(); // can be Student or a clinic
                $u = User::create([
                    'name'              => "Student {$i} ({$org['name']})",
                    'email'             => "student{$orgIndex}_{$i}@{$org['domain']}",
                    'department'        => $dept,
                    'organization'      => $org['name'],
                    'email_verified_at' => now(),
                    'password'          => bcrypt('password'),
                ]);
                $students->push($u);
            }
        }

        // Helper: random timestamp within last N days
        $randPast = fn(int $maxDays) =>
            Carbon::now()->subDays(rand(0, $maxDays))
                  ->subHours(rand(0, 23))
                  ->subMinutes(rand(0, 59));

        // ---- Listings per author ----
        foreach ($authors as $author) {
            $numListings = rand($minListingsPerAU, $maxListingsPerAU);

            for ($l = 1; $l <= $numListings; $l++) {
                $createdAt = $randPast($daysBack);
                $dept      = $author->department; // keeps dept coherent

                $listing = Listing::create([
                    'user_id'     => $author->id,
                    'title'       => "Research Topic {$l} — {$author->name}",
                    'description' => 'Seeded project about ' . Str::random(24) . ' with some lorem ipsum to fill the card. ',
                    'author'      => $author->name, // your blade expects $listing->author (string)
                    'department'  => $dept,
                    'created_at'  => $createdAt,
                    'updated_at'  => $createdAt,
                ]);

                $listings->push($listing);

                // ---- Applications to each listing (from all students overall) ----
                $numApps = rand($minAppsPerListing, $maxAppsPerListing);
                $someStudents = $students->shuffle()->take($numApps);

                foreach ($someStudents as $stu) {
                    $appCreated = $randPast(max(1, now()->diffInDays($createdAt)));
                    $accepted   = (int) (rand(0, 100) < 55); // ~55% accepted

                    $app = Application::create([
                        'listing_id' => $listing->id,
                        'user_id'    => $stu->id,
                        'message'    => 'I would like to participate. ' . Str::random(16),
                        'accepted'   => $accepted,
                        'created_at' => $appCreated,
                        'updated_at' => $appCreated,
                    ]);
                    $apps->push($app);

                    // Notification to author that student applied (seen: random)
                    Notification::create([
                        'user_id'    => $author->id,
                        'type'       => 'application.new',
                        'title'      => 'New application',
                        'body'       => "{$stu->name} applied to “{$listing->title}”.",
                        'url'        => route('listings.show-manage', $listing->id),
                        'created_at' => $appCreated,
                        'updated_at' => $appCreated,
                        'seen_at'    => rand(0,1) ? $appCreated->copy()->addHours(2) : null,
                    ]);

                    if ($accepted) {
                        // Notification to participant: accepted
                        Notification::create([
                            'user_id'    => $stu->id,
                            'type'       => 'application.accepted',
                            'title'      => 'You’ve been accepted',
                            'body'       => "Your application to “{$listing->title}” was accepted.",
                            'url'        => route('listings.show', $listing->id),
                            'created_at' => $appCreated->copy()->addHours(1),
                            'updated_at' => $appCreated->copy()->addHours(1),
                            'seen_at'    => rand(0,1) ? $appCreated->copy()->addHours(5) : null,
                        ]);

                        // ---- Tasks for accepted participant ----
                        $numTasks = rand(0, $maxTasksPerAccept);
                        for ($t = 1; $t <= $numTasks; $t++) {
                            $taskCreated = $randPast(max(1, now()->diffInDays($appCreated)));
                            $statuses = ['assigned','submitted','modification_requested','finished'];
                            $status   = $statuses[array_rand($statuses)];

                            $task = Task::create([
                                'author_id'        => $author->id,
                                'listing_id'       => $listing->id,
                                'name'             => "Task {$t} for {$stu->name}",
                                'description'      => 'Do work on ' . Str::random(16),
                                'deadline'         => rand(0,1) ? $taskCreated->copy()->addDays(rand(7, 60)) : null,
                                'assigned_user_id' => $stu->id,
                                'status'           => $status,
                                'created_at'       => $taskCreated,
                                'updated_at'       => $taskCreated,
                            ]);
                            $tasks->push($task);

                            // Notifications around tasks (assigned + maybe updated)
                            Notification::create([
                                'user_id'    => $stu->id,
                                'type'       => 'task.assigned',
                                'title'      => 'New task assigned',
                                'body'       => "“{$task->name}” in “{$listing->title}”.",
                                'url'        => route('listings.show', $listing->id),
                                'created_at' => $taskCreated,
                                'updated_at' => $taskCreated,
                                'seen_at'    => rand(0,1) ? $taskCreated->copy()->addHours(3) : null,
                            ]);

                            if (rand(0,1)) {
                                $updAt = $taskCreated->copy()->addDays(rand(1, 20));
                                Notification::create([
                                    'user_id'    => $stu->id,
                                    'type'       => 'task.updated',
                                    'title'      => 'Task updated',
                                    'body'       => "“{$task->name}” was updated.",
                                    'url'        => route('listings.show', $listing->id),
                                    'created_at' => $updAt,
                                    'updated_at' => $updAt,
                                    'seen_at'    => rand(0,1) ? $updAt->copy()->addHours(6) : null,
                                ]);
                            }

                            // Occasionally simulate student submission → notify author
                            if ($status === 'submitted' || (rand(0,100) < 20)) {
                                $subAt = $taskCreated->copy()->addDays(rand(1, 25));
                                Notification::create([
                                    'user_id'    => $author->id,
                                    'type'       => 'task.submitted',
                                    'title'      => 'Submission received',
                                    'body'       => "{$stu->name} submitted “{$task->name}”.",
                                    'url'        => route('listings.show-manage', $listing->id),
                                    'created_at' => $subAt,
                                    'updated_at' => $subAt,
                                    'seen_at'    => rand(0,1) ? $subAt->copy()->addHours(4) : null,
                                ]);
                            }
                        }
                    } else {
                        // Optional: notify deny to participant (some apps will be unaccepted)
                        if (rand(0,100) < 35) {
                            $denyAt = $appCreated->copy()->addDays(rand(1, 7));
                            Notification::create([
                                'user_id'    => $stu->id,
                                'type'       => 'application.denied',
                                'title'      => 'Application denied',
                                'body'       => "Your application to “{$listing->title}” was not accepted.",
                                'url'        => route('listings.show', $listing->id),
                                'created_at' => $denyAt,
                                'updated_at' => $denyAt,
                                'seen_at'    => rand(0,1) ? $denyAt->copy()->addHours(2) : null,
                            ]);
                        }
                    }
                }
            }
        }

        Model::reguard();

        $this->command?->info("Seeded: "
            . "{$authors->count()} authors, "
            . "{$students->count()} students, "
            . "{$listings->count()} listings, "
            . "{$apps->count()} applications, "
            . "{$tasks->count()} tasks, "
            . "notifications: " . Notification::count()
        );
    }
}

