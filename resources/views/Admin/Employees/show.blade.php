<div class="row align-items-center">
    <div class="col-lg-4 order-lg-2 order-1">
        <div class="">
            <div class="d-flex align-items-center justify-content-center mb-2">
                <div class="linear-gradient d-flex align-items-center justify-content-center rounded-circle" style="width: 110px; height: 110px;";>
                    <div class="border border-4 border-white d-flex align-items-center justify-content-center rounded-circle overflow-hidden" style="width: 100px; height: 100px;";>
                        <img src="/backend/dist/images/profile/user-1.jpg" alt="" class="w-100 h-100">
                    </div>
                </div>
            </div>
            <div class="text-center">
                <h5 class="fs-5 mb-0 fw-semibold">{{ $employee->user->first_name }} {{ $employee->user->last_name }}</h5>
            </div>
        </div>
    </div>
    <div class="col-lg-8 order-last">
        <ul class="list-unstyled mb-0">
            <li class="d-flex align-items-center gap-3 mb-4">
                <i class="ti ti-id-badge-2 text-dark fs-6"></i>
                <h6 class="fs-4 fw-semibold mb-0">
                    {{ $employee->emp_id }}
                </h6>
            </li>
            <li class="d-flex align-items-center gap-3 mb-4">
                <i class="ti ti-user-circle text-dark fs-6"></i>
                <h6 class="fs-4 fw-semibold mb-0">
                    {{ $employee->designation }}
                </h6>
            </li>
            <li class="d-flex align-items-center gap-3 mb-4">
                <i class="ti ti-circle-key text-dark fs-6"></i>
                <h6 class="fs-4 fw-semibold mb-0">
                    @foreach($employee->user->getRoleNames() as $role)
                        {{ $role }} @if(!$loop->last), @endif
                    @endforeach
                </h6>
            </li>
            <li class="d-flex align-items-center gap-3 mb-4">
                <i class="ti ti-mail text-dark fs-6"></i>
                <h6 class="fs-4 fw-semibold mb-0">{{ $employee->user->email }}</h6>
            </li>
            <li class="d-flex align-items-center gap-3 mb-4">
                <i class="ti ti-mail text-dark fs-6"></i>
                <h6 class="fs-4 fw-semibold mb-0">{{ $employee->personal_email }}</h6>
            </li>
            <li class="d-flex align-items-center gap-3 mb-4">
                <i class="ti ti-phone text-dark fs-6"></i>
                <h6 class="fs-4 fw-semibold mb-0">{{ $employee->user->mobile }}</h6>
            </li>
            <li class="d-flex align-items-center gap-3 mb-2">
                <i class="ti ti-fingerprint text-dark fs-6"></i>
                <h6 class="fs-4 fw-semibold mb-0 {{ ($employee->user->status == 'ACTIVE' ? 'text-success' : 'text-danger' ) }}">
                    {{ ucwords(strtolower($employee->user->status)) }}
                </h6>
            </li>
        </ul>
    </div>
</div>
