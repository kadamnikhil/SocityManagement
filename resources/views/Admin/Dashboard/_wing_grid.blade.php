@php
    $wing = $wing ?? null;
    $floors = $floors ?? collect();
    $stats = $stats ?? ['total' => 0, 'occupied' => 0, 'vacant' => 0, 'vehicles' => 0];
@endphp

<section class="dash-wing-section" aria-label="Wing {{ $wing?->code }}">
    <div class="dash-wing-head">
        <h6 class="dash-wing-title">
            <span class="dash-wing-letter">{{ $wing?->code }}</span>
            <span>Wing {{ $wing?->code }}</span>
        </h6>
        <div class="dash-wing-stats">
            <span class="dash-wing-stat" title="Total flats"><i class="ti ti-building"></i> {{ $stats['total'] }} flats</span>
            <span class="dash-wing-stat dash-wing-stat--occ" title="Occupied"><i class="ti ti-user-check"></i> {{ $stats['occupied'] }}</span>
            <span class="dash-wing-stat dash-wing-stat--vac" title="Vacant"><i class="ti ti-user-off"></i> {{ $stats['vacant'] }}</span>
            @if ($stats['vehicles'] > 0)
                <span class="dash-wing-stat dash-wing-stat--veh" title="Vehicles"><i class="ti ti-car"></i> {{ $stats['vehicles'] }}</span>
            @endif
        </div>
    </div>

    @if ($floors->isEmpty())
        <div class="alert alert-light border mb-0 rounded-2 small">No flats generated for this wing yet.</div>
    @else
        <div class="dash-building">
            @foreach ($floors as $floorNumber => $flatsOnFloor)
                @php $isGround = (int) $floorNumber === 0; @endphp
                <div class="dash-floor-row">
                    <div class="dash-floor-label {{ $isGround ? 'dash-floor-label--ground' : '' }}">
                        {{ $isGround ? 'Ground' : 'Floor '.$floorNumber }}
                    </div>
                    <div class="dash-floor-flats">
                        @php $sorted = $flatsOnFloor->sortBy('flat_index'); @endphp
                        @if ($sorted->isEmpty())
                            <div class="dash-floor-empty">No flats</div>
                        @else
                            @foreach ($sorted as $flat)
                                @php
                                    $occupied = trim((string) $flat->owner_name) !== '';
                                    $titleParts = [$flat->unit_code];
                                    $titleParts[] = $occupied ? $flat->owner_name : 'Vacant';
                                    if ($flat->owner_mobile) $titleParts[] = $flat->owner_mobile;
                                    if ((int) $flat->vehicles_count > 0) $titleParts[] = $flat->vehicles_count.' vehicle'.($flat->vehicles_count > 1 ? 's' : '');
                                @endphp
                                <a
                                    href="{{ route('admin.dashboard.flats.show', $flat) }}"
                                    class="dash-flat {{ $occupied ? 'dash-flat--occupied' : 'dash-flat--vacant' }}"
                                    title="{{ implode(' · ', array_filter($titleParts)) }}"
                                    aria-label="Open profile for flat {{ $flat->unit_code }}"
                                >
                                    @if ((int) $flat->vehicles_count > 0)
                                        <span class="dash-flat-veh" aria-label="{{ $flat->vehicles_count }} vehicles">
                                            <i class="ti ti-car"></i>{{ $flat->vehicles_count }}
                                        </span>
                                    @endif
                                    <span class="dash-flat-code">{{ $flat->unit_code }}</span>
                                    <span class="dash-flat-owner">
                                        {{ $occupied ? $flat->owner_name : 'Vacant' }}
                                    </span>
                                </a>
                            @endforeach
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</section>
