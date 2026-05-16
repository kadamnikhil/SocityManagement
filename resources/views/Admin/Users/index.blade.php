@extends('layouts.admin')
@section('title') Users @endsection

@section('content')
<style>
    .users-page { --users-primary:#0f6bff; --users-dark:#0b4bb3; --users-ink:#0f172a; --users-muted:#64748b; --users-border:#e2e8f0; font-family:system-ui,-apple-system,"Segoe UI",Roboto,sans-serif; }
    .users-hero { position:relative; overflow:hidden; border-radius:1rem; padding:1.35rem; color:#fff; background:linear-gradient(135deg,#0f6bff,#0b4bb3); box-shadow:0 18px 42px rgba(15,107,255,.22); margin-bottom:1rem; }
    .users-hero::after { content:""; position:absolute; right:-4rem; bottom:-5rem; width:16rem; height:16rem; border-radius:50%; background:rgba(255,255,255,.1); }
    .users-hero-content { position:relative; z-index:1; }
    .users-eyebrow { color:rgba(255,255,255,.68) !important; font-size:.72rem; font-weight:850; text-transform:uppercase; letter-spacing:.08em; margin-bottom:.3rem; }
    .users-title { color:#fff !important; font-size:clamp(1.45rem,2.5vw,2.25rem); font-weight:900 !important; letter-spacing:0; margin:0; }
    .users-sub { color:rgba(255,255,255,.78) !important; max-width:44rem; margin:.45rem 0 0; }
    .users-stats { display:flex; flex-wrap:wrap; gap:.5rem; margin-top:1rem; }
    .users-stat { background:rgba(255,255,255,.13); border:1px solid rgba(255,255,255,.18); border-radius:.85rem; padding:.8rem .9rem; min-width:8.8rem; }
    .users-stat span { display:block; color:rgba(255,255,255,.68); font-size:.72rem; font-weight:800; text-transform:uppercase; letter-spacing:.04em; }
    .users-stat strong { display:block; color:#fff; font-size:1.45rem; line-height:1; margin-top:.25rem; }
    .users-card { border:0; border-radius:1rem; box-shadow:0 12px 30px rgba(15,23,42,.08); overflow:hidden; background:#fff; }
    .users-card-header { display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:.75rem; padding:1rem 1.1rem; border-bottom:1px solid var(--users-border); background:linear-gradient(180deg,#fbfdff,#fff); }
    .users-card-title { margin:0; color:var(--users-ink) !important; font-size:1rem; font-weight:900 !important; display:flex; align-items:center; gap:.45rem; }
    .users-toolbar { display:flex; flex-wrap:wrap; align-items:center; gap:.65rem; }
    .users-search { position:relative; min-width:min(100%,20rem); }
    .users-search i { position:absolute; left:.8rem; top:50%; transform:translateY(-50%); color:#94a3b8; font-size:1rem; }
    .users-search input { width:100%; min-height:2.55rem; border:1px solid #dbe4f0; border-radius:.75rem; padding:.55rem .85rem .55rem 2.2rem; color:#0f172a; font-weight:650; outline:0; }
    .users-search input:focus { border-color:#93c5fd; box-shadow:0 0 0 .2rem rgba(37,99,235,.12); }
    .users-table-shell { overflow:hidden; border:1px solid #e8eef7; border-radius:1rem; background:#fff; }
    .users-table-wrap { width:100%; overflow-x:auto; }
    .users-table { width:100% !important; margin:0 !important; border-collapse:separate !important; border-spacing:0; }
    .users-table thead th { background:#f8fbff; border-bottom:1px solid #e2e8f0 !important; color:#334155; font-size:.72rem; font-weight:900 !important; letter-spacing:.06em; text-transform:uppercase; padding:.85rem 1rem !important; white-space:nowrap; }
    .users-table tbody td { border-bottom:1px solid #eef2f7; color:#0f172a; padding:.9rem 1rem !important; vertical-align:middle; }
    .users-table tbody tr:hover td { background:#fbfdff; }
    .users-table tbody tr:last-child td { border-bottom:0; }
    .user-row-profile { display:flex; align-items:center; gap:.75rem; min-width:13rem; }
    .user-avatar { width:2.75rem; height:2.75rem; border-radius:.8rem; display:flex; align-items:center; justify-content:center; background:linear-gradient(135deg,#dbeafe,#eff6ff); color:#1d4ed8; font-size:.95rem; font-weight:950; border:1px solid #bfdbfe; flex:0 0 auto; }
    .user-row-name { min-width:0; }
    .user-row-name strong { display:block; color:#0f172a; font-size:.92rem; font-weight:900; line-height:1.2; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .user-row-name span { display:block; color:#64748b; font-size:.74rem; font-weight:800; margin-top:.18rem; }
    .user-contact-stack { display:grid; gap:.28rem; min-width:15rem; }
    .user-contact-stack span { display:flex; align-items:center; gap:.4rem; color:#64748b; font-size:.8rem; font-weight:700; line-height:1.25; white-space:nowrap; }
    .user-contact-stack i { color:#1d4ed8; font-size:.95rem; }
    .user-status-badge { flex:0 0 auto; border-radius:999px; padding:.25rem .55rem; font-size:.68rem; font-weight:850; text-transform:uppercase; letter-spacing:.04em; }
    .user-status-badge.is-active { background:#ecfdf5; color:#047857; border:1px solid #a7f3d0; }
    .user-status-badge.is-inactive { background:#fff7ed; color:#c2410c; border:1px solid #fed7aa; }
    .user-status-wrap { display:flex; align-items:center; gap:.65rem; white-space:nowrap; }
    .user-role-pill { display:inline-flex; align-items:center; border-radius:999px; background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; padding:.28rem .55rem; font-size:.72rem; font-weight:850; line-height:1; }
    .user-roles-cell { display:flex; flex-wrap:wrap; gap:.35rem; min-width:9rem; }
    .users-empty { min-height:20rem; display:flex; align-items:center; justify-content:center; border:1px dashed #cbd5e1; border-radius:.9rem; background:radial-gradient(circle at 50% 10%,rgba(37,99,235,.08),transparent 32%),#f8fafc; color:#64748b; text-align:center; padding:2rem 1rem; }
    .users-empty i { display:block; color:#1d4ed8; font-size:2.5rem; margin-bottom:.75rem; }
    .dataTables_wrapper .dt-buttons { display:flex; flex-wrap:wrap; gap:.35rem; margin-bottom:.75rem; }
    .dataTables_wrapper .dataTables_info { color:#64748b; font-size:.78rem; padding:.75rem 1rem; }
    .dataTables_wrapper .dataTables_paginate { padding-top:.75rem; }
    .dataTables_wrapper .dataTables_filter, .dataTables_wrapper .dataTables_length { display:none; }
    @media (max-width:575.98px) { .users-card-header { align-items:stretch; } .users-toolbar,.users-search { width:100%; } }
</style>

<section class="users-page">
    <div class="users-hero">
        <div class="users-hero-content">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
                <div>
                    <div class="users-eyebrow">User management</div>
                    <h4 class="users-title">Customer Profiles</h4>
                    <p class="users-sub">View every customer account with contact details, role, profile status, and quick actions in one place.</p>
                </div>
            </div>
            <div class="users-stats">
                <div class="users-stat"><span>Total users</span><strong>{{ $summary['total'] ?? 0 }}</strong></div>
                <div class="users-stat"><span>Active</span><strong>{{ $summary['active'] ?? 0 }}</strong></div>
                <div class="users-stat"><span>Inactive</span><strong>{{ $summary['inactive'] ?? 0 }}</strong></div>
            </div>
        </div>
    </div>

    <div class="users-card">
        <div class="users-card-header">
            <div>
                <h5 class="users-card-title"><i class="ti ti-users text-primary"></i> All Customers</h5>
                <p class="text-muted small mb-0 mt-1">Search by name, email, or mobile.</p>
            </div>
            <div class="users-toolbar">
                <div class="users-search">
                    <i class="ti ti-search"></i>
                    <input type="search" id="users-card-search" placeholder="Search customers">
                </div>
            </div>
        </div>
        <div class="card-body p-3 p-md-4">
            <div id="users-empty-state" class="users-empty d-none">
                <div>
                    <i class="ti ti-user-search"></i>
                    <h5 class="fw-bold text-dark mb-2">No customers found</h5>
                    <p class="mb-0">Try a different name, email, mobile number, or role.</p>
                </div>
            </div>
            <div class="users-table-shell">
                <div class="users-table-wrap">
                    <table class="table users-table mb-0" id="datatable">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Contact</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Action</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>E-Mail</th>
                                <th>Mobile</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
    $(function() {
        var emptyState = $('#users-empty-state');
        var dataTable = $('#datatable').DataTable({
            dom: "Brtip",
            buttons: ["copy", "csv", "excel", "pdf", "print"],
            processing: true,
            serverSide: true,
            pageLength: -1,
            lengthMenu: [[25, 50, 100, -1], [25, 50, 100, "All"]],
            ajax: {
                url: '{!! route('admin.users.data') !!}',
                type: 'POST',
                data: function(d) {
                    d._token = $('meta[name=csrf-token]').attr('content');
                }
            },
            columns: [
                {data: 'customer', name: 'users.first_name', orderable: false, searchable: false},
                {data: 'contact', name: 'users.email', orderable: false, searchable: false},
                {data: 'roles_badges', name: 'users.id', orderable: false, searchable: false, className: 'user-roles-cell'},
                {data: 'status_control', name: 'users.status', orderable: false, searchable: false},
                {data: 'profile_action', name: 'users.id', orderable: false, searchable: false, className: 'text-end'},
                {data: 'first_name', name: 'users.first_name', visible: false},
                {data: 'last_name', name: 'users.last_name', visible: false},
                {data: 'email', name: 'users.email', visible: false},
                {data: 'mobile', name: 'users.mobile', visible: false},
            ],
            order: [],
            drawCallback: function() {
                var hasRows = this.api().rows({ page: 'current' }).data().length > 0;
                emptyState.toggleClass('d-none', hasRows);
                $('.users-table-shell').toggleClass('d-none', !hasRows);
            }
        });

        $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-primary btn-sm');

        var searchTimer = null;
        $('#users-card-search').on('input', function() {
            var value = this.value;
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function() {
                dataTable.search(value).draw();
            }, 250);
        });
    });

    $(document).on('change', '.user-status-switch', function(e){
        e.preventDefault();
        var routeKey = $(this).data('routekey');
        var status = $(this).is(':checked') ? 'ACTIVE' : 'INACTIVE';
        $.ajax({
            url: "{{ route('admin.users.change.status') }}",
            type: 'POST',
            data: {
                _token: $('meta[name=csrf-token]').attr('content'),
                route_key: routeKey,
                status: status
            },
            success: function(data) {
                if(data.status == 'success'){
                    toastr.success(data.message,'',{
                        showMethod: "slideDown",
                        hideMethod: "slideUp",
                        timeOut: 1500,
                        closeButton: true,
                    });
                    if($.fn.DataTable.isDataTable("#datatable")){
                        $('#datatable').DataTable().draw(false);
                    }
                }else{
                    toastr.error(data.message,'',{
                        showMethod: "slideDown",
                        hideMethod: "slideUp",
                        timeOut: 1500,
                        closeButton: true,
                    });
                }
            },
            error: function() {
                toastr.error('Something went wrong!');
                if($.fn.DataTable.isDataTable("#datatable")){
                    $('#datatable').DataTable().draw(false);
                }
            }
        });
    });
</script>
@endsection
