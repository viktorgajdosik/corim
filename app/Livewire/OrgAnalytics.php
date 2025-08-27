<?php

namespace App\Livewire;

use App\Models\Application;
use App\Models\Listing;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Livewire\Component;

class OrgAnalytics extends Component
{
    public User $user;

    /**
     * Time window: '6m' | '1y' | '5y' | 'all'
     * Default 6 months (includes current month).
     */
    public string $window = '6m';

    public function mount(User $user): void
    {
        $this->user = $user;
    }

    /** Department color mapping */
    protected function deptColor(string $dept): string
    {
        static $map = [
            'Anaesthesiology, Resuscitation and Intensive Care Medicine' => '#C62828',
            'Anatomy'                        => '#D7CCC8',
            'Clinical Biochemistry'          => '#00897B',
            'Histology and Embryology'       => '#6A1B9A',
            'Physiology and Pathophysiology' => '#455A64',
            'Clinical Neurosciences'         => '#5C6BC0',
            'Imaging Methods'                => '#B0BEC5',
            'Craniofacial Surgery'           => '#FF7043',
            'Surgical Studies'               => '#2E8B57',
            'Emergency Medicine'             => '#FF6D00',
            'Internal Medicine'              => '#0D47A1',
            'Nursing and Midwifery'          => '#81D4FA',
            'Dermatovenerology'              => '#F4A261',
            'Dentistry'                      => '#00BFA5',
            'Gynecology and Obstetrics'      => '#D81B60',
            'Pediatrics'                     => '#00BCD4',
            'Rehabilitation and Sports Medicine' => '#2E7D32',
            'Medical Microbiology'           => '#7CB342',
            'Molecular and Clinical Pathology and Medical Genetics' => '#EC407A',
            'Oncology'                       => '#FBC02D',
            'Hematooncology'                 => '#8B0000',
            'Forensic Medicine'              => '#8E24AA',
            'Epidemiology and Public Health' => '#9E9D24',
            'Hyperbaric Medicine'            => '#004D40',
            'Pharmacology'                   => '#8D6E63',
            'Student'                        => '#FF5BFA',
        ];

        return $map[$dept] ?? '#999999';
    }

    /** Oldest month present in org (listings/tasks/accepted applications/users) */
    protected function earliestMonth(string $org): Carbon
    {
        $lm = Listing::query()
            ->join('users', 'users.id', '=', 'listings.user_id')
            ->where('users.organization', $org)
            ->min('listings.created_at');

        $tm = Task::query()
            ->join('listings', 'listings.id', '=', 'tasks.listing_id')
            ->join('users as authors', 'authors.id', '=', 'listings.user_id')
            ->where('authors.organization', $org)
            ->min('tasks.created_at');

        $am = Application::query()
            ->join('listings', 'listings.id', '=', 'applications.listing_id')
            ->join('users as authors', 'authors.id', '=', 'listings.user_id')
            ->where('authors.organization', $org)
            ->where('applications.accepted', 1)
            ->min('applications.created_at');

        $um = User::query()
            ->where('organization', $org)
            ->min('created_at');

        $candidates = array_filter([$lm, $tm, $am, $um]);
        if (empty($candidates)) {
            return now()->startOfMonth();
        }
        return Carbon::parse(min($candidates))->startOfMonth();
    }

    /** Keys/labels from an inclusive start up to and including CURRENT month */
    protected function monthBucketsInclusive(Carbon $start): array
    {
        $end = now()->startOfMonth(); // current month
        if ($start->greaterThan($end)) {
            $start = $end->copy();
        }

        $keys = [];
        $labels = [];
        $cursor = $start->copy();
        while ($cursor->lessThanOrEqualTo($end)) {
            $keys[]   = $cursor->format('Y-m');
            $labels[] = $cursor->isoFormat('MMM YYYY');
            $cursor->addMonth();
        }
        return ['keys' => $keys, 'labels' => $labels];
    }

