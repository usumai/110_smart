</body>
</html>


<script type="text/javascript" language="javascript" src="includes/datatables/jquery.dataTables.min.js"></script>
<script type="text/javascript" language="javascript" src="includes/datatables/dataTables.bootstrap4.min.js"></script>
<script src="includes/bootstrap-4.3.1-dist/js/bootstrap.bundle.min.js"></script>
















<div class="modal fade" id="helpModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Help</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <span id='helpwords'></span>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<script>
$(document).ready(function() {
    $(document).on('click', '.helpBtn', function(){
        let helpwords = $(this).data('helpwords')
        $('#helpwords').html(helpwords)
    });
});
</script>