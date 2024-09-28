import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import './bootstrap';
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

 // Initialize popovers
$(document).ready(function () {
    $('[data-bs-toggle="popover"]').popover({
        html: true,
        trigger: 'hover',
        container: 'body'
    });
});

// Listing Preview modal
$(document).ready(function() {
    let hoverTimer;

    // Attach hover event to the expand span
    $('.expand-listing').hover(
        function() {
            const title = $(this).data('title');
            const description = $(this).data('description');
            const formattedDescription = description.replace(/\n/g, '<br>');

            hoverTimer = setTimeout(function() {
                $('#descriptionModalLabel').text(title);
                $('#descriptionModal .modal-body').html(formattedDescription);
                $('#descriptionModal').modal('show');
            }, 150); // Wait 0.1 seconds before showing the modal
        },
        function() {
            clearTimeout(hoverTimer); // Clear the timer if the mouse leaves before 0.5 seconds
        }
    );

    // Hide the modal when mouse leaves the modal content area
    $('#descriptionModal .modal-content').mouseleave(function() {
        $('#descriptionModal').modal('hide');
    });
});
