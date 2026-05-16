<!-- Show Entity Modal -->
<div id="modalOne" class="modal fade" tabindex="-1" aria-labelledby="modalOne" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header d-flex align-items-center p-4">
                <h4 class="modal-title" id="modalOneTitle"></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 border-top border-bottom" id="modalOneBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-danger text-danger font-medium waves-effect" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>




<script>
    $(document).on('click','.modal-one-btn', function(e){
        e.preventDefault();
        var entity = $(this).data('entity');
        var title = $(this).data('title');
        var routeKey = $(this).data('route-key');
        var url = '/admin/'+entity+'/'+routeKey;
        $.ajax({
            type: "GET",
            url: url,
            contentType: false,
            cache: false,
            processData:false,
            success: function(data){
                $('#modalOneTitle').html(title);
                $('#modalOneBody').html(data);
                $('#modalOne').modal('show');
            },
            error: function (xhr, ajaxOptions, thrownError) {
                toastr.error('There is some problem. Please try again','',{   
                    showMethod: "slideDown", 
                    hideMethod: "slideUp", 
                    timeOut: 1500, 
                    closeButton: true,
                });
            }
        });
    });
</script>