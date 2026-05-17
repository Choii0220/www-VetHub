function redirectFunc(id) {
    if (id == 1) window.location.href = "../views/LoginPage.php";
    if (id == 2) window.location.href = "../views/DashboardPage.php";
    if (id == 3) window.location.href = "../views/RegistrationPage.php";
}

function logout() { 
    window.location.href = "../views/LoginPage.php"; 
} 

function toggleSidebar() {
    let sidebar = document.getElementById("sidebar");
    let content = document.getElementById("content");
    let header = document.getElementById("header");

    sidebar.classList.toggle("collapsed");
    content.classList.toggle("collapsed");
    header.classList.toggle("collapsed");

    if (sidebar.classList.contains("collapsed")) {
        localStorage.setItem("sidebarCollapsed", "true");
    } else {
        localStorage.setItem("sidebarCollapsed", "false");
    }
} 

function setActive(element) { 
    let items = document.querySelectorAll(".nav-item"); 
    items.forEach(i => i.classList.remove("active")); 
    element.classList.add("active"); 
}

function navigateTo(page) {
    window.location.href = page;
}

function isValidEmail(email) {
    const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    return emailRegex.test(email);
}

function loginFunc() {
    let email = $("#email").val().trim();
    let password = $("#password").val();

    if (!email || !password) {
        Swal.fire("Error", "Email and password are required", "error");
        return;
    }
    if (!isValidEmail(email)) {
        Swal.fire("Error", "Please enter a valid email address", "error");
        return;
    }

    $.post("../controllers/Controller.php", {
        login: true,
        email: email,
        password: password
    }, function(res) {
        if (res === "true") {
            redirectFunc(2);
        } else {
            Swal.fire("Error", "Invalid credentials", "error");
        }
    });
}

function registerFunc() {
    let fName = $("#fName").val().trim();
    let lName = $("#lName").val().trim();
    let email = $("#email").val().trim();
    let password = $("#password").val();
    let prc = $("#prc").val().trim();

    if (!fName || !lName || !email || !password || !prc) {
        Swal.fire("Error", "All fields required", "error");
        return;
    }
    if (!isValidEmail(email)) {
        Swal.fire("Error", "Please enter a valid email address", "error");
        return;
    }
    if (password.length < 8) {
        Swal.fire("Error", "Password must be at least 8 characters", "error");
        return;
    }
    if (!/^\d{7}$/.test(prc)) {
        Swal.fire("Error", "PRC must be 7 digits", "error");
        return;
    }

    $.post("../controllers/Controller.php", {
        checkEmail: true,
        email: email
    }, function(res) {

        if (res === "exists") {
            Swal.fire("Error", "Email already registered", "error");
        } else {
            $.post("../controllers/Controller.php", {
                register: true,
                fName: fName,
                lName: lName,
                email: email,
                password: password,
                prc: prc
            }, function(res) {
                if (res === "success") {
                    Swal.fire("Success", "Account created!", "success")
                    .then(() => redirectFunc(1));
                } else if (res === "invalid_email") {
                    Swal.fire("Error", "Please enter a valid email address", "error");
                } else if (res === "exists") {
                    Swal.fire("Error", "Email already registered", "error");
                } else {
                    Swal.fire("Error", "Registration failed", "error");
                }
            });
        }
    });
}


function togglePassword(element) {
    let pass = document.getElementById("password");

    if (pass.type === "password") {
        pass.type = "text";
        element.innerText = "visibility_off";
    } else {
        pass.type = "password";
        element.innerText = "visibility";
    }
}

