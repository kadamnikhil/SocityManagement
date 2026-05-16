@extends('layouts.admin')
@section('title') Customer Profile @endsection

@section('content')
@php
    $fullName = trim(($user->first_name ?? '').' '.($user->last_name ?? '')) ?: 'Unnamed User';
    $initials = strtoupper(substr((string) ($user->first_name ?? 'U'), 0, 1).substr((string) ($user->last_name ?? ''), 0, 1));
    $initials = trim($initials) !== '' ? $initials : 'U';
    $statusClass = $user->status === 'ACTIVE' ? 'is-active' : 'is-inactive';
    $roleNames = $user->getRoleNames();
@endphp

<style>
    .profile-page { --profile-primary:#0f6bff; --profile-dark:#0b4bb3; --profile-ink:#0f172a; --profile-muted:#64748b; --profile-border:#e2e8f0; font-family:system-ui,-apple-system,"Segoe UI",Roboto,sans-serif; }
    .profile-hero { position:relative; overflow:hidden; border-radius:1rem; padding:1.35rem; color:#fff; background:linear-gradient(135deg,#0f6bff,#0b4bb3); box-shadow:0 18px 42px rgba(15,107,255,.22); margin-bottom:1rem; }
    .profile-hero::after { content:""; position:absolute; right:-4rem; bottom:-5rem; width:16rem; height:16rem; border-radius:50%; background:rgba(255,255,255,.1); }
    .profile-hero-content { position:relative; z-index:1; }
    .profile-eyebrow { color:rgba(255,255,255,.68) !important; font-size:.72rem; font-weight:850; text-transform:uppercase; letter-spacing:.08em; margin-bottom:.3rem; }
    .profile-title { color:#fff !important; font-size:clamp(1.45rem,2.5vw,2.25rem); font-weight:900 !important; letter-spacing:0; margin:0; }
    .profile-sub { color:rgba(255,255,255,.78) !important; max-width:44rem; margin:.45rem 0 0; }
    .profile-actions { display:flex; flex-wrap:wrap; gap:.5rem; }
    .profile-stats { display:flex; flex-wrap:wrap; gap:.5rem; margin-top:1rem; }
    .profile-stat { background:rgba(255,255,255,.13); border:1px solid rgba(255,255,255,.18); border-radius:.85rem; padding:.8rem .9rem; min-width:8.8rem; }
    .profile-stat span { display:block; color:rgba(255,255,255,.68); font-size:.72rem; font-weight:800; text-transform:uppercase; letter-spacing:.04em; }
    .profile-stat strong { display:block; color:#fff; font-size:1.45rem; line-height:1; margin-top:.25rem; }
    .profile-card { border:0; border-radius:1rem; box-shadow:0 12px 30px rgba(15,23,42,.08); overflow:hidden; background:#fff; height:100%; }
    .profile-card-head { padding:1rem 1.1rem; border-bottom:1px solid var(--profile-border); background:linear-gradient(180deg,#fbfdff,#fff); }
    .profile-card-title { margin:0; color:var(--profile-ink) !important; font-size:1rem; font-weight:900 !important; display:flex; align-items:center; gap:.45rem; }
    .profile-summary { display:flex; align-items:center; gap:1rem; flex-wrap:wrap; }
    .profile-avatar { width:5rem; height:5rem; border-radius:1.2rem; display:flex; align-items:center; justify-content:center; background:linear-gradient(135deg,#dbeafe,#eff6ff); color:#1d4ed8; font-size:1.45rem; font-weight:950; border:1px solid #bfdbfe; }
    .profile-name h5 { color:#0f172a !important; font-size:1.2rem; font-weight:900 !important; margin:0 0 .3rem; }
    .profile-name p { color:#64748b; margin:0; font-size:.85rem; font-weight:700; }
    .profile-status { display:inline-flex; align-items:center; border-radius:999px; padding:.28rem .65rem; font-size:.72rem; font-weight:850; text-transform:uppercase; letter-spacing:.04em; margin-top:.55rem; }
    .profile-status.is-active { background:#ecfdf5; color:#047857; border:1px solid #a7f3d0; }
    .profile-status.is-inactive { background:#fff7ed; color:#c2410c; border:1px solid #fed7aa; }
    .profile-detail-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:.75rem; }
    .profile-detail { border:1px solid #e8eef7; border-radius:.85rem; background:#f8fafc; padding:.8rem; min-width:0; }
    .profile-detail span { display:flex; align-items:center; gap:.35rem; color:#64748b; font-size:.72rem; font-weight:850; text-transform:uppercase; letter-spacing:.04em; }
    .profile-detail strong { display:block; color:#0f172a; font-size:.92rem; font-weight:900; line-height:1.3; margin-top:.3rem; overflow-wrap:anywhere; }
    .profile-pill-row { display:flex; flex-wrap:wrap; gap:.4rem; }
    .profile-pill { display:inline-flex; align-items:center; border-radius:999px; background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; padding:.32rem .6rem; font-size:.74rem; font-weight:850; line-height:1; }
    .profile-permission-box { max-height:18rem; overflow:auto; border:1px solid #e8eef7; border-radius:.85rem; background:#f8fafc; padding:.75rem; }
    .profile-child-row { display:flex; justify-content:space-between; gap:.75rem; padding:.7rem 0; border-bottom:1px solid #e2e8f0; }
    .profile-child-row:last-child { border-bottom:0; }
    .profile-child-row strong { color:#0f172a; font-size:.88rem; font-weight:900; }
    .profile-child-row span { color:#64748b; font-size:.78rem; font-weight:700; overflow-wrap:anywhere; }
    @media (max-width:767.98px) { .profile-detail-grid { grid-template-columns:1fr; } .profile-avatar { width:4rem; height:4rem; } }
</style>

<section class="profile-page">
    <div class="profile-hero">
        <div class="profile-hero-content">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
                <div>
                    <div class="profile-eyebrow">Customer profile</div>
                    <h4 class="profile-title">{{ $fullName }}</h4>
                    <p class="profile-sub">Complete customer account, society, role, hierarchy, and system details for SuperAdmin review.</p>
                </div>
                <div class="profile-actions">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-light btn-sm fw-semibold"><i class="ti ti-arrow-left me-1"></i> Back to Users</a>
                </div>
            </div>
            <div class="profile-stats">
                <div class="profile-stat"><span>User ID</span><strong>#{{ $user->id }}</strong></div>
                <div class="profile-stat"><span>Wings</span><strong>{{ $user->society_wings_count }}</strong></div>
                <div class="profile-stat"><span>Flats</span><strong>{{ $user->society_flats_count }}</strong></div>
                <div class="profile-stat"><span>Child users</span><strong>{{ $children->count() }}</strong></div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-xl-4">
            <div class="profile-card">
                <div class="card-body p-3 p-md-4">
                    <div class="profile-summary">
                        <div class="profile-avatar">{{ $initials }}</div>
                        <div class="profile-name">
                            <h5>{{ $fullName }}</h5>
                            <p>{{ $user->email ?: 'No email added' }}</p>
                            <span class="profile-status {{ $statusClass }}">{{ ucwords(strtolower((string) $user->status)) }}</span>
                        </div>
                    </div>
                    <div class="profile-pill-row mt-3">
                        @forelse($roleNames as $role)
                            <span class="profile-pill">{{ $role }}</span>
                        @empty
                            <span class="profile-pill">No role assigned</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="profile-card">
                <div class="profile-card-head">
                    <h5 class="profile-card-title"><i class="ti ti-address-book text-primary"></i> Account Details</h5>
                </div>
                <div class="card-body p-3 p-md-4">
                    <div class="profile-detail-grid">
                        <div class="profile-detail"><span><i class="ti ti-user"></i> First name</span><strong>{{ $user->first_name ?: '-' }}</strong></div>
                        <div class="profile-detail"><span><i class="ti ti-user"></i> Last name</span><strong>{{ $user->last_name ?: '-' }}</strong></div>
                        <div class="profile-detail"><span><i class="ti ti-mail"></i> Email</span><strong>{{ $user->email ?: '-' }}</strong></div>
                        <div class="profile-detail"><span><i class="ti ti-phone"></i> Mobile</span><strong>{{ $user->mobile ?: '-' }}</strong></div>
                        <div class="profile-detail"><span><i class="ti ti-shield"></i> App role</span><strong>{{ $user->role ?: '-' }}</strong></div>
                        <div class="profile-detail"><span><i class="ti ti-device-mobile"></i> Device ID</span><strong>{{ $user->device_id ?: '-' }}</strong></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="profile-card">
                <div class="profile-card-head">
                    <h5 class="profile-card-title"><i class="ti ti-building-community text-primary"></i> Society Details</h5>
                </div>
                <div class="card-body p-3 p-md-4">
                    <div class="profile-detail-grid">
                        <div class="profile-detail"><span><i class="ti ti-building"></i> Society name</span><strong>{{ $user->society_name ?: '-' }}</strong></div>
                        <div class="profile-detail"><span><i class="ti ti-map-pin"></i> Address</span><strong>{{ $user->address ?: '-' }}</strong></div>
                        <div class="profile-detail"><span><i class="ti ti-building"></i> Wings created</span><strong>{{ $user->society_wings_count }}</strong></div>
                        <div class="profile-detail"><span><i class="ti ti-home"></i> Flats created</span><strong>{{ $user->society_flats_count }}</strong></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="profile-card">
                <div class="profile-card-head">
                    <h5 class="profile-card-title"><i class="ti ti-clock-cog text-primary"></i> System Details</h5>
                </div>
                <div class="card-body p-3 p-md-4">
                    <div class="profile-detail-grid">
                        <div class="profile-detail"><span><i class="ti ti-calendar-plus"></i> Created</span><strong>{{ optional($user->created_at)->format('d M Y, h:i A') ?: '-' }}</strong></div>
                        <div class="profile-detail"><span><i class="ti ti-calendar-edit"></i> Updated</span><strong>{{ optional($user->updated_at)->format('d M Y, h:i A') ?: '-' }}</strong></div>
                        <div class="profile-detail"><span><i class="ti ti-mail-check"></i> Email verified</span><strong>{{ optional($user->email_verified_at)->format('d M Y, h:i A') ?: 'Not verified' }}</strong></div>
                        <div class="profile-detail"><span><i class="ti ti-route"></i> Route key</span><strong>{{ $user->route_key ?? '-' }}</strong></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="profile-card">
                <div class="profile-card-head">
                    <h5 class="profile-card-title"><i class="ti ti-users-group text-primary"></i> Child Users</h5>
                </div>
                <div class="card-body p-3 p-md-4">
                    @forelse($children as $child)
                        <div class="profile-child-row">
                            <div>
                                <strong>{{ $child->full_name ?: 'Unnamed User' }}</strong>
                                <span class="d-block">{{ $child->email ?: 'No email' }}</span>
                            </div>
                            <span>{{ ucwords(strtolower((string) $child->status)) }}</span>
                        </div>
                    @empty
                        <p class="text-muted mb-0">No child users assigned.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="profile-card">
                <div class="profile-card-head">
                    <h5 class="profile-card-title"><i class="ti ti-lock-check text-primary"></i> Permissions</h5>
                </div>
                <div class="card-body p-3 p-md-4">
                    <div class="profile-permission-box">
                        <div class="profile-pill-row">
                            @forelse($permissions as $permission)
                                <span class="profile-pill">{{ $permission }}</span>
                            @empty
                                <span class="text-muted">No permissions assigned.</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
