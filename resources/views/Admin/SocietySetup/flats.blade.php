@extends('layouts.admin')
@section('title') Society Setup — Flats & residents @endsection

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&display=swap" rel="stylesheet">

@php
    $flatTabData = [];
    foreach ($wings as $wing) {
        foreach ($wing->flats as $f) {
            $docs = [];
            if (method_exists($f, 'relationLoaded') && $f->relationLoaded('documents')) {
                foreach ($f->documents as $d) {
                    $isImage = str_starts_with((string) ($d->mime_type ?? ''), 'image/')
                        || preg_match('/\.(png|jpe?g|gif|webp)$/i', (string) $d->file_original_name);
                    $docs[] = [
                        'id' => (int) $d->id,
                        'name' => (string) $d->name,
                        'file_original_name' => (string) $d->file_original_name,
                        'file_size_human' => $d->file_size_human,
                        'file_url' => $d->file_url,
                        'icon_class' => $d->icon_class,
                        'is_image' => (bool) $isImage,
                    ];
                }
            }
            $flatTabData[(string) $f->id] = [
                'unit_code' => $f->unit_code,
                'wing_code' => $wing->code,
                'floor_number' => $f->floor_number,
                'floor_label' => (int) $f->floor_number === 0 ? 'Ground Floor' : 'Floor '.$f->floor_number,
                'owner_name' => $f->owner_name ?? '',
                'owner_mobile' => $f->owner_mobile ?? '',
                'owner_email' => $f->owner_email ?? '',
                'vehicles_2w' => (int) ($f->vehicles_2w ?? 0),
                'vehicles_3w' => (int) ($f->vehicles_3w ?? 0),
                'vehicles_4w' => (int) ($f->vehicles_4w ?? 0),
                'vehicles_count' => (int) ($f->vehicles_count ?? 0),
                'documents' => $docs,
            ];
        }
    }
@endphp

