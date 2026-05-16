@extends('layouts.admin')
@section('title')
    Settings
@endsection
@section('content')
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card w-100 position-relative overflow-hidden">
                    <div class="card-header px-4 py-3 border-bottom">
                        <div class="row">
                            <div class="col-6 d-flex justify-content-start">
                                <h5 class="card-title fw-semibold mb-0 lh-sm">Setting</h5>
                            </div>
                            <div class="col-6 d-flex justify-content-end">
                                <a href="#0" class="button btn btn-primary home-data-modal-update">
                                    Update
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="table-responsive rounded-2 mb-4">
                            <table class="table border table-bordered table-sm text-nowrap mb-0 align-middle"
                                id="datatable">
                                <thead class="text-dark fs-3">
                                    <tr>
                                        <th width="3%">
                                            <h6 class="fs-3 fw-semibold mb-0">#</h6>
                                        </th>
                                        <th>
                                            <h6 class="fs-3 fw-semibold mb-0">Key</h6>
                                        </th>
                                        <th>
                                            <h6 class="fs-3 fw-semibold mb-0">Value</h6>
                                        </th>
                                        <th>
                                            <h6 class="fs-3 fw-semibold mb-0">Values</h6>
                                        </th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
    </section>

    <div id="HomeModalOne" class="modal fade" tabindex="-1" aria-labelledby="HomeModalOne" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center p-4">
                    <h4 class="modal-title" id="HomeModalOneTitle">Update Settings</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 border-top border-bottom" id="HomeModalOneBody"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-danger text-danger font-medium waves-effect" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).on('click', '.home-data-modal-update', function(e) {
            e.preventDefault();

            // Send an AJAX request to the server to get data for the modal.
            $.ajax({
                url: "{{ route('admin.settings.get.data.page') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                },
                success: function(response) {
                    $('#HomeModalOne').modal('show');
                    $('#HomeModalOneBody').html(response);
                },
            });
        });

        // This function is triggered when a form with the ID 'dataPage-form' is submitted.
        $(document).on('submit', '#dataPage-form', function(e) {
            e.preventDefault();
            var form = $(this);
            var url = form.attr('action');

             // Send an AJAX request to update data on the server.
            $.ajax({
                type: "POST",
                url: url,
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                success: function(data) {
                    if (data.status == 'success') {
                        toastr.success(data.message, '', {
                            showMethod: "slideDown",
                            hideMethod: "slideUp",
                            timeOut: 1500,
                            closeButton: true,
                        });

                        $('#HomeModalOne').modal('hide');
                        $('#HomeModalOneBody').html('');
                        $('#datatable').DataTable().draw();
                    } else {
                        toastr.error(data.message, '', {
                            showMethod: "slideDown",
                            hideMethod: "slideUp",
                            timeOut: 1500,
                            closeButton: true,
                        });

                        $('#HomeModalOne').modal('hide');
                        $('#HomeModalOneBody').html('');
                    }
                },
            });
        });

        $(document).ready(function(e) {
            // Call the 'getData' function to fetch and display initial data.
            getData();
        });

        // Function to fetch and display data.
        function getData() {
            $.ajax({
                type: "POST",
                url: "{{ route('admin.settings.get.data') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                },
                success: function(data) {
                    // Replace the content of the element with the ID 'body-data' with the fetched data.
                    $('#body-data').html('');
                    $('#body-data').prepend(data);
                }
            });
        }

        $(function() {
            var dataTable = $('#datatable').DataTable({
                dom: "Bfrtip",
                buttons: ["copy", "csv", "excel", "pdf", "print"],
                processing: true,
                serverSide: true,
                scrollCollapse: true,
                scrollX: false,
                ajax: {
                    url: '{!! route('admin.settings.data') !!}',
                    type: 'POST',
                    data: function(d) {
                        d._token = $('meta[name=csrf-token]').attr('content');
                    }
                },
                columns: [{data: 'DT_RowIndex',orderable: false,searchable: false},
                    {data: 'key',name: 'settings.key'},
                    {data: 'value',name: 'settings.value'},
                    {data: 'values',name: 'settings.values'}
                ],
                order: [],
                columnDefs: [{
                    targets: [0, 2],
                    className: "text-center"
                }, ],
            });
            $(
                ".buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel"
            ).addClass("btn btn-primary mr-1");

        });
    </script>
@endsection