    /** Build month buckets for the selected window (inclusive of current month) */
    protected function monthBuckets(string $org): array
    {
        $end = now()->startOfMonth();
        $start = match ($this->window) {
            '6m'  => $end->copy()->subMonthsNoOverflow(5),   // 6 labels incl. current
            '1y'  => $end->copy()->subMonthsNoOverflow(11),  // 12 labels incl. current
            '5y'  => $end->copy()->subMonthsNoOverflow(59),  // 60 labels incl. current
            'all' => $this->earliestMonth($org),
            default => $end->copy()->subMonthsNoOverflow(5),
        };
        return $this->monthBucketsInclusive($start);
    }

    /**
     * Build per-dept monthly datasets (NO "Other") aligned to month keys.
     * Input rows: dept, ym(YYYY-MM), cnt
     * Includes EVERY department that has at least one point in the window.
     */
    protected function buildMonthlySeriesAll(Collection $rows, array $monthKeys): array
    {
        // Order datasets by total descending for nicer legend
        $totalsByDept = $rows->groupBy('dept')->map(fn($g) => (int)$g->sum('cnt'))->sortDesc();

        $idxByYm = array_flip($monthKeys);
        $datasets = [];

        foreach ($totalsByDept as $dept => $total) {
            $series = array_fill(0, count($monthKeys), 0);
            foreach ($rows as $r) {
                if (($r->dept ?? 'Unknown') !== $dept) continue;
                $ym  = $r->ym;
                $cnt = (int)$r->cnt;
                if (isset($idxByYm[$ym])) {
                    $series[$idxByYm[$ym]] += $cnt;
                }
            }
            $datasets[] = [
                'label' => $dept,
                'data'  => $series,
                'color' => $this->deptColor($dept),
            ];
        }

        return $datasets;
    }

    /** Build a single aligned line for a specific department (case-insensitive). */
    protected function buildMineDataset(Collection $rows, array $monthKeys, string $deptLabel, string $color = '#FFFFFF'): array
    {
        $want = mb_strtolower(trim($deptLabel ?? ''));
        $idxByYm = array_flip($monthKeys);
        $series = array_fill(0, count($monthKeys), 0);

        foreach ($rows as $r) {
            $dept = mb_strtolower(trim($r->dept ?? ''));
            if ($dept !== $want) continue;
            $ym = $r->ym;
            $cnt = (int)$r->cnt;
            if (isset($idxByYm[$ym])) $series[$idxByYm[$ym]] += $cnt;
        }

        return ['label' => $deptLabel ?: 'Unknown', 'data' => $series, 'color' => $color];
    }