<style>
    .sf-page {
        --sf-primary: #2563eb;
        --sf-primary-dark: #1d4ed8;
        --sf-border: #e2e8f0;
        --sf-muted: #64748b;
        --sf-text: #0f172a;
        --sf-radius: 1rem;
        --sf-radius-sm: 0.75rem;
        --sf-shadow: 0 1px 3px rgba(15, 23, 42, 0.06), 0 4px 20px rgba(15, 23, 42, 0.06);
        --sf-shadow-lg: 0 8px 40px rgba(15, 23, 42, 0.08);
        font-family: "DM Sans", system-ui, -apple-system, sans-serif;
        background: linear-gradient(165deg, #e8eef6 0%, #f1f4f9 45%, #f8fafc 100%);
        margin: -0.5rem -0.75rem 0;
        padding: 1rem 0.75rem 2.5rem;
        min-height: calc(100vh - 100px);
    }
    @media (min-width: 768px) { .sf-page { margin: -0.75rem -1.25rem 0; padding: 1.25rem 1.25rem 3rem; } }
    .sf-page .sf-shell { max-width: 800px; margin: 0 auto; }

    .sf-stepper { display: flex; gap: 0.65rem; margin-bottom: 1.5rem; flex-direction: column; }
    @media (min-width: 540px) { .sf-stepper { flex-direction: row; } }
    .sf-stepper .sf-step {
        flex: 1;
        display: flex; align-items: flex-start; gap: 0.65rem;
        padding: 0.85rem 1rem;
        border-radius: var(--sf-radius-sm);
        background: #fff;
        border: 2px solid var(--sf-border);
        text-decoration: none;
        color: inherit;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }
    .sf-stepper a.sf-step:hover { border-color: #cbd5e1; box-shadow: var(--sf-shadow); }
    .sf-stepper .sf-step--done { border-color: #86efac; background: linear-gradient(180deg, #f0fdf4 0%, #fff 100%); }
    .sf-stepper .sf-step--active { border-color: var(--sf-primary); background: linear-gradient(180deg, #eff6ff 0%, #fff 100%); box-shadow: 0 0 0 1px rgba(37, 99, 235, 0.12); }
    .sf-stepper .sf-num {
        width: 2rem; height: 2rem; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 0.875rem; flex-shrink: 0;
        background: #e2e8f0; color: #475569;
    }
    .sf-stepper .sf-step--done .sf-num { background: #22c55e; color: #fff; }
    .sf-stepper .sf-step--active .sf-num { background: var(--sf-primary); color: #fff; }
    .sf-stepper .sf-step-t { font-weight: 700; font-size: 0.9rem; color: var(--sf-text); margin: 0; line-height: 1.25; }
    .sf-stepper .sf-step-d { font-size: 0.75rem; color: var(--sf-muted); margin: 0.2rem 0 0; line-height: 1.35; }

    .sf-page .sf-h1 { font-size: 1.45rem; font-weight: 800; color: var(--sf-text); letter-spacing: -0.03em; margin: 0 0 0.5rem; }
    @media (min-width: 576px) { .sf-page .sf-h1 { font-size: 1.65rem; } }
    .sf-page .sf-lead { font-size: 0.9375rem; color: var(--sf-muted); line-height: 1.55; margin: 0 0 1.25rem; }

    .sf-banner {
        border-radius: var(--sf-radius-sm);
        padding: 0.85rem 1rem;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        font-size: 0.8125rem;
        color: #1e40af;
        margin-bottom: 1.25rem;
        line-height: 1.5;
    }
    .sf-banner strong { color: #1e3a8a; }

    .sf-card {
        background: #fff;
        border: 1px solid var(--sf-border);
        border-radius: var(--sf-radius);
        box-shadow: var(--sf-shadow);
        margin-bottom: 1.25rem;
        overflow: hidden;
    }
    .sf-card-h { padding: 1rem 1.15rem; border-bottom: 1px solid var(--sf-border); background: linear-gradient(180deg, #fafbfc 0%, #fff 100%); }
    .sf-badge {
        display: inline-block;
        font-size: 0.65rem;
        font-weight: 800;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        padding: 0.2rem 0.5rem;
        border-radius: 0.35rem;
        background: #e0e7ff;
        color: #3730a3;
        margin-bottom: 0.35rem;
    }
    .sf-badge--2 { background: #dbeafe; color: #1d4ed8; }
    .sf-card-t { font-size: 1.05rem; font-weight: 700; margin: 0; color: var(--sf-text); display: flex; align-items: center; gap: 0.45rem; flex-wrap: wrap; }
    .sf-card-d { font-size: 0.8125rem; color: var(--sf-muted); margin: 0.4rem 0 0; line-height: 1.45; }
    .sf-card-b { padding: 1.1rem 1.15rem 1.35rem; }

    .sf-empty {
        text-align: center;
        padding: 2rem 1rem;
        border: 2px dashed var(--sf-border);
        border-radius: var(--sf-radius-sm);
        background: #f8fafc;
    }
    .sf-empty .sf-empty-icon { font-size: 2.5rem; color: #cbd5e1; margin-bottom: 0.75rem; }
    .sf-empty p { margin: 0; font-size: 0.875rem; color: var(--sf-muted); max-width: 22rem; margin-left: auto; margin-right: auto; line-height: 1.5; }

    .sf-wing-block {
        background: #f8fafc;
        border: 1px solid var(--sf-border);
        border-radius: var(--sf-radius-sm);
        padding: 1rem;
        margin-bottom: 1rem;
    }
    .sf-wing-block:last-child { margin-bottom: 0; }
    .sf-wing-badge {
        display: inline-flex; align-items: center; gap: 0.35rem;
        font-weight: 700; font-size: 0.8125rem;
        color: var(--sf-primary-dark);
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        padding: 0.35rem 0.65rem;
        border-radius: 2rem;
        margin-bottom: 0.75rem;
    }
    .sf-floor-label { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--sf-muted); margin: 0.75rem 0 0.5rem; }
    .sf-chip-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(104px, 1fr)); gap: 0.5rem; }
    .sf-flat-chip {
        position: relative;
        min-height: 88px;
        padding: 0.55rem 0.4rem;
        border: 2px solid var(--sf-border);
        border-radius: var(--sf-radius-sm);
        background: #fff;
        cursor: pointer;
        transition: all 0.15s ease;
        text-align: center;
        width: 100%;
    }
    .sf-flat-chip:hover { border-color: var(--sf-primary); background: #eff6ff; transform: translateY(-2px); box-shadow: 0 6px 16px rgba(37, 99, 235, 0.12); }
    .sf-flat-chip .sf-code { font-size: 0.78rem; font-weight: 800; color: var(--sf-text); line-height: 1.2; word-break: break-all; }
    .sf-flat-chip .sf-tap { font-size: 0.65rem; color: var(--sf-primary); font-weight: 700; margin-top: 0.35rem; text-transform: uppercase; letter-spacing: 0.04em; }
    .sf-flat-chip .sf-dot {
        position: absolute; top: 6px; right: 6px;
        width: 8px; height: 8px; border-radius: 50%;
        background: #22c55e; opacity: 0;
        box-shadow: 0 0 0 2px #fff;
    }
    .sf-flat-chip.sf-flat-chip--done .sf-dot { opacity: 1; }

    .sf-btn { border-radius: var(--sf-radius-sm); font-weight: 700; padding: 0.75rem 1.35rem; }
    .sf-btn-primary { background: var(--sf-primary); border: none; color: #fff; box-shadow: 0 2px 10px rgba(37, 99, 235, 0.35); }
    .sf-btn-primary:hover { background: var(--sf-primary-dark); color: #fff; }

    /* Modal is rendered outside .sf-page, so --sf-primary was undefined: bg dropped but text stayed white → invisible. */
    #flatDetailModal {
        --sf-primary: #2563eb;
        --sf-primary-dark: #1d4ed8;
        --sf-radius-sm: 0.75rem;
        --sf-shadow-lg: 0 8px 40px rgba(15, 23, 42, 0.08);
    }
    #flatDetailModal .modal-content { border: none; border-radius: 1.1rem; box-shadow: var(--sf-shadow-lg); overflow: hidden; }
    #flatDetailModal .modal-dialog-scrollable .modal-content { max-height: calc(100vh - 3.5rem); }
    #flatDetailModal #flat-detail-form { min-height: 0; }
    #flatDetailModal .modal-body { overflow-y: auto; flex: 1 1 auto; min-height: 0; -webkit-overflow-scrolling: touch; }
    #flatDetailModal .modal-footer { padding: 0.85rem 1.1rem; border-top: 1px solid #e2e8f0; background: #fff; }
    #flatDetailModal .modal-footer.sf-mf { gap: 0.5rem; justify-content: flex-end; flex-shrink: 0; }
    #flatDetailModal .btn.sf-btn-primary {
        font-weight: 700;
        color: #fff !important;
        border: none;
    }
    #flatDetailModal .btn.sf-btn-primary:hover,
    #flatDetailModal .btn.sf-btn-primary:focus {
        color: #fff !important;
    }
    #flatDetailModal .sf-mh {
        background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 50%, #3b82f6 100%);
        color: #fff; padding: 1.15rem 1.25rem; border: none;
    }
    #flatDetailModal .sf-mh .btn-close { filter: invert(1) grayscale(1); opacity: 0.88; }
    #flatDetailModal .sf-eyebrow { font-size: 0.65rem; font-weight: 800; letter-spacing: 0.1em; text-transform: uppercase; opacity: 0.9; margin-bottom: 0.25rem; }
    #flatDetailModal .modal-title { font-weight: 800; font-size: 1.2rem; }
    #flatDetailModal .sf-subtitle { font-size: 0.8125rem; opacity: 0.92; margin: 0.35rem 0 0; font-weight: 500; }
    #flatDetailModal .sf-mb { padding: 1.25rem; }
    #flatDetailModal .form-control { border-radius: 0.65rem; }
    #flatDetailModal .sf-veh {
        border-radius: var(--sf-radius-sm);
        border: 1px solid #e0e7ff;
        background: linear-gradient(180deg, #f5f7ff 0%, #fff 100%);
        padding: 1rem;
    }
    #flatDetailModal .sf-veh-ic {
        width: 46px; height: 46px; border-radius: 0.65rem;
        background: #fff; border: 1px solid #e0e7ff;
        display: flex; align-items: center; justify-content: center;
        color: var(--sf-primary); font-size: 1.25rem;
    }

    #flatDetailModal .sf-section-h {
        display: flex; align-items: center; gap: 0.55rem;
        margin: 0 0 0.55rem; padding-bottom: 0.45rem;
        border-bottom: 1px dashed #e2e8f0;
        font-size: 0.78rem; font-weight: 800; color: #475569;
        letter-spacing: 0.04em; text-transform: uppercase;
    }
    #flatDetailModal .sf-section-h i { font-size: 1rem; color: var(--sf-primary); }
    #flatDetailModal .sf-section { margin-top: 1rem; }

    #flatDetailModal .sf-veh-grid {
        display: grid; gap: 0.6rem;
        grid-template-columns: 1fr;
    }
    @media (min-width: 576px) {
        #flatDetailModal .sf-veh-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    }
    #flatDetailModal .sf-veh-card {
        display: flex; flex-direction: column; gap: 0.55rem;
        padding: 0.85rem;
        border: 1px solid #e0e7ff;
        border-radius: var(--sf-radius-sm);
        background: linear-gradient(180deg, #f5f7ff 0%, #fff 100%);
        transition: border-color .15s ease, box-shadow .15s ease;
    }
    #flatDetailModal .sf-veh-card:focus-within { border-color: #93c5fd; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12); }
    #flatDetailModal .sf-veh-card-top { display: flex; align-items: center; gap: 0.55rem; }
    #flatDetailModal .sf-veh-card-ic {
        width: 2.25rem; height: 2.25rem; flex: 0 0 auto;
        border-radius: 0.55rem; background: #fff; border: 1px solid #e0e7ff;
        display: inline-flex; align-items: center; justify-content: center;
        color: var(--sf-primary); font-size: 1.1rem;
    }
    #flatDetailModal .sf-veh-card-t { font-size: 0.85rem; font-weight: 800; color: #0f172a; margin: 0; line-height: 1.1; }
    #flatDetailModal .sf-veh-card-d { font-size: 0.7rem; color: var(--sf-muted); margin: 0.1rem 0 0; line-height: 1.2; }
    #flatDetailModal .sf-stepper {
        display: inline-flex; align-items: stretch;
        border: 1px solid #cbd5e1; border-radius: 0.55rem; overflow: hidden;
        background: #fff;
        align-self: flex-start;
    }
    #flatDetailModal .sf-stepper-btn {
        width: 2.1rem; border: 0; background: #f8fafc;
        color: #334155; font-size: 1.05rem; font-weight: 800;
        line-height: 1; cursor: pointer;
        display: inline-flex; align-items: center; justify-content: center;
    }
    #flatDetailModal .sf-stepper-btn:hover { background: #eff6ff; color: var(--sf-primary); }
    #flatDetailModal .sf-stepper-btn:disabled { color: #cbd5e1; cursor: not-allowed; background: #f8fafc; }
    #flatDetailModal .sf-stepper-input {
        width: 2.75rem; border: 0; border-left: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0;
        background: #fff; text-align: center;
        font-weight: 800; color: #0f172a; font-size: 0.95rem;
        -moz-appearance: textfield;
    }
    #flatDetailModal .sf-stepper-input::-webkit-outer-spin-button,
    #flatDetailModal .sf-stepper-input::-webkit-inner-spin-button {
        -webkit-appearance: none; margin: 0;
    }
    #flatDetailModal .sf-stepper-input:focus { outline: 0; box-shadow: none; }
    #flatDetailModal .sf-veh-total {
        display: flex; align-items: center; justify-content: space-between;
        gap: 0.5rem; margin-top: 0.6rem; padding: 0.55rem 0.75rem;
        background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 0.55rem;
        font-size: 0.78rem; font-weight: 700; color: #1e40af;
    }
    #flatDetailModal .sf-veh-total strong { font-size: 1rem; color: #1e3a8a; }

    #flatDetailModal .sf-doc-head {
        display: flex; align-items: center; justify-content: space-between; gap: 0.5rem;
        margin: 0 0 0.5rem;
    }
    #flatDetailModal .sf-doc-hint { font-size: 0.72rem; color: var(--sf-muted); margin: 0; line-height: 1.35; }
    #flatDetailModal .sf-doc-list { display: flex; flex-direction: column; gap: 0.5rem; margin: 0; padding: 0; list-style: none; }
    #flatDetailModal .sf-doc-empty {
        padding: 0.85rem 0.9rem; border: 1px dashed var(--sf-border);
        border-radius: var(--sf-radius-sm); color: var(--sf-muted);
        font-size: 0.78rem; text-align: center; background: #f8fafc;
    }
    #flatDetailModal .sf-doc-row {
        display: flex; align-items: center; gap: 0.6rem;
        padding: 0.55rem 0.65rem;
        border: 1px solid var(--sf-border); border-radius: var(--sf-radius-sm);
        background: #fff;
    }
    #flatDetailModal .sf-doc-row--new { background: #f5f7ff; border-color: #c7d2fe; align-items: stretch; }
    #flatDetailModal .sf-doc-row--marked { opacity: 0.55; text-decoration: line-through; }
    #flatDetailModal .sf-doc-icon {
        width: 2.5rem; height: 2.5rem; flex: 0 0 auto;
        border-radius: 0.5rem; background: #fff;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 1.1rem;
        border: 1px solid #e2e8f0;
        color: #475569; text-decoration: none;
        overflow: hidden;
        position: relative;
    }
    #flatDetailModal .sf-doc-icon img {
        width: 100%; height: 100%; object-fit: cover; display: block;
    }
    #flatDetailModal .sf-doc-icon--image { border-color: #bbf7d0; padding: 0; }
    #flatDetailModal .sf-doc-icon--image:hover { box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.18); }
    #flatDetailModal .sf-doc-row--new .sf-doc-icon { background: #eef2ff; color: var(--sf-primary); border-color: #c7d2fe; }
    #flatDetailModal .sf-doc-body { flex: 1 1 auto; min-width: 0; display: flex; flex-direction: column; gap: 0.25rem; }
    #flatDetailModal .sf-doc-body .form-control { font-size: 0.85rem; border-radius: 0.5rem; }
    #flatDetailModal .sf-doc-meta {
        display: flex; align-items: center; gap: 0.35rem;
        font-size: 0.7rem; color: var(--sf-muted);
        min-width: 0;
    }
    #flatDetailModal .sf-doc-meta span { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; min-width: 0; }
    #flatDetailModal .sf-doc-actions { display: inline-flex; gap: 0.25rem; flex: 0 0 auto; align-self: center; }
    #flatDetailModal .sf-doc-actions .btn { padding: 0.3rem 0.45rem; }