function saveAppointment() {
            let appointmentID = document.getElementById('appointmentID').value;
            let petID = document.getElementById('petID').value;
            let ownerID = document.getElementById('ownerID').value;
            let appointmentDate = document.getElementById('appointmentDate').value;
            let appointmentTime = document.getElementById('appointmentTime').value;
            let status = document.getElementById('status').value;
            let reason = document.getElementById('reason').value;
            let notes = document.getElementById('notes').value;

            if (!petID || !ownerID || !appointmentDate || !appointmentTime) {
                Swal.fire('Error', 'Please fill in required fields', 'error');
                return;
            }
            
            let appDate = new Date(appointmentDate);
            let today = new Date();
            today.setHours(0, 0, 0, 0);
            if (appDate < today) {
                Swal.fire('Error', 'Appointment date cannot be in the past', 'error');
                return;
            }

            let action = appointmentID ? 'updateAppointment' : 'addAppointment';
            let data = {
                [action]: true,
                petID: petID,
                ownerID: ownerID,
                appointmentDate: appointmentDate,
                appointmentTime: appointmentTime,
                status: status,
                reason: reason,
                notes: notes
            };

            if (appointmentID) {
                data.appointmentID = appointmentID;
            }

            $.ajax({
                    url: '../controllers/Controller.php',
                    type: 'POST',
                    data: data,
                    success: function(response) {
                        if (response === 'success') {
                            Swal.fire('Success', 'Appointment saved successfully', 'success').then(() => {
                                location.reload();
                            });
                        } else if (response === 'past_date_not_allowed') {
                            Swal.fire('Error', 'Appointment date cannot be in the past', 'error');
                        } else if (response === 'invalid_date') {
                            Swal.fire('Error', 'Invalid date format', 'error');
                        } else {
                            Swal.fire('Error', 'Failed to save appointment', 'error');
                        }
                    }
                });
        }

        function deleteAppointment(id) {
            Swal.fire({
                title: 'Confirm Delete',
                text: 'Are you sure you want to delete this appointment?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '../controllers/Controller.php',
                        type: 'POST',
                        data: { deleteAppointment: true, appointmentID: id },
                        success: function(response) {
                            if (response === 'success') {
                                Swal.fire('Deleted', 'Appointment deleted successfully', 'success').then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Error', 'Failed to delete appointment', 'error');
                            }
                        }
                    });
                }
            });
        }

    function saveOwner() {
                let ownerID = document.getElementById('ownerID').value;
                let firstName = document.getElementById('firstName').value;
                let lastName = document.getElementById('lastName').value;
                let email = document.getElementById('ownerEmail').value;
                let phone = document.getElementById('phone').value;
                let address = document.getElementById('address').value;
                let city = document.getElementById('city').value;
                let province = document.getElementById('province').value;
                let postalCode = document.getElementById('postalCode').value;

                if (!firstName || !lastName || !email || !phone) {
                    Swal.fire('Error', 'Please fill in required fields', 'error');
                    return;
                }

                let action = ownerID ? 'updateOwner' : 'addOwner';
                let data = {
                    [action]: true,
                    firstName: firstName,
                    lastName: lastName,
                    email: email,
                    phone: phone,
                    address: address,
                    city: city,
                    province: province,
                    postalCode: postalCode
                };

                if (ownerID) {
                    data.ownerID = ownerID;
                }

                $.ajax({
                    url: '../controllers/Controller.php',
                    type: 'POST',
                    data: data,
                    success: function(response) {
                        if (response === 'success') {
                            Swal.fire('Success', 'Owner saved successfully', 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error', 'Failed to save owner', 'error');
                        }
                    }
                });
            }

        function deleteOwner(id) {
            Swal.fire({
                title: 'Confirm Delete',
                text: 'Are you sure you want to delete this owner?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '../controllers/Controller.php',
                        type: 'POST',
                        data: { deleteOwner: true, ownerID: id },
                        success: function(response) {
                            if (response === 'success') {
                                Swal.fire('Deleted', 'Owner deleted successfully', 'success').then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Error', 'Failed to delete owner', 'error');
                            }
                        }
                    });
                }
            });
        }

