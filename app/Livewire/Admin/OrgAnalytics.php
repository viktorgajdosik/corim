<?php

namespace App\Livewire\Admin;

use App\Models\Application;
use App\Models\Listing;
use App\Models\Task;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Livewire\Component;

class OrgAnalytics extends Component
{
    /** Time window: '6m' | '1y' | '5y' | 'all' */
    public string $window = '6m';

    /** 'all' or specific organization name */
    public string $org = 'all';

    public function getOrganizationsProperty()
    {
        return User::query()
            ->whereNotNull('organization')
            ->where('organization', '!=', '')
            ->distinct()
            ->orderBy('organization')
            ->pluck('organization')
            ->values();
    }

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

    protected function earliestMonthForOrg(string $org): Carbon
    {
        if ($org === 'all') {
            $lm = Listing::min('created_at');
            $tm = Task::min('created_at');
            $am = Application::where('accepted', 1)->min('created_at');
            $um = User::min('created_at');
        } else {
            $lm = Listing::query()
                ->leftJoin('users','users.id','=','listings.user_id')
                ->where('users.organization', $org)
                ->min('listings.created_at');

            $tm = Task::query()
                ->join('listings','listings.id','=','tasks.listing_id')
                ->join('users as authors','authors.id','=','listings.user_id')
                ->where('authors.organization', $org)
                ->min('tasks.created_at');

            $am = Application::query()
                ->join('listings','listings.id','=','applications.listing_id')
                ->join('users as authors','authors.id','=','listings.user_id')
                ->where('authors.organization', $org)
                ->where('applications.accepted', 1)
                ->min('applications.created_at');

            $um = User::where('organization', $org)->min('created_at');
        }

        $candidates = array_filter([$lm,$tm,$am,$um]);
        return $candidates ? Carbon::parse(min($candidates))->startOfMonth() : now()->startOfMonth();
    }

    protected function monthBuckets(string $org): array
    {
        $end = now()->startOfMonth();
        $start = match ($this->window) {
            '6m'  => $end->copy()->subMonthsNoOverflow(5),
            '1y'  => $end->copy()->subMonthsNoOverflow(11),
            '5y'  => $end->copy()->subMonthsNoOverflow(59),
            'all' => $this->earliestMonthForOrg($org),
            default => $end->copy()->subMonthsNoOverflow(5),
        };

        $keys=[]; $labels=[];
        $cursor = $start->copy();
        while ($cursor->lessThanOrEqualTo($end)) {
            $keys[]   = $cursor->format('Y-m');
            $labels[] = $cursor->isoFormat('MMM YYYY');
            $cursor->addMonth();
        }
        return ['keys'=>$keys,'labels'=>$labels];
    }

    protected function buildMonthlySeriesAll(Collection $rows, array $monthKeys): array
    {
        $totalsByDept = $rows->groupBy('dept')->map(fn($g) => (int)$g->sum('cnt'))->sortDesc();
        $idxByYm = array_flip($monthKeys);
        $datasets = [];
        foreach ($totalsByDept as $dept => $total) {
            $series = array_fill(0, count($monthKeys), 0);
            foreach ($rows as $r) {
                if (($r->dept ?? 'Unknown') !== $dept) continue;
                $ym  = $r->ym;
                $cnt = (int)$r->cnt;
                if (isset($idxByYm[$ym])) $series[$idxByYm[$ym]] += $cnt;
            }
            $datasets[] = ['label'=>$dept,'data'=>$series,'color'=>$this->deptColor($dept)];
        }
        return $datasets;
    }

    protected function buildMineDataset(Collection $rows, array $monthKeys, string $deptLabel, string $color): array
    {
        $want = mb_strtolower(trim($deptLabel ?? ''));
        $idxByYm = array_flip($monthKeys);
        $series = array_fill(0, count($monthKeys), 0);
        foreach ($rows as $r) {
            $dept = mb_strtolower(trim($r->dept ?? ''));
            if ($dept !== $want) continue;
            $ym = $r->ym; $cnt = (int)$r->cnt;
            if (isset($idxByYm[$ym])) $series[$idxByYm[$ym]] += $cnt;
        }
        return ['label'=>$deptLabel ?: 'Unknown','data'=>$series,'color'=>$color];
    }

