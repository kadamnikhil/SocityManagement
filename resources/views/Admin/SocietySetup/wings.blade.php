@extends('layouts.admin')
@section('title') Society Setup - Building layout @endsection

@section('content')
<style>
    .ss-page {
        --ss-primary: #0f6bff;
        --ss-primary-dark: #0b4bb3;
        --ss-accent: #0f766e;
        --ss-danger: #b45309;
        --ss-border: #e2e8f0;
        --ss-soft: #f8fafc;
        --ss-muted: #64748b;
        --ss-text: #0f172a;
        --ss-radius: 1rem;
        --ss-radius-sm: .85rem;
        --ss-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
        font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
    }
    .ss-shell { width: 100%; margin: 0 auto; }
    .ss-hero {
        position: relative;
        overflow: hidden;
        border-radius: 1rem;
        padding: 1.35rem;
        color: #fff;
        background: linear-gradient(135deg, #0f6bff, #0b4bb3);
        box-shadow: 0 18px 42px rgba(15, 107, 255, .22);
        margin-bottom: 1rem;
    }
    .ss-hero::after {
        content: "";
        position: absolute;
        right: -4rem;
        bottom: -5rem;
        width: 16rem;
        height: 16rem;
        border-radius: 50%;
        background: rgba(255,255,255,.1);
    }
    .ss-hero-content { position: relative; z-index: 1; }
    .ss-eyebrow {
        color: rgba(255,255,255,.68) !important;
        font-size: .72rem;
        font-weight: 850;
        text-transform: uppercase;
        letter-spacing: .08em;
        margin-bottom: .3rem;
    }
    .ss-title {
        margin: 0;
        color: #fff !important;
        font-size: clamp(1.45rem, 2.5vw, 2.25rem);
        font-weight: 900 !important;
        letter-spacing: 0;
        line-height: 1.15;
    }
    .ss-sub {
        color: rgba(255,255,255,.78) !important;
        max-width: 44rem;
        margin: .45rem 0 0;
    }
    .ss-hero-actions { display: flex; flex-wrap: wrap; gap: .5rem; }
    .ss-quick-stats { display: flex; flex-wrap: wrap; gap: .5rem; margin-top: 1rem; }
    .ss-quick-stat {
        background: rgba(255,255,255,.13);
        border: 1px solid rgba(255,255,255,.18);
        border-radius: .85rem;
        padding: .8rem .9rem;
        min-width: 8.8rem;
    }
    .ss-quick-stat span {
        display: block;
        color: rgba(255,255,255,.68);
        font-size: .72rem;
        font-weight: 800;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }
    .ss-quick-stat strong {
        display: block;
        color: #fff;
        font-size: 1.45rem;
        line-height: 1;
        margin-top: .25rem;
    }

    .ss-stepper {
        display: grid;
        grid-template-columns: 1fr;
        gap: 0.75rem;
        margin: 1rem 0 1.15rem;
    }
    @media (min-width: 640px) { .ss-stepper { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    .ss-step {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.85rem;
        border: 1px solid var(--ss-border);
        border-radius: var(--ss-radius-sm);
        background: #fff;
        color: inherit;
        text-decoration: none;
        box-shadow: 0 4px 14px rgba(15, 23, 42, .04);
    }
    .ss-step--active { border-color: #93c5fd; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.08); }
    .ss-step:hover { color: inherit; border-color: #93c5fd; }
    .ss-step-num {
        width: 2rem;
        height: 2rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        background: #e2e8f0;
        color: #475569;
        font-weight: 800;
        flex: 0 0 auto;
    }
    .ss-step--active .ss-step-num { background: var(--ss-primary); color: #fff; }
    .ss-step-title { margin: 0; color: var(--ss-text); font-size: 0.9rem; font-weight: 800; line-height: 1.2; }

    .ss-layout {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
        align-items: start;
    }
    @media (min-width: 940px) { .ss-layout { grid-template-columns: minmax(0, 1fr) 320px; } }
    .ss-panel {
        overflow: hidden;
        border-radius: var(--ss-radius);
        background: #fff;
        border: 0;
        box-shadow: var(--ss-shadow);
    }
    .ss-panel-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        padding: 1rem 1.1rem;
        border-bottom: 1px solid var(--ss-border);
        background: #fbfcff;
    }
    .ss-panel-title { display: flex; align-items: center; gap: 0.55rem; margin: 0; color: var(--ss-text) !important; font-size: 1rem; font-weight: 900 !important; }
    .ss-panel-body { padding: 1rem 1.1rem 1.15rem; }

    .ss-label { display: block; margin: 0 0 0.35rem; color: #334155; font-size: 0.8rem; font-weight: 800; }
    .ss-field {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        padding: 0.72rem 0.8rem;
        border: 1px solid #dbe4f0;
        border-radius: .65rem;
        background: #fff;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }
    .ss-field:focus-within { border-color: #93c5fd; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); }
    .ss-field i { color: #64748b; font-size: 1.05rem; }
    .ss-field .form-select { border: 0; padding: 0; background-color: transparent; font-weight: 700; color: var(--ss-text); }
    .ss-field .form-select:focus { box-shadow: none; }
    .ss-section-gap { margin-top: 1.15rem; }

    .ss-wing-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 0.75rem;
        margin-top: 0.75rem;
    }
    @media (min-width: 720px) { .ss-wing-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    .ss-wing-row {
        border: 1px solid var(--ss-border);
        border-radius: .9rem;
        background: var(--ss-soft);
        padding: 0.85rem;
    }
    .ss-wing-row-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        margin-bottom: 0.7rem;
    }
    .ss-wing-name { display: flex; align-items: center; gap: 0.55rem; min-width: 0; }
    .ss-wing-letter {
        width: 2.35rem;
        height: 2.35rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: .72rem;
        background: #e0f2fe;
        color: #0369a1;
        font-size: 1rem;
        font-weight: 800;
        flex: 0 0 auto;
    }
    .ss-wing-title { margin: 0; color: var(--ss-text); font-weight: 800; font-size: 0.92rem; }
    .ss-quick {
        display: flex;
        flex-wrap: wrap;
        gap: 0.4rem;
        margin-top: 0.55rem;
    }
    .ss-quick-btn {
        border: 1px solid #cbd5e1;
        border-radius: 999px;
        background: #fff;
        color: #334155;
        padding: 0.28rem 0.55rem;
        font-size: 0.72rem;
        font-weight: 800;
        line-height: 1.1;
    }
    .ss-quick-btn:hover,
    .ss-quick-btn:focus { border-color: #93c5fd; color: #1d4ed8; outline: 0; }
    .ss-ground-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        margin-bottom: 0.65rem;
        padding: 0.35rem 0.55rem;
        border-radius: 999px;
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
        font-size: 0.72rem;
        font-weight: 800;
    }

    .ss-actions {
        display: flex;
        flex-direction: column;
        gap: 0.65rem;
        margin-top: 1.15rem;
        padding-top: 1rem;
        border-top: 1px solid var(--ss-border);
    }
    @media (min-width: 560px) { .ss-actions { flex-direction: row; flex-wrap: wrap; align-items: center; } }
    .ss-btn-primary,
    .ss-btn-secondary {
        border-radius: var(--ss-radius);
        font-weight: 800;
        padding: 0.72rem 1.05rem;
    }
    .ss-btn-primary { background: var(--ss-primary); border-color: var(--ss-primary); box-shadow: 0 8px 18px rgba(37, 99, 235, 0.22); }
    .ss-btn-primary:hover { background: var(--ss-primary-dark); border-color: var(--ss-primary-dark); }
    .ss-btn-secondary { border: 1px solid #cbd5e1; background: #fff; color: #334155; }
    .ss-btn-secondary:hover { border-color: var(--ss-primary); color: var(--ss-primary); background: #eff6ff; }

    .ss-summary {
        position: sticky;
        top: 0.75rem;
    }
    .ss-stat-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.65rem;
        margin-bottom: 0.9rem;
    }
    .ss-stat {
        border: 1px solid var(--ss-border);
        border-radius: .85rem;
        background: #fff;
        padding: 0.75rem;
    }
    .ss-stat-label { display: block; color: var(--ss-muted); font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; }
    .ss-stat-value { display: block; margin-top: 0.25rem; color: var(--ss-text); font-size: 1.45rem; font-weight: 800; line-height: 1; }
    .ss-preview {
        border: 1px solid var(--ss-border);
        border-radius: .85rem;
        background: var(--ss-soft);
        padding: 0.75rem;
    }
    .ss-preview-title { margin: 0 0 0.55rem; color: var(--ss-text); font-size: 0.82rem; font-weight: 800; }
    .ss-preview-list { display: grid; gap: 0.45rem; margin: 0; padding: 0; list-style: none; }
    .ss-preview-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        border-radius: 6px;
        background: #fff;
        border: 1px solid #e2e8f0;
        padding: 0.5rem 0.6rem;
        color: #334155;
        font-size: 0.8rem;
        font-weight: 700;
    }
    .ss-preview-item span:last-child { color: var(--ss-accent); white-space: nowrap; }
</style>

@php
    $wingsDefaults = [
        'total' => (int) old('total_wings', $existing['total'] ?: 1),
        'floors' => array_values(old('floors', $existing['floors'] ?? [])),
    ];
    $configuredWings = $wings->count();
    $selectedWingFloors = collect(range(0, max($wingsDefaults['total'], 1) - 1))
        ->map(fn ($index) => max((int) ($wingsDefaults['floors'][$index] ?? 1), 1));
    $configuredFloors = $selectedWingFloors->sum() + $selectedWingFloors->count();
@endphp

<section class="ss-page">
    <div class="ss-shell">
        <div class="ss-hero">
            <div class="ss-hero-content">
                <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
                    <div>
                        <div class="ss-eyebrow">Society setup</div>
                        <h1 class="ss-title">Building Layout</h1>
                        <p class="ss-sub">Set up wings and floors first, then continue to flats and resident details.</p>
                    </div>
                    <div class="ss-hero-actions">
                        <a href="{{ route('admin.dashboard.index') }}" class="btn btn-light btn-sm fw-semibold">
                            <i class="ti ti-layout-dashboard me-1"></i> Dashboard
                        </a>
                        @if ($wings->isNotEmpty())
                            <a href="{{ route('admin.society-setup.flats') }}" class="btn btn-warning btn-sm fw-semibold">
                                <i class="ti ti-arrow-right me-1"></i> Continue to flats
                            </a>
                        @endif
                    </div>
                </div>
                <div class="ss-quick-stats">
                    <div class="ss-quick-stat"><span>Configured wings</span><strong>{{ $configuredWings }}</strong></div>
                    <div class="ss-quick-stat"><span>Selected wings</span><strong>{{ $wingsDefaults['total'] }}</strong></div>
                    <div class="ss-quick-stat"><span>Total floors</span><strong>{{ $configuredFloors }}</strong></div>
                </div>
            </div>
        </div>

        <nav class="ss-stepper" aria-label="Setup steps">
            <div class="ss-step ss-step--active" aria-current="step">
                <span class="ss-step-num">1</span>
                <div>
                    <p class="ss-step-title">Building layout</p>
                </div>
            </div>
            <a href="{{ route('admin.society-setup.flats') }}" class="ss-step">
                <span class="ss-step-num">2</span>
                <div>
                    <p class="ss-step-title">Flats and residents</p>
                </div>
            </a>
        </nav>

        @if (session('success'))
            <div class="alert alert-success border-0 rounded-3 shadow-sm mb-3">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if (session('warning'))
            <div class="alert alert-warning border-0 rounded-3 shadow-sm mb-3">{{ session('warning') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif

        <div class="ss-layout">
            <div class="ss-panel">
                <div class="ss-panel-head">
                    <div>
                        <h2 class="ss-panel-title"><i class="ti ti-layout-grid text-primary"></i> Building details</h2>
                    </div>
                </div>
                <div class="ss-panel-body">
                    @if ($errors->any())
                        <div class="alert alert-danger small rounded-3 mb-3"><ul class="mb-0 ps-3">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
                    @endif

                    @can('society-setup-manage')
                        <form method="post" action="{{ route('admin.society-setup.wings.store') }}" id="wings-form">
                            @csrf
                            <div>
                                <label class="ss-label" for="total_wings">Total wings / blocks</label>
                                <div class="ss-field">
                                    <i class="ti ti-building"></i>
                                    <select class="form-select" id="total_wings" name="total_wings" required>
                                        @for ($n = 1; $n <= 26; $n++)
                                            <option value="{{ $n }}" @selected($wingsDefaults['total'] === $n)>
                                                {{ $n }} {{ $n === 1 ? 'wing' : 'wings' }} - {{ $n === 1 ? 'Wing A' : 'Wing A to '.chr(64 + $n) }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>

                            <div class="ss-section-gap">
                                <label class="ss-label">Floors in each wing</label>
                                <div id="wing-floor-rows" class="ss-wing-grid"></div>
                            </div>

                            <div class="ss-actions">
                                <button type="submit" class="btn btn-primary ss-btn-primary">
                                    <i class="ti ti-device-floppy me-1"></i> Save and continue
                                </button>
                                @if ($wings->isNotEmpty())
                                    <a href="{{ route('admin.society-setup.flats') }}" class="btn ss-btn-secondary text-center">
                                        <i class="ti ti-home-plus me-1"></i> Go to flats setup
                                    </a>
                                @endif
                            </div>
                        </form>
                    @else
                        <p class="text-muted small mb-0">You do not have permission to edit this setup.</p>
                    @endcan
                </div>
            </div>

            <aside class="ss-panel ss-summary" aria-label="Building summary">
                <div class="ss-panel-head">
                    <div>
                        <h2 class="ss-panel-title"><i class="ti ti-clipboard-list text-primary"></i> Summary</h2>
                    </div>
                </div>
                <div class="ss-panel-body">
                    <div class="ss-stat-grid">
                        <div class="ss-stat">
                            <span class="ss-stat-label">Wings</span>
                            <span class="ss-stat-value" id="summary-wings">1</span>
                        </div>
                        <div class="ss-stat">
                            <span class="ss-stat-label">Floors</span>
                            <span class="ss-stat-value" id="summary-floors">1</span>
                        </div>
                    </div>
                    <div class="ss-preview">
                        <p class="ss-preview-title">Layout preview</p>
                        <ul class="ss-preview-list" id="summary-list"></ul>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>

@can('society-setup-manage')
<script>
(function () {
    var existing = @json($wingsDefaults);
    var totalSelect = document.getElementById('total_wings');
    var container = document.getElementById('wing-floor-rows');
    var summaryWings = document.getElementById('summary-wings');
    var summaryFloors = document.getElementById('summary-floors');
    var summaryList = document.getElementById('summary-list');

    function letter(index) {
        return String.fromCharCode(65 + index);
    }

    function plural(value, singular, pluralText) {
        return value + ' ' + (value === 1 ? singular : pluralText);
    }

    function floorOptionsHtml(selected) {
        var html = '';
        for (var floor = 1; floor <= 60; floor++) {
            html += '<option value="' + floor + '"' + (floor === selected ? ' selected' : '') + '>' + plural(floor, 'floor', 'floors') + '</option>';
        }
        return html;
    }

    function selectedFloorValues() {
        if (!container) return [];
        var selects = container.querySelectorAll('select[name="floors[]"]');
        return Array.prototype.map.call(selects, function (select) {
            var value = parseInt(select.value, 10);
            return isNaN(value) ? 1 : value;
        });
    }

    function updateSummary() {
        var floors = selectedFloorValues();
        var totalFloors = floors.reduce(function (sum, value) { return sum + value; }, 0);
        var totalWithGround = totalFloors + floors.length;
        if (summaryWings) summaryWings.textContent = String(floors.length || 1);
        if (summaryFloors) summaryFloors.textContent = String(totalWithGround || 1);
        if (!summaryList) return;
        summaryList.innerHTML = '';
        floors.forEach(function (floorCount, index) {
            var item = document.createElement('li');
            item.className = 'ss-preview-item';
            item.innerHTML = '<span>Wing ' + letter(index) + '</span><span>Ground + ' + plural(floorCount, 'floor', 'floors') + '</span>';
            summaryList.appendChild(item);
        });
    }

    function setFloor(select, value) {
        select.value = String(value);
        updateSummary();
    }

    function render() {
        if (!container || !totalSelect) return;
        var total = parseInt(totalSelect.value, 10) || 1;
        container.innerHTML = '';

        for (var index = 0; index < total; index++) {
            (function (i) {
                var floorsVal = 1;
                if (existing && existing.floors && existing.floors[i]) {
                    floorsVal = parseInt(existing.floors[i], 10) || 1;
                }

                var row = document.createElement('div');
                row.className = 'ss-wing-row';
                row.innerHTML =
                    '<div class="ss-wing-row-top">' +
                        '<div class="ss-wing-name">' +
                            '<span class="ss-wing-letter">' + letter(i) + '</span>' +
                            '<div><p class="ss-wing-title">Wing ' + letter(i) + '</p></div>' +
                        '</div>' +
                    '</div>' +
                    '<div class="ss-ground-pill"><i class="ti ti-home"></i> Ground Floor - 0 flats</div>' +
                    '<div class="ss-field">' +
                        '<i class="ti ti-stairs"></i>' +
                        '<select class="form-select" name="floors[]" required>' + floorOptionsHtml(floorsVal) + '</select>' +
                    '</div>' +
                    '<div class="ss-quick" aria-label="Quick floor choices">' +
                        '<button type="button" class="ss-quick-btn" data-floor="4">4 floors</button>' +
                        '<button type="button" class="ss-quick-btn" data-floor="7">7 floors</button>' +
                        '<button type="button" class="ss-quick-btn" data-floor="10">10 floors</button>' +
                    '</div>';

                var select = row.querySelector('select');
                select.addEventListener('change', updateSummary);
                row.querySelectorAll('[data-floor]').forEach(function (button) {
                    button.addEventListener('click', function () {
                        setFloor(select, parseInt(button.getAttribute('data-floor'), 10));
                    });
                });
                container.appendChild(row);
            })(index);
        }
        updateSummary();
    }

    if (totalSelect) {
        totalSelect.addEventListener('change', function () {
            existing = { floors: selectedFloorValues() };
            render();
        });
        render();
    }
})();
</script>
@endcan
@endsection
