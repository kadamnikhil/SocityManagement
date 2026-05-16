<div class="row ">
    <div class="col-lg-4 order-lg-2 order-1">
        <div class="">
            <div class="d-flex align-items-center justify-content-center mb-2">
                <div class="linear-gradient d-flex align-items-center justify-content-center rounded-circle"
                    style="width: 110px; height: 110px;">
                    <div class="border border-4 border-white d-flex align-items-center justify-content-center rounded-circle overflow-hidden"
                        style="width: 100px; height: 100px;">
                        <img src="{{ asset(Storage::url($slider->photo)) }}" alt="" class="w-100 h-100">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-8 order-last">
        <ul class="list-unstyled mb-0">
            <li class="d-flex align-items-center gap-3 mb-4">
                <i class="fas fa-indent text-dark fs-4"></i>
                <h6 class="fs-4 mb-0">
                    Index : &nbsp;
                    {{ $slider->index }}
                </h6>
            </li>
        </ul>
    </div>
</div>
