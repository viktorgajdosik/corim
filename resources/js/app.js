import 'bootstrap';

// Initialize popovers
$(document).ready(function () {
    $('[data-bs-toggle="popover"]').popover({
        html: true,
        trigger: 'hover',
        container: 'body'
    });
});