</style>

<div class="sf-page">
    <div class="sf-shell">
        <nav class="sf-stepper" aria-label="Setup steps">
            <a href="{{ route('admin.society-setup.wings') }}" class="sf-step sf-step--done">
                <span class="sf-num"><i class="ti ti-check" style="font-size:1rem"></i></span>
                <div>
                    <p class="sf-step-t">Building layout</p>
                    <p class="sf-step-d">Done — tap to go back and change wings/floors.</p>
                </div>
            </a>
            <div class="sf-step sf-step--active" aria-current="step">
                <span class="sf-num">2</span>
                <div>
                    <p class="sf-step-t">Flats &amp; families</p>
                    <p class="sf-step-d">You are on this step now.</p>
                </div>
            </div>
        </nav>

        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-1">
            <h1 class="sf-h1 mb-0">Homes in your society</h1>
            <a href="{{ route('admin.society-setup.wings') }}" class="btn btn-outline-secondary btn-sm rounded-3">← Edit layout</a>
        </div>
        <p class="sf-lead">Do this in order: <strong>①</strong> Tell us how many flats are on each floor and press the blue button to create flat numbers. <strong>②</strong> Tap each flat to add the owner’s name, phone, email, and how many vehicles they have.</p>

        <div class="sf-banner">
            <strong>Flat number</strong> looks like <strong>A-F1-01</strong> = Wing A, Floor 1, flat 01. One card = one home. Green dot = you already saved some info there.
        </div>

        @if (session('success'))
            <div class="alert alert-success border-0 rounded-3 shadow-sm mb-3">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if (session('warning'))
            <div class="alert alert-warning border-0 rounded-3 shadow-sm mb-3">{{ session('warning') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif

        {{-- STEP 1 ON THIS PAGE: generate --}}
        <div class="sf-card">
            <div class="sf-card-h">
                <span class="sf-badge">Step 1 on this page</span>
                <h2 class="sf-card-t"><i class="ti ti-calculator text-primary"></i> How many flats per floor?</h2>
                <p class="sf-card-d">Open each wing below. For every floor, pick how many <strong>apartments (flats)</strong> exist. Then press <strong>Create all flats</strong>. If you change numbers and create again, old flat numbers and family details are removed.</p>
            </div>
            <div class="sf-card-b">
                @if ($errors->any())
                    <div class="alert alert-danger small rounded-3 mb-3"><ul class="mb-0 ps-3">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
                @endif

                @can('society-setup-manage')
                <form method="post" action="{{ route('admin.society-setup.flats.generate') }}">
                    @csrf
                    <div class="accordion" id="wingsAccordion">
                        @foreach ($wings as $idx => $wing)
                            <div class="accordion-item border rounded-3 overflow-hidden mb-2 shadow-sm">
                                <h2 class="accordion-header m-0" id="heading-{{ $wing->id }}">
                                    <button class="accordion-button {{ $idx > 0 ? 'collapsed' : '' }} fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $wing->id }}">
                                        Wing {{ $wing->code }} — Ground + {{ $wing->floors_count }} floor(s)
                                    </button>
                                </h2>
                                <div id="collapse-{{ $wing->id }}" class="accordion-collapse collapse {{ $idx === 0 ? 'show' : '' }}" data-bs-parent="#wingsAccordion">
                                    <div class="accordion-body bg-light bg-opacity-50">
                                        @foreach ($wing->floors as $floor)
                                            <div class="mb-3 pb-3 border-bottom">
                                                <label class="form-label fw-bold mb-1">{{ (int) $floor->floor_number === 0 ? 'Ground Floor' : 'Floor '.$floor->floor_number }}</label>
                                                <select class="form-select rounded-3" name="flats[{{ $wing->id }}][{{ $floor->floor_number }}]" required>
                                                    @php
                                                        $minFlats = (int) $floor->floor_number === 0 ? 0 : 1;
                                                        $defaultFlats = (int) $floor->floor_number === 0 ? 0 : 1;
                                                    @endphp
                                                    @for ($c = $minFlats; $c <= 120; $c++)
                                                        <option value="{{ $c }}" @selected(old('flats.'.$wing->id.'.'.$floor->floor_number, $floor->flats_count ?? $defaultFlats) == $c)>
                                                            {{ $c }} home{{ $c === 1 ? '' : 's' }}
                                                        </option>
                                                    @endfor
                                                </select>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button type="submit" class="btn sf-btn sf-btn-primary w-100 mt-2">
                        <i class="ti ti-home-plus me-1"></i> Create all flats
                    </button>
                </form>
                @else
                    <p class="text-muted small mb-0">You do not have permission to edit this.</p>
                @endcan
            </div>
        </div>

        {{-- STEP 2: directory --}}
        <div class="sf-card">
            <div class="sf-card-h">
                <span class="sf-badge sf-badge--2">Step 2 on this page</span>
                <h2 class="sf-card-t"><i class="ti ti-users text-primary"></i> Add details for each home</h2>
                <p class="sf-card-d">After step 1, your flats appear here. Tap a card to fill owner contact and vehicles for that home only.</p>
            </div>
            <div class="sf-card-b">
                @if (! $hasFlats)
                    <div class="sf-empty">
                        <div class="sf-empty-icon"><i class="ti ti-home-off"></i></div>
                        <p><strong>No flats yet.</strong> Complete step 1 above — choose counts for each floor, then press <strong>Create all flats</strong>. This list will fill automatically.</p>
                    </div>
                @else
                    @foreach ($wings as $wing)
                        <div class="sf-wing-block">
                            <div class="sf-wing-badge"><i class="ti ti-building"></i> Wing {{ $wing->code }}</div>
                            @foreach ($wing->floors as $floor)
                                @php $flatsOnFloor = $wing->flats->where('floor_number', $floor->floor_number)->values(); @endphp
                                @if ($flatsOnFloor->isNotEmpty())
                                    <p class="sf-floor-label">{{ (int) $floor->floor_number === 0 ? 'Ground Floor' : 'Floor '.$floor->floor_number }}</p>
                                    <div class="sf-chip-grid mb-2">
                                        @foreach ($flatsOnFloor as $unit)
                                            @php
                                                $done = (bool) (
                                                    ($unit->owner_name ?? '') !== ''
                                                    || ($unit->owner_mobile ?? '') !== ''
                                                    || ($unit->owner_email ?? '') !== ''
                                                    || (int) ($unit->vehicles_count ?? 0) > 0
                                                );
                                            @endphp
                                            <button
                                                type="button"
                                                class="sf-flat-chip {{ $done ? 'sf-flat-chip--done' : '' }}"
                                                data-flat-id="{{ $unit->id }}"
                                                data-bs-toggle="modal"
                                                data-bs-target="#flatDetailModal"
                                            >
                                                <span class="sf-dot" aria-hidden="true"></span>
                                                <span class="sf-code">{{ $unit->unit_code }}</span>
                                                <span class="sf-tap">Tap to edit</span>
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>

@can('society-setup-manage')
<div class="modal fade" id="flatDetailModal" tabindex="-1" aria-labelledby="flatDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header sf-mh rounded-0">
                <div>
                    <p class="sf-eyebrow mb-0">This home only</p>
                    <h5 class="modal-title text-white mb-0" id="flatDetailModalLabel">—</h5>
                    <p class="sf-subtitle text-white" id="flatDetailModalSub">We save these details for this flat number only.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="" id="flat-detail-form" enctype="multipart/form-data" class="d-flex flex-column flex-grow-1 min-h-0">
                @csrf
                <div class="modal-body sf-mb">

                {{-- OWNER --}}
                <div class="sf-section">
                    <p class="sf-section-h mb-2"><i class="ti ti-user"></i> Owner contact</p>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold small text-secondary" for="modal_owner_name">Owner full name</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="ti ti-user text-muted"></i></span>
                                <input type="text" class="form-control border-start-0 rounded-end-3" name="owner_name" id="modal_owner_name" maxlength="255" placeholder="e.g. Priya Sharma" autocomplete="name">
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small text-secondary" for="modal_owner_mobile">Mobile</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="ti ti-phone text-muted"></i></span>
                                <input type="tel" class="form-control border-start-0 rounded-end-3" name="owner_mobile" id="modal_owner_mobile" maxlength="32" placeholder="10-digit number" autocomplete="tel">
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold small text-secondary" for="modal_owner_email">Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 rounded-start-3"><i class="ti ti-mail text-muted"></i></span>
                                <input type="email" class="form-control border-start-0 rounded-end-3" name="owner_email" id="modal_owner_email" maxlength="255" placeholder="optional" autocomplete="email">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- VEHICLES --}}
                <div class="sf-section">
                    <p class="sf-section-h"><i class="ti ti-car"></i> Parking &amp; vehicles</p>
                    <div class="sf-veh-grid">
                        <div class="sf-veh-card">
                            <div class="sf-veh-card-top">
                                <span class="sf-veh-card-ic"><i class="ti ti-motorbike"></i></span>
                                <div class="flex-grow-1 min-w-0">
                                    <p class="sf-veh-card-t">2 Wheeler</p>
                                    <p class="sf-veh-card-d">Bikes, scooters</p>
                                </div>
                            </div>
                            <div class="sf-stepper" role="group" aria-label="2 wheeler count">
                                <button type="button" class="sf-stepper-btn" data-action="dec" data-target="modal_vehicles_2w" aria-label="Decrease">−</button>
                                <input type="number" class="sf-stepper-input" id="modal_vehicles_2w" name="vehicles_2w" min="0" max="99" value="0" inputmode="numeric">
                                <button type="button" class="sf-stepper-btn" data-action="inc" data-target="modal_vehicles_2w" aria-label="Increase">+</button>
                            </div>
                        </div>
                        <div class="sf-veh-card">
                            <div class="sf-veh-card-top">
                                <span class="sf-veh-card-ic"><i class="ti ti-rickshaw"></i></span>
                                <div class="flex-grow-1 min-w-0">
                                    <p class="sf-veh-card-t">3 Wheeler</p>
                                    <p class="sf-veh-card-d">Auto, e-rickshaw</p>
                                </div>
                            </div>
                            <div class="sf-stepper" role="group" aria-label="3 wheeler count">
                                <button type="button" class="sf-stepper-btn" data-action="dec" data-target="modal_vehicles_3w" aria-label="Decrease">−</button>
                                <input type="number" class="sf-stepper-input" id="modal_vehicles_3w" name="vehicles_3w" min="0" max="99" value="0" inputmode="numeric">
                                <button type="button" class="sf-stepper-btn" data-action="inc" data-target="modal_vehicles_3w" aria-label="Increase">+</button>
                            </div>
                        </div>
                        <div class="sf-veh-card">
                            <div class="sf-veh-card-top">
                                <span class="sf-veh-card-ic"><i class="ti ti-car"></i></span>
                                <div class="flex-grow-1 min-w-0">
                                    <p class="sf-veh-card-t">4 Wheeler</p>
                                    <p class="sf-veh-card-d">Cars, SUVs</p>
                                </div>
                            </div>
                            <div class="sf-stepper" role="group" aria-label="4 wheeler count">
                                <button type="button" class="sf-stepper-btn" data-action="dec" data-target="modal_vehicles_4w" aria-label="Decrease">−</button>
                                <input type="number" class="sf-stepper-input" id="modal_vehicles_4w" name="vehicles_4w" min="0" max="99" value="0" inputmode="numeric">
                                <button type="button" class="sf-stepper-btn" data-action="inc" data-target="modal_vehicles_4w" aria-label="Increase">+</button>
                            </div>
                        </div>
                    </div>
                    <div class="sf-veh-total">
                        <span><i class="ti ti-sum me-1"></i> Total vehicles registered to this flat</span>
                        <strong id="modal_vehicle_display">0</strong>
                    </div>
                </div>

                {{-- DOCUMENTS --}}
                <div class="sf-section">
                    <div class="sf-doc-head">
                        <div>
                            <p class="sf-section-h mb-1" style="border:0; padding:0;"><i class="ti ti-paperclip"></i> Documents</p>
                            <p class="sf-doc-hint">Attach agreements, KYC, NOC, etc. PDF / images / Word / Excel · max 5 MB each. Add as many as you need.</p>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-3 flex-shrink-0" id="sf-doc-add">
                            <i class="ti ti-plus me-1"></i> Add document
                        </button>
                    </div>
                    <ul class="sf-doc-list" id="sf-doc-list" aria-live="polite"></ul>
                </div>

                <template id="sf-doc-tpl-existing">
                    <li class="sf-doc-row sf-doc-row--existing" data-doc-id="">
                        <a class="sf-doc-icon" href="#" target="_blank" rel="noopener" title="Open file">
                            <i class="ti"></i>
                        </a>
                        <div class="sf-doc-body">
                            <input type="text" class="form-control form-control-sm sf-doc-name" maxlength="120" placeholder="Document name">
                            <div class="sf-doc-meta">
                                <i class="ti ti-paperclip"></i>
                                <span class="sf-doc-orig"></span>
                                <span>·</span>
                                <span class="sf-doc-size"></span>
                            </div>
                        </div>
                        <div class="sf-doc-actions">
                            <a class="btn btn-sm btn-light sf-doc-open" href="#" target="_blank" rel="noopener" title="Open"><i class="ti ti-external-link"></i></a>
                            <button type="button" class="btn btn-sm btn-outline-danger sf-doc-delete" data-action="toggle-delete" title="Remove"><i class="ti ti-trash"></i></button>
                        </div>
                    </li>
                </template>

                <template id="sf-doc-tpl-new">
                    <li class="sf-doc-row sf-doc-row--new" data-new-idx="">
                        <span class="sf-doc-icon"><i class="ti ti-cloud-upload"></i></span>
                        <div class="sf-doc-body">
                            <input type="text" class="form-control form-control-sm sf-doc-name" maxlength="120" placeholder="Name (e.g. Sale agreement, KYC, NOC)">
                            <input type="file" class="form-control form-control-sm sf-doc-file" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx" required>
                        </div>
                        <div class="sf-doc-actions">
                            <button type="button" class="btn btn-sm btn-outline-danger" data-action="remove-new" title="Remove"><i class="ti ti-x"></i></button>
                        </div>
                    </li>
                </template>

                </div>{{-- /.modal-body --}}

                <div class="modal-footer sf-mf">
                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn sf-btn sf-btn-primary rounded-3 px-4"><i class="ti ti-check me-1"></i> Save for this flat</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    var flatData = @json($flatTabData);
    var flatFormBase = @json(rtrim(url('admin/society-setup/flat-unit'), '/'));
    var modalEl = document.getElementById('flatDetailModal');
    var formEl = document.getElementById('flat-detail-form');
    var titleEl = document.getElementById('flatDetailModalLabel');
    var subEl = document.getElementById('flatDetailModalSub');

    var vehInputs = {
        '2w': document.getElementById('modal_vehicles_2w'),
        '3w': document.getElementById('modal_vehicles_3w'),
        '4w': document.getElementById('modal_vehicles_4w'),
    };
    var vDisplay = document.getElementById('modal_vehicle_display');
    var docList = document.getElementById('sf-doc-list');
    var docAddBtn = document.getElementById('sf-doc-add');
    var tplExisting = document.getElementById('sf-doc-tpl-existing');
    var tplNew = document.getElementById('sf-doc-tpl-new');
    var newDocCounter = 0;

    function clampNum(el) {
        var n = parseInt(el.value, 10);
        if (isNaN(n) || n < 0) n = 0;
        if (n > 99) n = 99;
        el.value = String(n);
        return n;
    }
    function syncTotal() {
        var total = 0;
        Object.keys(vehInputs).forEach(function (k) {
            if (vehInputs[k]) total += clampNum(vehInputs[k]);
        });
        if (vDisplay) vDisplay.textContent = String(total);
    }
    Object.keys(vehInputs).forEach(function (k) {
        var el = vehInputs[k];
        if (el) el.addEventListener('input', syncTotal);
    });

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.sf-stepper-btn');
        if (!btn || !formEl.contains(btn)) return;
        var targetId = btn.getAttribute('data-target');
        var input = document.getElementById(targetId);
        if (!input) return;
        var delta = btn.getAttribute('data-action') === 'inc' ? 1 : -1;
        var cur = parseInt(input.value, 10);
        if (isNaN(cur)) cur = 0;
        input.value = String(Math.max(0, Math.min(99, cur + delta)));
        syncTotal();
    });

    function renderDocs(docs) {
        if (!docList) return;
        docList.innerHTML = '';
        newDocCounter = 0;
        if (!docs || !docs.length) {
            var li = document.createElement('li');
            li.className = 'sf-doc-empty';
            li.id = 'sf-doc-empty';
            li.innerHTML = '<i class="ti ti-paperclip me-1"></i> No documents yet. Click <strong>Add document</strong> to attach files.';
            docList.appendChild(li);
            return;
        }
        docs.forEach(function (doc) {
            var node = tplExisting.content.firstElementChild.cloneNode(true);
            node.setAttribute('data-doc-id', String(doc.id));
            var iconLink = node.querySelector('.sf-doc-icon');
            var openBtn = node.querySelector('.sf-doc-open');
            if (iconLink) iconLink.setAttribute('href', doc.file_url || '#');
            if (openBtn) openBtn.setAttribute('href', doc.file_url || '#');
            if (doc.is_image && doc.file_url) {
                if (iconLink) {
                    iconLink.classList.add('sf-doc-icon--image');
                    iconLink.setAttribute('title', 'Click to view full image');
                    iconLink.innerHTML = '';
                    var img = document.createElement('img');
                    img.src = doc.file_url;
                    img.alt = doc.name || doc.file_original_name || '';
                    img.loading = 'lazy';
                    iconLink.appendChild(img);
                }
            } else {
                var iconI = node.querySelector('.sf-doc-icon i');
                if (iconI && doc.icon_class) iconI.className = 'ti ' + doc.icon_class;
            }
            var nameInput = node.querySelector('.sf-doc-name');
            if (nameInput) {
                nameInput.value = doc.name || '';
                nameInput.setAttribute('name', 'existing_documents[' + doc.id + '][name]');
            }
            var origEl = node.querySelector('.sf-doc-orig');
            if (origEl) origEl.textContent = doc.file_original_name || '';
            var sizeEl = node.querySelector('.sf-doc-size');
            if (sizeEl) sizeEl.textContent = doc.file_size_human || '';
            docList.appendChild(node);
        });
    }

    function ensureEmptyState() {
        if (!docList) return;
        var hasRows = docList.querySelector('.sf-doc-row');
        var empty = document.getElementById('sf-doc-empty');
        if (hasRows && empty) {
            empty.remove();
        }
        if (!hasRows && !empty) {
            var li = document.createElement('li');
            li.className = 'sf-doc-empty';
            li.id = 'sf-doc-empty';
            li.innerHTML = '<i class="ti ti-paperclip me-1"></i> No documents yet. Click <strong>Add document</strong> to attach files.';
            docList.appendChild(li);
        }
    }

    if (docAddBtn) {
        docAddBtn.addEventListener('click', function () {
            if (!tplNew) return;
            var idx = newDocCounter++;
            var node = tplNew.content.firstElementChild.cloneNode(true);
            node.setAttribute('data-new-idx', String(idx));
            var nameInput = node.querySelector('.sf-doc-name');
            var fileInput = node.querySelector('.sf-doc-file');
            if (nameInput) nameInput.setAttribute('name', 'new_documents[' + idx + '][name]');
            if (fileInput) fileInput.setAttribute('name', 'new_documents[' + idx + '][file]');
            var empty = document.getElementById('sf-doc-empty');
            if (empty) empty.remove();
            docList.appendChild(node);
            if (nameInput) nameInput.focus();
        });
    }

    if (docList) {
        docList.addEventListener('click', function (e) {
            var removeBtn = e.target.closest('[data-action="remove-new"]');
            if (removeBtn) {
                var newRow = removeBtn.closest('.sf-doc-row--new');
                if (newRow) {
                    newRow.remove();
                    ensureEmptyState();
                }
                return;
            }
            var toggleBtn = e.target.closest('[data-action="toggle-delete"]');
            if (toggleBtn) {
                var row = toggleBtn.closest('.sf-doc-row--existing');
                if (!row) return;
                var docId = row.getAttribute('data-doc-id');
                var marked = row.classList.toggle('sf-doc-row--marked');
                var nameInput = row.querySelector('.sf-doc-name');
                if (nameInput) nameInput.disabled = marked;
                var iconEl = toggleBtn.querySelector('i');
                if (iconEl) iconEl.className = marked ? 'ti ti-rotate' : 'ti ti-trash';
                toggleBtn.setAttribute('title', marked ? 'Undo remove' : 'Remove');
                var hidden = row.querySelector('input[type="hidden"][name="delete_documents[]"]');
                if (marked && !hidden && docId) {
                    var input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'delete_documents[]';
                    input.value = docId;
                    row.appendChild(input);
                } else if (!marked && hidden) {
                    hidden.remove();
                }
            }
        });
    }

    if (modalEl && formEl) {
        modalEl.addEventListener('show.bs.modal', function (event) {
            var btn = event.relatedTarget;
            if (!btn || !btn.getAttribute) return;
            var id = btn.getAttribute('data-flat-id');
            if (!id) return;
            var row = flatData[id];
            if (!row) return;

            formEl.action = flatFormBase + '/' + encodeURIComponent(id);
            if (titleEl) titleEl.textContent = row.unit_code;
            if (subEl) subEl.textContent = 'Wing ' + row.wing_code + ' · ' + row.floor_label;
            document.getElementById('modal_owner_name').value = row.owner_name || '';
            document.getElementById('modal_owner_mobile').value = row.owner_mobile || '';
            document.getElementById('modal_owner_email').value = row.owner_email || '';

            if (vehInputs['2w']) vehInputs['2w'].value = String(row.vehicles_2w != null ? row.vehicles_2w : 0);
            if (vehInputs['3w']) vehInputs['3w'].value = String(row.vehicles_3w != null ? row.vehicles_3w : 0);
            if (vehInputs['4w']) vehInputs['4w'].value = String(row.vehicles_4w != null ? row.vehicles_4w : 0);
            syncTotal();

            renderDocs(row.documents || []);
        });
    }
})();
</script>
@endcan
@endsection
