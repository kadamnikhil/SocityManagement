@extends('layouts.admin')
@section('title') My profile @endsection
@php
    $rolesLabel = $user->getRoleNames()->implode(', ');
    $initials = '';
    if ($user->first_name) {
        $initials .= mb_strtoupper(mb_substr($user->first_name, 0, 1));
    }
    if ($user->last_name) {
        $initials .= mb_strtoupper(mb_substr($user->last_name, 0, 1));
    }
    if ($initials === '') {
        $initials = mb_strtoupper(mb_substr((string) $user->email, 0, 1)) ?: '?';
    }
@endphp
@section('content')
<style>
    .acct-wrap { font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif; }
    .acct-hero { border-radius: 1rem; padding: 1.25rem; color: #fff; background: linear-gradient(135deg, #0f6bff, #0b4bb3); box-shadow: 0 18px 42px rgba(15, 107, 255, .22); }
    .acct-hero h1 { font-size: clamp(1.35rem, 2.5vw, 2rem); font-weight: 900; margin: 0; }
    .acct-hero p { margin: .45rem 0 0; color: rgba(255,255,255,.78); max-width: 40rem; }
    .acct-card { border: 0; border-radius: 1rem; box-shadow: 0 12px 30px rgba(15, 23, 42, .08); overflow: hidden; }
    .acct-card .card-header { background: linear-gradient(180deg, #fbfdff, #fff); border-bottom: 1px solid #e8eef7; font-weight: 800; }
    .acct-avatar { width: 4.5rem; height: 4.5rem; border-radius: 50%; background: rgba(255,255,255,.2); border: 2px solid rgba(255,255,255,.35); display: flex; align-items: center; justify-content: center; font-size: 1.35rem; font-weight: 900; flex-shrink: 0; }
    .acct-dl dt { font-size: .72rem; text-transform: uppercase; letter-spacing: .06em; color: #64748b; font-weight: 800; margin-bottom: .2rem; }
    .acct-dl dd { font-weight: 650; color: #0f172a; margin-bottom: 1rem; }
    .acct-dl dd:last-child { margin-bottom: 0; }
</style>
<div class="acct-wrap">
    <div class="acct-hero mb-4">
        <div class="d-flex flex-wrap align-items-center gap-3">
            <div class="acct-avatar text-white">{{ $initials }}</div>
            <div>
                <h1>{{ $user->full_name }}</h1>
                <p class="mb-0">{{ $rolesLabel ?: 'No role assigned' }}</p>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card acct-card h-100">
                <div class="card-header py-3"><i class="ti ti-user me-1 text-primary"></i> Account</div>
                <div class="card-body">
                    <dl class="acct-dl mb-0">
                        <dt>Email</dt>
                        <dd>{{ $user->email ?: '—' }}</dd>
                        <dt>Mobile</dt>
                        <dd>{{ $user->mobile ?: '—' }}</dd>
                        <dt>Status</dt>
                        <dd>
                            <span class="badge {{ $user->status === 'ACTIVE' ? 'bg-success' : 'bg-secondary' }}">
                                {{ $user->status ?? '—' }}
                            </span>
                        </dd>
                        <dt>Email verified</dt>
                        <dd>{{ $user->email_verified_at ? $user->email_verified_at->timezone(config('app.timezone'))->format('M j, Y g:i a') : 'Not verified' }}</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card acct-card h-100">
                <div class="card-header py-3"><i class="ti ti-building me-1 text-primary"></i> Society</div>
                <div class="card-body">
                    <dl class="acct-dl mb-0">
                        <dt>Society name</dt>
                        <dd>{{ $user->society_name ?: '—' }}</dd>
                        <dt>Address</dt>
                        <dd class="mb-3">{{ $user->address ?: '—' }}</dd>
                        <dt>Legacy role (field)</dt>
                        <dd>{{ $user->role ?? '—' }}</dd>
                        <dt>Access roles</dt>
                        <dd>{{ $rolesLabel ?: '—' }}</dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card acct-card">
                <div class="card-header py-3"><i class="ti ti-clock me-1 text-primary"></i> Record</div>
                <div class="card-body">
                    <dl class="acct-dl mb-0 row">
                        <div class="col-md-6">
                            <dt>Member since</dt>
                            <dd>{{ $user->created_at?->timezone(config('app.timezone'))->format('M j, Y g:i a') ?? '—' }}</dd>
                        </div>
                        <div class="col-md-6">
                            <dt>Last updated</dt>
                            <dd>{{ $user->updated_at?->timezone(config('app.timezone'))->format('M j, Y g:i a') ?? '—' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('admin.dashboard.index') }}" class="btn btn-outline-primary"><i class="ti ti-arrow-left me-1"></i> Back to dashboard</a>
    </div>
</div>
@endsection