    /** ===== Shared query set for both charts and exports ===== */
    protected function queryAllRows(string $org, array $monthKeys): array
    {
        $trendStart = Carbon::createFromFormat('Y-m', $monthKeys[0])->startOfMonth();

        $rowsListings = Listing::query()
            ->join('users', 'users.id', '=', 'listings.user_id')
            ->where('users.organization', $org)
            ->where('listings.created_at', '>=', $trendStart)
            ->selectRaw("COALESCE(NULLIF(listings.department,''),'Unknown') AS dept,
                         DATE_FORMAT(listings.created_at,'%Y-%m') AS ym,
                         COUNT(*) AS cnt")
            ->groupBy('dept', 'ym')
            ->get();

        $rowsTasks = Task::query()
            ->join('listings', 'listings.id', '=', 'tasks.listing_id')
            ->join('users as authors', 'authors.id', '=', 'listings.user_id')
            ->where('authors.organization', $org)
            ->where('tasks.created_at', '>=', $trendStart)
            ->selectRaw("COALESCE(NULLIF(listings.department,''),'Unknown') AS dept,
                         DATE_FORMAT(tasks.created_at,'%Y-%m') AS ym,
                         COUNT(*) AS cnt")
            ->groupBy('dept', 'ym')
            ->get();

        $rowsParticipantsAccepted = Application::query()
            ->join('listings', 'listings.id', '=', 'applications.listing_id')
            ->join('users as authors', 'authors.id', '=', 'listings.user_id')
            ->join('users as participants', 'participants.id', '=', 'applications.user_id')
            ->where('authors.organization', $org)
            ->where('applications.accepted', 1)
            ->where('applications.created_at', '>=', $trendStart)
            ->selectRaw("COALESCE(NULLIF(participants.department,''),'Unknown') AS dept,
                         DATE_FORMAT(applications.created_at, '%Y-%m') AS ym,
                         COUNT(DISTINCT applications.user_id) AS cnt")
            ->groupBy('dept', 'ym')
            ->get();

        $rowsUsersPerDept = User::query()
            ->where('organization', $org)
            ->where('created_at', '>=', $trendStart)
            ->selectRaw("COALESCE(NULLIF(department,''),'Unknown') AS dept,
                         DATE_FORMAT(created_at, '%Y-%m') AS ym,
                         COUNT(*) AS cnt")
            ->groupBy('dept', 'ym')
            ->get();

        return compact('rowsListings','rowsTasks','rowsParticipantsAccepted','rowsUsersPerDept');
    }

    /** Tall arrays for export: [ [month, department, count], ... ] */
    protected function rowsToTallArray(Collection $rows): array
    {
        return $rows->map(fn($r) => [
            'month'      => $r->ym,
            'department' => $r->dept ?? 'Unknown',
            'count'      => (int) $r->cnt,
        ])->values()->all();
    }

    /** Simple summary for PDF header/footer */
    protected function buildSummary(Collection $rows, array $monthKeys): array
    {
        $lastKey = $monthKeys[count($monthKeys)-1] ?? null;
        $prevKey = $monthKeys[count($monthKeys)-2] ?? null;

        $totalLast = $lastKey ? (int)$rows->where('ym',$lastKey)->sum('cnt') : 0;
        $totalPrev = $prevKey ? (int)$rows->where('ym',$prevKey)->sum('cnt') : 0;
        $momPct = $totalPrev > 0 ? round(100 * ($totalLast - $totalPrev) / $totalPrev, 1) : null;

        $totalsByDept = $rows->groupBy('dept')->map(fn($g)=> (int)$g->sum('cnt'))->sortDesc();
        $top = $totalsByDept->take(5)->map(fn($v,$k)=> ['department'=>$k,'total'=>$v])->values()->all();

        return [
            'total_last' => $totalLast,
            'total_prev' => $totalPrev,
            'mom_pct'    => $momPct,
            'top5'       => $top,
        ];
    }

    /** ====== Exports ====== */

    public function exportXlsx()
    {
        // Requires: composer require maatwebsite/excel
        if (!class_exists(\Maatwebsite\Excel\Facades\Excel::class)) {
            return $this->exportCsvZip(); // graceful fallback
        }

        $org = $this->user->organization ?? 'Unknown';
        $b = $this->monthBuckets($org);
        $monthKeys = $b['keys'];

        $rows = $this->queryAllRows($org, $monthKeys);

        $payload = [
            'meta' => [
                'org' => $org,
                'window' => $this->window,
                'generated_at' => now()->toDateTimeString(),
                'months' => $b['labels'],
            ],
            'listings'    => $this->rowsToTallArray($rows['rowsListings']),
            'tasks'       => $this->rowsToTallArray($rows['rowsTasks']),
            'participants_accepted' => $this->rowsToTallArray($rows['rowsParticipantsAccepted']),
            'users_all'   => $this->rowsToTallArray($rows['rowsUsersPerDept']),
        ];

        $export = new \App\Support\Exports\OrgAnalyticsExport($payload);
        $filename = 'corim-org-analytics-' . Str::slug($org) . '-' . now()->format('Ymd-His') . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download($export, $filename);
    }

    public function exportCsvZip()
    {
        $org = $this->user->organization ?? 'Unknown';
        $b = $this->monthBuckets($org);
        $monthKeys = $b['keys'];

        $rows = $this->queryAllRows($org, $monthKeys);

        $files = [
            'listings.csv'    => $this->csvFromTall($this->rowsToTallArray($rows['rowsListings'])),
            'tasks.csv'       => $this->csvFromTall($this->rowsToTallArray($rows['rowsTasks'])),
            'participants_accepted.csv' => $this->csvFromTall($this->rowsToTallArray($rows['rowsParticipantsAccepted'])),
            'users_all.csv'   => $this->csvFromTall($this->rowsToTallArray($rows['rowsUsersPerDept'])),
            'meta.txt'        => "org: {$org}\nwindow: {$this->window}\nmonths: ".implode(', ', $b['labels'])."\ngenerated_at: ".now()->toDateTimeString()."\n",
        ];

        $tmp = tempnam(sys_get_temp_dir(), 'corim_zip_');
        $zip = new \ZipArchive();
        $zip->open($tmp, \ZipArchive::CREATE |\ZipArchive::OVERWRITE);
        foreach ($files as $name => $content) {
            $zip->addFromString($name, $content);
        }
        $zip->close();

        $filename = 'corim-org-analytics-' . Str::slug($org) . '-' . now()->format('Ymd-His') . '.zip';
        return Response::download($tmp, $filename)->deleteFileAfterSend(true);
    }

    protected function csvFromTall(array $rows): string
    {
        $out = fopen('php://temp', 'r+');
        fputcsv($out, ['month','department','count']);
        foreach ($rows as $r) {
            fputcsv($out, [$r['month'], $r['department'], $r['count']]);
        }
        rewind($out);
        return stream_get_contents($out) ?: '';
    }

    public function exportPdf(?array $images = null)
    {

        // ---- quick diagnostics ----
    $diag = [
        'php'     => PHP_VERSION,
        'sapi'    => php_sapi_name(),
        'gd'      => extension_loaded('gd'),
        'imagick' => extension_loaded('imagick'),
        'ini'     => php_ini_loaded_file() ?: '(none)',
        'extdir'  => ini_get('extension_dir') ?: '(none)',
    ];
    \Log::info('[PDF export] runtime diag', $diag);
    // ---------------------------
        // Requires: composer require barryvdh/laravel-dompdf
        if (!class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            // Fallback to CSV zip if DomPDF is not installed
            return $this->exportCsvZip();
        }

        $org = $this->user->organization ?? 'Unknown';
        $b = $this->monthBuckets($org);
        $monthKeys   = $b['keys'];
        $monthLabels = $b['labels'];

        $rows = $this->queryAllRows($org, $monthKeys);

        $summary = [
            'listings'   => $this->buildSummary($rows['rowsListings'], $monthKeys),
            'tasks'      => $this->buildSummary($rows['rowsTasks'], $monthKeys),
            'accepted'   => $this->buildSummary($rows['rowsParticipantsAccepted'], $monthKeys),
            'users_all'  => $this->buildSummary($rows['rowsUsersPerDept'], $monthKeys),
        ];

        $html = view('exports.org-analytics-report', [
            'org'    => $org,
            'window' => $this->window,
            'generated_at' => now(),
            'months' => $monthLabels,
            'images' => $images ?? [], // data URLs captured from canvases (optional)
            'summary'=> $summary,
        ])->render();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)->setPaper('a4', 'portrait');
        $filename = 'corim-org-analytics-' . Str::slug($org) . '-' . now()->format('Ymd-His') . '.pdf';

        return response()->streamDownload(function() use ($pdf) {
            echo $pdf->output();
        }, $filename);
    }

    public function render()
    {
        $org = $this->user->organization;

        if (!$org) {
            return view('livewire.org-analytics', [
                'hasOrg'  => false,
                'window'  => $this->window,
                'charts'  => [
                    'listingsMonthlyByDept'              => ['labels' => [], 'datasets' => [], 'mine' => ['label' => '', 'data' => []]],
                    'tasksMonthlyByDept'                 => ['labels' => [], 'datasets' => [], 'mine' => ['label' => '', 'data' => []]],
                    'participantsAcceptedPerDeptMonthly' => ['labels' => [], 'datasets' => [], 'mine' => ['label' => '', 'data' => []]],
                    'usersPerDeptMonthly'                => ['labels' => [], 'datasets' => [], 'mine' => ['label' => '', 'data' => []]],
                ],
                'currentUserDept' => $this->user->department ?: 'Unknown',
            ]);
        }

        // Month buckets (inclusive of current month)
        $buckets     = $this->monthBuckets($org);
        $monthKeys   = $buckets['keys'];
        $monthLabels = $buckets['labels'];

        if (empty($monthKeys)) {
            return view('livewire.org-analytics', [
                'hasOrg'  => true,
                'window'  => $this->window,
                'charts'  => [
                    'listingsMonthlyByDept'              => ['labels' => [], 'datasets' => [], 'mine' => ['label' => '', 'data' => []]],
                    'tasksMonthlyByDept'                 => ['labels' => [], 'datasets' => [], 'mine' => ['label' => '', 'data' => []]],
                    'participantsAcceptedPerDeptMonthly' => ['labels' => [], 'datasets' => [], 'mine' => ['label' => '', 'data' => []]],
                    'usersPerDeptMonthly'                => ['labels' => [], 'datasets' => [], 'mine' => ['label' => '', 'data' => []]],
                ],
                'currentUserDept' => $this->user->department ?: 'Unknown',
            ]);
        }

        // Shared queries
        $rows = $this->queryAllRows($org, $monthKeys);

        // Build datasets (NO "Other")
        $dsListings   = $this->buildMonthlySeriesAll($rows['rowsListings'], $monthKeys);
        $dsTasks      = $this->buildMonthlySeriesAll($rows['rowsTasks'], $monthKeys);
        $dsPartAcc    = $this->buildMonthlySeriesAll($rows['rowsParticipantsAccepted'], $monthKeys);
        $dsUsersAll   = $this->buildMonthlySeriesAll($rows['rowsUsersPerDept'], $monthKeys);

        // 'Mine' fallback lines (so My department never renders empty)
        $currentUserDept = $this->user->department ?: 'Unknown';
        $listingsMine    = $this->buildMineDataset($rows['rowsListings'],              $monthKeys, $currentUserDept, $this->deptColor($currentUserDept));
        $tasksMine       = $this->buildMineDataset($rows['rowsTasks'],                 $monthKeys, $currentUserDept, $this->deptColor($currentUserDept));
        $partAccMine     = $this->buildMineDataset($rows['rowsParticipantsAccepted'],  $monthKeys, $currentUserDept, $this->deptColor($currentUserDept));
        $usersAllMine    = $this->buildMineDataset($rows['rowsUsersPerDept'],          $monthKeys, $currentUserDept, $this->deptColor($currentUserDept));

        return view('livewire.org-analytics', [
            'hasOrg'         => true,
            'window'         => $this->window,
            'charts'         => [
                'listingsMonthlyByDept'              => ['labels' => $monthLabels, 'datasets' => $dsListings,   'mine' => $listingsMine],
                'tasksMonthlyByDept'                 => ['labels' => $monthLabels, 'datasets' => $dsTasks,      'mine' => $tasksMine],
                'participantsAcceptedPerDeptMonthly' => ['labels' => $monthLabels, 'datasets' => $dsPartAcc,    'mine' => $partAccMine],
                'usersPerDeptMonthly'                => ['labels' => $monthLabels, 'datasets' => $dsUsersAll,   'mine' => $usersAllMine],
            ],
            'currentUserDept' => $currentUserDept,
        ]);
    }
}
