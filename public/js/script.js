
    // init popover
    $(function () {
        $('[data-toggle="popover"]').popover()
      })

    // Hide the loading spinner when the page is fully loaded
    window.addEventListener('load', function () {
            var loadingSpinner = document.getElementById('loadingSpinner');
              if (loadingSpinner) {
                  loadingSpinner.style.display = 'none';
              }
          });


            // password visibility
            function togglePasswordVisibility() {
                var passwordInput = document.getElementById('password');
                var passwordIcon = document.querySelector('.input-group-append .fa-eye, .input-group-append .fa-eye-slash');

                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    passwordIcon.classList.remove('fa-eye');
                    passwordIcon.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    passwordIcon.classList.remove('fa-eye-slash');
                    passwordIcon.classList.add('fa-eye');
                }
            }

//Disabled sign in button for login until inputs are filled properly
            document.addEventListener('DOMContentLoaded', function() {
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const loginButton = document.getElementById('loginButton');

    function validateInputs() {
        // Check if the email is valid and password is not empty
        if (emailInput.value && isValidEmail(emailInput.value) && passwordInput.value.length >= 8) {
            loginButton.disabled = false;
        } else {
            loginButton.disabled = true;
        }
    }

    function isValidEmail(email) {
        // Simple regex for email validation
        const regex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
        return regex.test(email);
    }

    emailInput.addEventListener('input', validateInputs);
    passwordInput.addEventListener('input', validateInputs);

    // Initial check
    validateInputs();
});

  //Disabled sign up button for login until inputs are filled properly
    document.addEventListener("DOMContentLoaded", function() {
        let nameInput = document.getElementById("name");
        let emailInput = document.getElementById("email");
        let departmentInput = document.getElementById("department");
        let passwordInput = document.getElementById("password");
        let confirmPasswordInput = document.getElementById("password_confirmation");
        let signupButton = document.getElementById("signupButton");

        function validateInputs() {
            if (nameInput.value.length >= 6 &&
                emailInput.value.trim() !== "" &&
                departmentInput.value !== "" &&
                passwordInput.value.length >= 8 &&
                passwordInput.value === confirmPasswordInput.value) {
                signupButton.disabled = false;
            } else {
                signupButton.disabled = true;
            }
        }

        nameInput.addEventListener("input", validateInputs);
        emailInput.addEventListener("input", validateInputs);
        departmentInput.addEventListener("input", validateInputs);
        passwordInput.addEventListener("input", validateInputs);
        confirmPasswordInput.addEventListener("input", validateInputs);
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
$(document).ready(function(){
    // Attach hover event to the expand span
    $('.expand-listing').hover(function(){
        var title = $(this).data('title');
        var description = $(this).data('description');

        $('#descriptionModalLabel').text(title);
        $('#descriptionModal .modal-body').text(description);
        $('#descriptionModal').modal('show');
    });

    // Hide the modal when mouse leaves the modal content area
    $('#descriptionModal .modal-content').mouseleave(function(){
        $('#descriptionModal').modal('hide');
    });
});

