<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Organization Analytics</title>
  <style>
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #222; }
    h1 { font-size: 20px; margin: 0 0 6px; }
    h2 { font-size: 14px; margin: 18px 0 6px; }
    .muted { color: #666; }
    .grid { display: table; width: 100%; border-collapse: collapse; }
    .grid .row { display: table-row; }
    .grid .cell { display: table-cell; padding: 6px 8px; border-bottom: 1px solid #eee; }
    .small { font-size: 11px; }
    .section { margin-top: 14px; page-break-inside: avoid; }
    .img { text-align: center; margin: 6px 0 12px; }
    img { max-width: 100%; height: auto; border: 1px solid #ddd; }
  </style>
</head>
<body>
  <h1>Organization Analytics</h1>
  <div class="muted small">
    Org: <strong>{{ $org }}</strong> &nbsp;|&nbsp;
    Window: <strong>{{ $window }}</strong> &nbsp;|&nbsp;
    Months: {{ implode(', ', $months) }} &nbsp;|&nbsp;
    Generated: {{ $generated_at->format('Y-m-d H:i') }}
  </div>

  @php
    $blocks = [
      ['key'=>'listings', 'title'=>'Listings per Department — Trend', 'img'=>$images['listings'] ?? null, 'sum'=>$summary['listings'] ?? null],
      ['key'=>'tasks', 'title'=>'Tasks per Department — Trend', 'img'=>$images['tasks'] ?? null, 'sum'=>$summary['tasks'] ?? null],
      ['key'=>'accepted', 'title'=>'Participating Users (accepted) — per Department', 'img'=>$images['participantsAccepted'] ?? null, 'sum'=>$summary['accepted'] ?? null],
      ['key'=>'users_all', 'title'=>'All Users (registered) — per Department', 'img'=>$images['usersAll'] ?? null, 'sum'=>$summary['users_all'] ?? null],
    ];
  @endphp

  @foreach($blocks as $b)
    <div class="section">
      <h2>{{ $b['title'] }}</h2>
      @if($b['img'])
        <div class="img"><img src="{{ $b['img'] }}" alt="{{ $b['title'] }}"></div>
      @endif
      @if($b['sum'])
        <div class="grid small">
          <div class="row">
            <div class="cell"><strong>Latest month total</strong></div>
            <div class="cell">{{ $b['sum']['total_last'] }}</div>
            <div class="cell"><strong>Prev month total</strong></div>
            <div class="cell">{{ $b['sum']['total_prev'] }}</div>
            <div class="cell"><strong>MoM</strong></div>
            <div class="cell">
              @if(!is_null($b['sum']['mom_pct']))
                {{ $b['sum']['mom_pct'] }}%
              @else
                n/a
              @endif
            </div>
          </div>
        </div>

        <div class="small"><strong>Top 5 departments (in window):</strong></div>
        <div class="grid small">
          @foreach($b['sum']['top5'] as $row)
            <div class="row">
              <div class="cell" style="width:60%">{{ $row['department'] }}</div>
              <div class="cell">{{ $row['total'] }}</div>
            </div>
          @endforeach
        </div>
      @endif
    </div>
  @endforeach
</body>
</html>
