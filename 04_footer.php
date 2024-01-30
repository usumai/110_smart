
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