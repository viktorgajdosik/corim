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

    //Disabled submit button when creating listing unitl all inputs are filled properly
    document.addEventListener("DOMContentLoaded", function() {
        let titleInput = document.getElementById("title");
        let descriptionInput = document.getElementById("description");
        let departmentInput = document.getElementById("department");
        let createOfferButton = document.getElementById("createOfferButton");

        function validateInputs() {
            if (titleInput.value.length >= 10 &&
                descriptionInput.value.length >= 50 &&
                departmentInput.value !== "") {
                createOfferButton.disabled = false;
            } else {
                createOfferButton.disabled = true;
            }
        }

        titleInput.addEventListener("input", validateInputs);
        descriptionInput.addEventListener("input", validateInputs);
        departmentInput.addEventListener("input", validateInputs);
    });

//Listing Preview modal
$(document).ready(function() {
    // Attach hover event to the expand span
    $('.expand-listing').hover(function() {
        var title = $(this).data('title');
        var description = $(this).data('description');

        // Escape the description and replace newlines with <br> tags
        var formattedDescription = description.replace(/\n/g, '<br>');

        $('#descriptionModalLabel').text(title);
        $('#descriptionModal .modal-body').html(formattedDescription); // Use .html() instead of .text()
        $('#descriptionModal').modal('show');
    });

    // Hide the modal when mouse leaves the modal content area
    $('#descriptionModal .modal-content').mouseleave(function() {
        $('#descriptionModal').modal('hide');
    });
});

