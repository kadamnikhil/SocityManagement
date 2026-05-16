@extends('layouts.admin')
@section('title') Enquiries @endsection
@section('content')
<section>
    <div class="row">
        <div class="col-12">
            <div class="card w-100 position-relative overflow-hidden">
                <div class="card-header px-4 py-3 border-bottom">
                    <div class="row">
                        <div class="col-6 d-flex justify-content-start">
                            <h5 class="card-title fw-semibold mb-0 lh-sm">Enquiry</h5>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive rounded-2 mb-4">
                    <table class="table border table-bordered table-sm mb-0 align-middle" id="datatable">
                        <thead class="text-dark fs-3">
                            <tr>
                                <th width="3%">
                                    <h6 class="fs-3 fw-semibold mb-0">#</h6>
                                </th>
                                <th width="5%">
                                    <h6 class="fs-3 fw-semibold mb-0">Actions</h6>
                                </th>
                                <th>
                                    <h6 class="fs-3 fw-semibold mb-0">Date-Time</h6>
                                </th>
                                <th>
                                    <h6 class="fs-3 fw-semibold mb-0">First Name</h6>
                                </th>
                                <th>
                                    <h6 class="fs-3 fw-semibold mb-0">Last Name</h6>
                                </th>
                                <th>
                                    <h6 class="fs-3 fw-semibold mb-0">E-Mail</h6>
                                </th>
                                <th>
                                    <h6 class="fs-3 fw-semibold mb-0">Mobile</h6>
                                </th>
                                <th>
                                    <h6 class="fs-3 fw-semibold mb-0">Message</h6>
                                </th>
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
        var dataTable = $('#datatable').DataTable({
            dom: "Bfrtip",
            buttons: ["copy", "csv", "excel", "pdf", "print"],
            processing: true,
            serverSide: true,
            scrollCollapse: true,
            scrollX:false,
            ajax: {
                url: '{!! route('admin.enquiries.data') !!}',
                type: 'POST',
                data: function(d) {
                    d._token = $('meta[name=csrf-token]').attr('content');
                }
            },
            columns: [
                {data: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'action',name: 'enquiries.id',searchable: false},
				{data: 'datetime',name: 'enquiries.created_at'},
				{data: 'first_name',name: 'enquiries.first_name'},
                {data: 'last_name',name: 'enquiries.last_name'},
                {data: 'email',name: 'enquiries.email'},
                {data: 'mobile',name: 'enquiries.mobile'},
                {data: 'message',name: 'enquiries.message'},
            ],
            order: [],
            columnDefs: [
                { targets: [0,1], className: "text-center"},
            ],
        }); 
        $(
            ".buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel"
        ).addClass("btn btn-primary mr-1");
    });
</script>
@endsection