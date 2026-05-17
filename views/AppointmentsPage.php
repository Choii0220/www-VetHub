<?php 
session_start(); 
require_once "../bl/AppointmentManager.php"; 
require_once "../bl/PetManager.php"; 
require_once "../bl/OwnerManager.php"; 

$appointmentmanager = new AppointmentManager(); 
$petmanager = new PetManager();
$ownermanager = new OwnerManager();
$appointments = $appointmentmanager->getAppointments(); 
$pets = $petmanager->getPets();
$owners = $ownermanager->getOwners();
$totalAppointments = $appointmentmanager->getTotalAppointments();

if (!isset($_SESSION["user"])) { 
    header("Location: LoginPage.php"); 
    exit; 
} 

$user = $_SESSION["user"]; 
?> 

<!DOCTYPE html> 
<html lang="en"> 
<head> 
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VetHub - Appointments Management</title> 
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script> 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css"> 
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.material.min.css">
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.material.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"> 
    <link rel="stylesheet" href="../css/Appointments.css">

</head> 

<body> 
    <!-- SIDEBAR --> 
    <div class="sidebar" id="sidebar"> 
        <div> 
            <div class="logo-box"> 
                <img src="../assets/logo.png" alt="VetHub Logo"> 
                <span class="logo-text">VetHub</span> 
            </div> 

            <ul class="nav-menu"> 
                <li class="nav-item" onclick="setActive(this); navigateTo('DashboardPage.php')"> 
                    <i class="material-icons">dashboard</i> 
                    <span>Dashboard</span> 
                </li> 

                <li class="nav-item" onclick="setActive(this); navigateTo('OwnersPage.php')"> 
                    <i class="material-icons">people</i> 
                    <span>Owners</span> 
                </li> 

                <li class="nav-item" onclick="setActive(this); navigateTo('PetsPage.php')"> 
                    <i class="material-icons">pets</i> 
                    <span>Pets</span> 
                </li> 

                <li class="nav-item active" onclick="setActive(this)"> 
                    <i class="material-icons">event</i> 
                    <span>Appointments</span> 
                </li> 
            </ul> 
        </div> 

        <div class="logout-box"> 
            <button class="logout-btn" onclick="logout()"> 
                <i class="material-icons">logout</i> 
                <span>Logout</span> 
            </button> 
        </div> 
    </div> 

    <!-- HEADER --> 
    <header id="header"> 
        <div class="header-left">
            <button class="menu-toggle" onclick="toggleSidebar()">
                <i class="material-icons">menu</i>
            </button>
        </div>
        <div class="header-user">
            <span><?= htmlspecialchars("Dr. " . $user["lastName"]) ?></span>
        </div>
    </header> 

    <!-- MAIN CONTENT --> 
    <main id="content"> 
        <div class="page-header">
            <h1 class="header-title">Appointments</h1>
        </div>

        <div class="table-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3>
                    <i class="material-icons">event</i>
                    Appointments (Total: <?= $totalAppointments ?>)
                </h3>
                <button class="btn waves-effect waves-light" onclick="openAddAppointmentModal()">
                    <i class="material-icons left">add</i>Schedule Appointment
                </button>
            </div>
            <table id="appointmentsTable" class="striped"> 
                <thead> 
                    <tr> 
                        <th>ID</th> 
                        <th>Pet Name</th> 
                        <th>Owner</th> 
                        <th>Date</th>
                        <th>Time</th>
                        <th>Status</th>
                        <th>Reason</th>
                        <th>Actions</th>
                    </tr> 
                </thead> 

                <tbody> 
                    <?php foreach ($appointments as $appointment): ?> 
                    <tr> 
                        <td><?= htmlspecialchars($appointment["appointmentID"]) ?></td> 
                        <td><?= htmlspecialchars($appointment["petName"]) ?></td> 
                        <td><?= htmlspecialchars($appointment["ownerName"]) ?></td> 
                        <td><?= htmlspecialchars($appointment["appointmentDate"]) ?></td>
                        <td><?= htmlspecialchars($appointment["appointmentTime"]) ?></td>
                        <td>
                            <span class="badge <?php 
                                switch($appointment["status"]) {
                                    case "Scheduled": echo "orange"; break;
                                    case "Completed": echo "green"; break;
                                    case "Cancelled": echo "red"; break;
                                    default: echo "blue";
                                }
                            ?>"><?= htmlspecialchars($appointment["status"]) ?></span>
                        </td>
                        <td><?= htmlspecialchars(substr($appointment["reason"], 0, 30)) ?></td>
                        <td>
                            <button class="btn-small waves-effect waves-light blue" onclick="editAppointment(<?= $appointment['appointmentID'] ?>, <?= $appointment['petID'] ?>, <?= $appointment['ownerID'] ?>, '<?= htmlspecialchars($appointment['appointmentDate']) ?>', '<?= htmlspecialchars($appointment['appointmentTime']) ?>', '<?= htmlspecialchars($appointment['status']) ?>', '<?= htmlspecialchars($appointment['reason']) ?>', '<?= htmlspecialchars(addslashes($appointment['notes'])) ?>')">
                                <i class="material-icons">edit</i>
                            </button>
                            <button class="btn-small waves-effect waves-light green" onclick="sendConfirmationEmail(<?= $appointment['appointmentID'] ?>, <?= $appointment['ownerID'] ?>, '<?= htmlspecialchars($appointment['petName']) ?>', '<?= htmlspecialchars($appointment['appointmentDate']) ?>', '<?= htmlspecialchars($appointment['appointmentTime']) ?>')">
                                <i class="material-icons">mail</i>
                            </button>
                            <button class="btn-small waves-effect waves-light red" onclick="deleteAppointment(<?= $appointment['appointmentID'] ?>)">
                                <i class="material-icons">delete</i>
                            </button>
                        </td>
                    </tr> 
                    <?php endforeach; ?> 
                </tbody> 
            </table> 
        </div> 
    </main>

    <!-- Add/Edit Appointment Modal -->
    <div id="appointmentModal" class="modal" style="max-height: 90vh; overflow-y: auto;">
        <div class="modal-content">
            <h4 id="modalTitle">Schedule New Appointment</h4>
            <form id="appointmentForm">
                <input type="hidden" id="appointmentID">
                <div class="input-field">
                    <label for="petID">Pet</label>
                    <select id="petID" required>
                        <option value="">Select Pet</option>
                        <?php foreach ($pets as $pet): ?>
                        <option value="<?= $pet['petID'] ?>"><?= htmlspecialchars($pet['petName'] . " (" . $pet['ownerName'] . ")") ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="input-field">
                    <label for="ownerID">Owner</label>
                    <select id="ownerID" required>
                        <option value="">Select Owner</option>
                        <?php foreach ($owners as $owner): ?>
                        <option value="<?= $owner['ownerID'] ?>"><?= htmlspecialchars($owner['firstName'] . " " . $owner['lastName']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="input-field">
                    <label for="appointmentDate">Appointment Date</label>
                    <input type="date" id="appointmentDate" min="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="input-field">
                    <label for="appointmentTime">Appointment Time</label>
                    <input type="time" id="appointmentTime" required>
                </div>
                <div class="input-field">
                    <label for="status">Status</label>
                    <select id="status" required>
                        <option value="Scheduled">Scheduled</option>
                        <option value="Completed">Completed</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="input-field">
                    <label for="reason">Reason</label>
                    <input type="text" id="reason" placeholder="Reason for appointment">
                </div>
                <div class="input-field">
                    <label for="notes">Notes</label>
                    <textarea id="notes" class="materialize-textarea" placeholder="Notes"></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-close waves-effect waves-green btn-flat">Cancel</a>
            <button class="waves-effect waves-green btn" onclick="saveAppointment()">Save</button>
        </div>
    </div>
    
    <script src="../scripts/Service.js"></script>
    <script> 
        function openAddAppointmentModal() {
            document.getElementById('appointmentForm').reset();
            document.getElementById('appointmentID').value = '';
            document.getElementById('modalTitle').textContent = 'Schedule New Appointment';
            
            M.FormSelect.getInstance(document.getElementById('petID')).destroy();
            M.FormSelect.getInstance(document.getElementById('ownerID')).destroy();
            M.FormSelect.getInstance(document.getElementById('status')).destroy();
            
            document.getElementById('petID').innerHTML = '<option value="">Select Pet</option><?php foreach ($pets as $pet): ?><option value="<?= $pet['petID'] ?>"><?= htmlspecialchars($pet['petName'] . " (" . $pet['ownerName'] . ")") ?></option><?php endforeach; ?>';
            document.getElementById('ownerID').innerHTML = '<option value="">Select Owner</option><?php foreach ($owners as $owner): ?><option value="<?= $owner['ownerID'] ?>"><?= htmlspecialchars($owner['firstName'] . " " . $owner['lastName']) ?></option><?php endforeach; ?>';
            
            M.FormSelect.init(document.getElementById('petID'));
            M.FormSelect.init(document.getElementById('ownerID'));
            M.FormSelect.init(document.getElementById('status'));
            
            var modal = document.getElementById('appointmentModal');
            M.Modal.getInstance(modal).open();
        }

        function editAppointment(id, petID, ownerID, date, time, status, reason, notes) {
            document.getElementById('appointmentID').value = id;
            document.getElementById('appointmentDate').value = date;
            document.getElementById('appointmentTime').value = time;
            document.getElementById('reason').value = reason;
            document.getElementById('notes').value = notes;
            document.getElementById('modalTitle').textContent = 'Edit Appointment';
            
            let petSelect = document.getElementById('petID');
            let ownerSelect = document.getElementById('ownerID');
            let statusSelect = document.getElementById('status');
            
            petSelect.value = petID;
            ownerSelect.value = ownerID;
            statusSelect.value = status;
            
            M.FormSelect.getInstance(petSelect).destroy();
            M.FormSelect.getInstance(ownerSelect).destroy();
            M.FormSelect.getInstance(statusSelect).destroy();
            
            M.FormSelect.init(petSelect);
            M.FormSelect.init(ownerSelect);
            M.FormSelect.init(statusSelect);
            
            M.updateTextFields();
            var modal = document.getElementById('appointmentModal');
            M.Modal.getInstance(modal).open();
        }
    </script> 
</body> 
</html>