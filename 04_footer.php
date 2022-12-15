
<script type="text/javascript" language="javascript" src="includes/datatables/jquery.dataTables.min.js"></script>
<script type="text/javascript" language="javascript" src="includes/datatables/dataTables.bootstrap4.min.js"></script>
<script src="includes/bootstrap-4.3.1-dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    $(document).on('click', '.helpBtn', function(){
        let helpwords = $(this).data('helpwords')
        $('#helpwords').html(helpwords)
    });
});
</script>

</body>

</html>