    protected function queryAllRows(string $org, array $monthKeys): array
    {
        $trendStart = Carbon::createFromFormat('Y-m', $monthKeys[0])->startOfMonth();

        $rowsListings = Listing::query()
            ->leftJoin('users','users.id','=','listings.user_id')
            ->when($org !== 'all', fn($q)=>$q->where('users.organization',$org))
            ->where('listings.created_at','>=',$trendStart)
            ->selectRaw("COALESCE(NULLIF(listings.department,''),'Unknown') AS dept,
                         DATE_FORMAT(listings.created_at,'%Y-%m') AS ym,
                         COUNT(*) AS cnt")
            ->groupBy('dept','ym')
            ->get();

        $rowsOpenListings = Listing::query()
            ->leftJoin('users','users.id','=','listings.user_id')
            ->when($org !== 'all', fn($q)=>$q->where('users.organization',$org))
            ->where('listings.is_open', 1)
            ->where('listings.created_at','>=',$trendStart)
            ->selectRaw("COALESCE(NULLIF(listings.department,''),'Unknown') AS dept,
                         DATE_FORMAT(listings.created_at,'%Y-%m') AS ym,
                         COUNT(*) AS cnt")
            ->groupBy('dept','ym')
            ->get();

        $rowsTasks = Task::query()
            ->join('listings','listings.id','=','tasks.listing_id')
            ->join('users as authors','authors.id','=','listings.user_id')
            ->when($org !== 'all', fn($q)=>$q->where('authors.organization',$org))
            ->where('tasks.created_at','>=',$trendStart)
            ->selectRaw("COALESCE(NULLIF(listings.department,''),'Unknown') AS dept,
                         DATE_FORMAT(tasks.created_at,'%Y-%m') AS ym,
                         COUNT(*) AS cnt")
            ->groupBy('dept','ym')
            ->get();

        $rowsParticipantsAccepted = Application::query()
            ->join('listings','listings.id','=','applications.listing_id')
            ->join('users as authors','authors.id','=','listings.user_id')
            ->join('users as participants','participants.id','=','applications.user_id')
            ->when($org !== 'all', fn($q)=>$q->where('authors.organization',$org))
            ->where('applications.accepted', 1)
            ->where('applications.created_at','>=',$trendStart)
            ->selectRaw("COALESCE(NULLIF(participants.department,''),'Unknown') AS dept,
                         DATE_FORMAT(applications.created_at,'%Y-%m') AS ym,
                         COUNT(DISTINCT applications.user_id) AS cnt")
            ->groupBy('dept','ym')
            ->get();

        $rowsUsersPerDept = User::query()
            ->when($org !== 'all', fn($q)=>$q->where('organization',$org))
            ->where('created_at','>=',$trendStart)
            ->selectRaw("COALESCE(NULLIF(department,''),'Unknown') AS dept,
                         DATE_FORMAT(created_at,'%Y-%m') AS ym,
                         COUNT(*) AS cnt")
            ->groupBy('dept','ym')
            ->get();

        return compact('rowsListings','rowsOpenListings','rowsTasks','rowsParticipantsAccepted','rowsUsersPerDept');
    }

    protected function rowsToTallArray(Collection $rows): array
    {
        return $rows->map(fn($r)=>[
            'month'=>$r->ym,
            'department'=>$r->dept ?? 'Unknown',
            'count'=>(int)$r->cnt,
        ])->values()->all();
    }

    protected function buildSummary(Collection $rows, array $monthKeys): array
    {
        $lastKey = $monthKeys[count($monthKeys)-1] ?? null;
        $prevKey = $monthKeys[count($monthKeys)-2] ?? null;

        $totalLast = $lastKey ? (int)$rows->where('ym',$lastKey)->sum('cnt') : 0;
        $totalPrev = $prevKey ? (int)$rows->where('ym',$prevKey)->sum('cnt') : 0;
        $momPct = $totalPrev > 0 ? round(100 * ($totalLast - $totalPrev) / $totalPrev, 1) : null;

        $totalsByDept = $rows->groupBy('dept')->map(fn($g)=> (int)$g->sum('cnt'))->sortDesc();
        $top = $totalsByDept->take(5)->map(fn($v,$k)=> ['department'=>$k,'total'=>$v])->values()->all();

        return ['total_last'=>$totalLast,'total_prev'=>$totalPrev,'mom_pct'=>$momPct,'top5'=>$top];
    }

    /** ====== Exports ====== */
    public function exportXlsx()
    {
        if (!class_exists(\Maatwebsite\Excel\Facades\Excel::class)) {
            return $this->exportCsvZip();
        }

        $org = $this->org;
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
        $filename = 'corim-admin-analytics-' . Str::slug($org) . '-' . now()->format('Ymd-His') . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download($export, $filename);
    }

    public function exportCsvZip()
    {
        $org = $this->org;
        $b = $this->monthBuckets($org);
        $monthKeys = $b['keys'];
        $rows = $this->queryAllRows($org, $monthKeys);

        $files = [
            'listings.csv'    => $this->csvFromTall($this->rowsToTallArray($rows['rowsListings'])),
            'tasks.csv'       => $this->csvFromTall($this->rowsToTallArray($rows['rowsTasks'])),
            'participants_accepted.csv' => $this->csvFromTall($this->rowsToTallArray($rows['rowsParticipantsAccepted'])),
            'users_all.csv'   => $this->csvFromTall($this->rowsToTallArray($rows['rowsUsersPerDept'])),
            'open_listings.csv' => $this->csvFromTall($this->rowsToTallArray($rows['rowsOpenListings'])),
            'meta.txt'        => "org: {$org}\nwindow: {$this->window}\nmonths: ".implode(', ', $b['labels'])."\ngenerated_at: ".now()->toDateTimeString()."\n",
        ];

        $tmp = tempnam(sys_get_temp_dir(), 'corim_zip_');
        $zip = new \ZipArchive();
        $zip->open($tmp, \ZipArchive::CREATE |\ZipArchive::OVERWRITE);
        foreach ($files as $name => $content) {
            $zip->addFromString($name, $content);
        }
        $zip->close();

        $filename = 'corim-admin-analytics-' . Str::slug($org) . '-' . now()->format('Ymd-His') . '.zip';
        return Response::download($tmp, $filename)->deleteFileAfterSend(true);
    }

    protected function csvFromTall(array $rows): string
    {
        $out = fopen('php://temp', 'r+');
        fputcsv($out, ['month','department','count']);
        foreach ($rows as $r) fputcsv($out, [$r['month'],$r['department'],$r['count']]);
        rewind($out);
        return stream_get_contents($out) ?: '';
    }

    public function exportPdf(?array $images = null)
    {
        if (!class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            return $this->exportCsvZip();
        }

        Pdf::setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => true,
            'dpi'                  => 144,
        ]);

        $org = $this->org;
        $b = $this->monthBuckets($org);
        $monthKeys   = $b['keys'];
        $monthLabels = $b['labels'];

        $rows = $this->queryAllRows($org, $monthKeys);

        $summary = [
            'listings'       => $this->buildSummary($rows['rowsListings'], $monthKeys),
            'open_listings'  => $this->buildSummary($rows['rowsOpenListings'], $monthKeys),
            'tasks'          => $this->buildSummary($rows['rowsTasks'], $monthKeys),
            'accepted'       => $this->buildSummary($rows['rowsParticipantsAccepted'], $monthKeys),
            'users_all'      => $this->buildSummary($rows['rowsUsersPerDept'], $monthKeys),
        ];

        $html = view('exports.org-analytics-report', [
            'org'    => $org,
            'window' => $this->window,
            'generated_at' => now(),
            'months' => $monthLabels,
            'images' => $images ?? [],
            'summary'=> $summary,
        ])->render();

        $pdf = Pdf::loadHTML($html)->setPaper('a4', 'portrait');
        $filename = 'corim-admin-analytics-' . Str::slug($org) . '-' . now()->format('Ymd-His') . '.pdf';

        return response()->streamDownload(function() use ($pdf) {
            echo $pdf->output();
        }, $filename);
    }

    public function render()
    {
        $buckets = $this->monthBuckets($this->org);
        $monthKeys   = $buckets['keys'];
        $monthLabels = $buckets['labels'];

        if (empty($monthKeys)) {
            $empty = ['labels'=>[], 'datasets'=>[], 'mine'=>['label'=>'','data'=>[]]];
            return view('livewire.admin.org-analytics', [
                'organizations' => $this->organizations,
                'window' => $this->window,
                'charts' => [
                    'openListingsMonthlyByDept'          => $empty,
                    'listingsMonthlyByDept'              => $empty,
                    'tasksMonthlyByDept'                 => $empty,
                    'participantsAcceptedPerDeptMonthly' => $empty,
                    'usersPerDeptMonthly'                => $empty,
                ],
                'currentUserDept' => auth()->user()->department ?: 'Unknown',
            ]);
        }

        $rows = $this->queryAllRows($this->org, $monthKeys);

        $dsListings       = $this->buildMonthlySeriesAll($rows['rowsListings'], $monthKeys);
        $dsOpenListings   = $this->buildMonthlySeriesAll($rows['rowsOpenListings'], $monthKeys);
        $dsTasks          = $this->buildMonthlySeriesAll($rows['rowsTasks'], $monthKeys);
        $dsPartAcc        = $this->buildMonthlySeriesAll($rows['rowsParticipantsAccepted'], $monthKeys);
        $dsUsersAll       = $this->buildMonthlySeriesAll($rows['rowsUsersPerDept'], $monthKeys);

        $currentUserDept  = auth()->user()->department ?: 'Unknown';
        $listingsMine     = $this->buildMineDataset($rows['rowsListings'],             $monthKeys, $currentUserDept, $this->deptColor($currentUserDept));
        $openListingsMine = $this->buildMineDataset($rows['rowsOpenListings'],         $monthKeys, $currentUserDept, $this->deptColor($currentUserDept));
        $tasksMine        = $this->buildMineDataset($rows['rowsTasks'],                $monthKeys, $this->deptColor($currentUserDept) ? $currentUserDept : 'Unknown', $this->deptColor($currentUserDept));
        $partAccMine      = $this->buildMineDataset($rows['rowsParticipantsAccepted'], $monthKeys, $currentUserDept, $this->deptColor($currentUserDept));
        $usersAllMine     = $this->buildMineDataset($rows['rowsUsersPerDept'],         $monthKeys, $currentUserDept, $this->deptColor($currentUserDept));

        return view('livewire.admin.org-analytics', [
            'organizations' => $this->organizations,
            'window'        => $this->window,
            'charts'        => [
                'openListingsMonthlyByDept'          => ['labels'=>$monthLabels,'datasets'=>$dsOpenListings,'mine'=>$openListingsMine],
                'listingsMonthlyByDept'              => ['labels'=>$monthLabels,'datasets'=>$dsListings,    'mine'=>$listingsMine],
                'tasksMonthlyByDept'                 => ['labels'=>$monthLabels,'datasets'=>$dsTasks,       'mine'=>$tasksMine],
                'participantsAcceptedPerDeptMonthly' => ['labels'=>$monthLabels,'datasets'=>$dsPartAcc,     'mine'=>$partAccMine],
                'usersPerDeptMonthly'                => ['labels'=>$monthLabels,'datasets'=>$dsUsersAll,    'mine'=>$usersAllMine],
            ],
            'currentUserDept' => $currentUserDept,
        ]);
    }
}