function savePet() {
            let petID = document.getElementById('petID').value;
            let ownerID = document.getElementById('ownerID').value;
            let petName = document.getElementById('petName').value;
            let species = document.getElementById('species').value;
            let breed = document.getElementById('breed').value;
            let dateOfBirth = document.getElementById('dateOfBirth').value;
            let color = document.getElementById('color').value;
            let microchipID = document.getElementById('microchipID').value;

            if (!ownerID || !petName || !species) {
                Swal.fire('Error', 'Please fill in required fields', 'error');
                return;
            }

            if (dateOfBirth && dateOfBirth.trim() !== '') {
                let birthDate = new Date(dateOfBirth);
                let today = new Date();
                today.setHours(0, 0, 0, 0);
                if (birthDate > today) {
                    Swal.fire('Error', 'Birthdate cannot be in the future', 'error');
                    return;
                }
            }

            let action = petID ? 'updatePet' : 'addPet';
            let data = {
                [action]: true,
                ownerID: ownerID,
                petName: petName,
                species: species,
                breed: breed,
                dateOfBirth: dateOfBirth,
                color: color,
                microchipID: microchipID
            };

            if (petID) {
                data.petID = petID;
            }

            $.ajax({
                    url: '../controllers/Controller.php',
                    type: 'POST',
                    data: data,
                    success: function(response) {
                        if (response === 'success') {
                            Swal.fire('Success', 'Pet saved successfully', 'success').then(() => {
                                location.reload();
                            });
                        } else if (response === 'future_date_not_allowed') {
                            Swal.fire('Error', 'Birthdate cannot be in the future', 'error');
                        } else if (response === 'invalid_date_format') {
                            Swal.fire('Error', 'Invalid date format', 'error');
                        } else {
                            Swal.fire('Error', 'Failed to save pet', 'error');
                        }
                    }
                });
        }

        function deletePet(id) {
            Swal.fire({
                title: 'Confirm Delete',
                text: 'Are you sure you want to delete this pet?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '../controllers/Controller.php',
                        type: 'POST',
                        data: { deletePet: true, petID: id },
                        success: function(response) {
                            if (response === 'success') {
                                Swal.fire('Deleted', 'Pet deleted successfully', 'success').then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Error', 'Failed to delete pet', 'error');
                            }
                        }
                    });
                }
            });
        }

function sendConfirmationEmail(appointmentID, ownerID, petName, appointmentDate, appointmentTime) {
    Swal.fire({
        title: 'Send Confirmation Email',
        text: 'Send appointment confirmation email to the owner?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, send email',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '../controllers/Controller.php',
                type: 'POST',
                data: {
                    sendConfirmationEmail: true,
                    appointmentID: appointmentID,
                    ownerID: ownerID
                },
                success: function(response) {
                    if (response === 'success') {
                        Swal.fire('Success', 'Confirmation email sent successfully', 'success');
                    } else if (response === 'appointment_not_found') {
                        Swal.fire('Error', 'Appointment not found', 'error');
                    } else if (response === 'owner_not_found') {
                        Swal.fire('Error', 'Owner not found', 'error');
                    } else if (response === 'email_failed') {
                        Swal.fire('Error', 'Failed to send email. Please check email configuration.', 'error');
                    } else {
                        Swal.fire('Error', 'An error occurred while sending email', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Failed to send email request', 'error');
                }
            });
        }
    });
}

$(document).ready(function () {

    M.AutoInit();

    if (localStorage.getItem("sidebarCollapsed") === "true") {
        document.getElementById("sidebar").classList.add("collapsed");
        document.getElementById("content").classList.add("collapsed");
        document.getElementById("header").classList.add("collapsed");
    }

    if ($('#myTable').length) {
        $('#myTable').DataTable({
            lengthChange: false,
            info: false,
            searching: false
        });
    }

    if ($('#ownersTable').length) {
        $('#ownersTable').DataTable({
            lengthChange: false,
            info: false
        });
    }

    if ($('#petsTable').length) {
        $('#petsTable').DataTable({
            lengthChange: false,
            info: false
        });
    }

    if ($('#appointmentsTable').length) {
        $('#appointmentsTable').DataTable({
            lengthChange: false,
            info: false
        });
    }

});