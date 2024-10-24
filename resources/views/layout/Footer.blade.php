<script src="assets/js/core/jquery.min.js"></script>
<script src="assets/js/core/popper.min.js"></script>
<script src="assets/js/core/bootstrap-material-design.min.js"></script>
<script src="assets/js/plugins/perfect-scrollbar.min.js"></script>

<script src="assets/js/material-dashboard.js?v=2.2.2" type="text/javascript"></script>

<script>
    $(document).ready(function() {
        $sidebar = $('.sidebar');
        $sidebar_img_container = $sidebar.find('.sidebar-background');
        $full_page = $('.full-page');
    });
</script>

@stack('scripts')
