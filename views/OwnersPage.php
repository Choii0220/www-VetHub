<?php 
session_start(); 
require_once "../bl/OwnerManager.php"; 

$ownermanager = new OwnerManager(); 
$owners = $ownermanager->getOwners(); 
$totalOwners = $ownermanager->getTotalOwners();

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
    <title>VetHub - Owners Management</title> 
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script> 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css"> 
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.material.min.css">
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.material.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"> 
    <link rel="stylesheet" href="../css/Owners.css">

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

                <li class="nav-item active" onclick="setActive(this)"> 
                    <i class="material-icons">people</i> 
                    <span>Owners</span> 
                </li> 

                <li class="nav-item" onclick="setActive(this); navigateTo('PetsPage.php')"> 
                    <i class="material-icons">pets</i> 
                    <span>Pets</span> 
                </li> 

                <li class="nav-item" onclick="setActive(this); navigateTo('AppointmentsPage.php')"> 
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
            <h1 class="header-title">Pet Owners</h1>
        </div>

        <div class="table-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3>
                    <i class="material-icons">people</i>
                    Pet Owners (Total: <?= $totalOwners ?>)
                </h3>
                <button class="btn waves-effect waves-light" onclick="openAddOwnerModal()">
                    <i class="material-icons left">add</i>Add Owner
                </button>
            </div>
            <table id="ownersTable" class="striped"> 
                <thead> 
                    <tr> 
                        <th>ID</th> 
                        <th>Name</th> 
                        <th>Email</th> 
                        <th>Phone</th> 
                        <th>City</th>
                        <th>Actions</th>
                    </tr> 
                </thead> 

                <tbody> 
                    <?php foreach ($owners as $owner): ?> 
                    <tr> 
                        <td><?= htmlspecialchars($owner["ownerID"]) ?></td> 
                        <td><?= htmlspecialchars($owner["firstName"] . " " . $owner["lastName"]) ?></td> 
                        <td><?= htmlspecialchars($owner["email"]) ?></td> 
                        <td><?= htmlspecialchars($owner["phone"]) ?></td> 
                        <td><?= htmlspecialchars($owner["city"]) ?></td>
                        <td>
                            <button class="btn-small waves-effect waves-light blue" onclick="editOwner(<?= $owner['ownerID'] ?>, '<?= htmlspecialchars($owner['firstName']) ?>', '<?= htmlspecialchars($owner['lastName']) ?>', '<?= htmlspecialchars($owner['email']) ?>', '<?= htmlspecialchars($owner['phone']) ?>', '<?= htmlspecialchars($owner['address']) ?>', '<?= htmlspecialchars($owner['city']) ?>', '<?= htmlspecialchars($owner['province']) ?>', '<?= htmlspecialchars($owner['postalCode']) ?>')">
                                <i class="material-icons">edit</i>
                            </button>
                            <button class="btn-small waves-effect waves-light red" onclick="deleteOwner(<?= $owner['ownerID'] ?>)">
                                <i class="material-icons">delete</i>
                            </button>
                        </td>
                    </tr> 
                    <?php endforeach; ?> 
                </tbody> 
            </table> 
        </div> 
    </main>

    <!-- Add/Edit Owner Modal -->
    <div id="ownerModal" class="modal">
        <div class="modal-content">
            <h4 id="modalTitle">Add New Owner</h4>
            <form id="ownerForm">
                <input type="hidden" id="ownerID">
                <div class="input-field">
                    <label for="firstName">First Name</label>
                    <input type="text" id="firstName" placeholder="First Name" required>
                </div>
                <div class="input-field">
                    <label for="lastName">Last Name</label>
                    <input type="text" id="lastName" placeholder="Last Name" required>
                </div>
                <div class="input-field">
                    <label for="ownerEmail">Email</label>
                    <input type="email" id="ownerEmail" placeholder="Email" required>
                </div>
                <div class="input-field">
                    <label for="phone">Phone</label>
                    <input type="tel" id="phone" placeholder="Phone" required>
                </div>
                <div class="input-field">
                    <label for="address">Address</label>
                    <input type="text" id="address" placeholder="Address">
                </div>
                <div class="input-field">
                    <label for="city">City</label>
                    <input type="text" id="city" placeholder="City">
                </div>
                <div class="input-field">
                    <label for="province">Province</label>
                    <input type="text" id="province" placeholder="Province">
                </div>
                <div class="input-field">
                    <label for="postalCode">Postal Code</label>
                    <input type="text" id="postalCode" placeholder="Postal Code">
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-close waves-effect waves-green btn-flat">Cancel</a>
            <button class="waves-effect waves-green btn" onclick="saveOwner()">Save</button>
        </div>
    </div>
    
    <script src="../scripts/Service.js"></script>
    <script> 
        function openAddOwnerModal() {
            document.getElementById('ownerForm').reset();
            document.getElementById('ownerID').value = '';
            document.getElementById('modalTitle').textContent = 'Add New Owner';
            var modal = document.getElementById('ownerModal');
            M.Modal.getInstance(modal).open();
        }

        function editOwner(id, fname, lname, email, phone, address, city, province, postalCode) {
            document.getElementById('ownerID').value = id;
            document.getElementById('firstName').value = fname;
            document.getElementById('lastName').value = lname;
            document.getElementById('ownerEmail').value = email;
            document.getElementById('phone').value = phone;
            document.getElementById('address').value = address;
            document.getElementById('city').value = city;
            document.getElementById('province').value = province;
            document.getElementById('postalCode').value = postalCode;
            document.getElementById('modalTitle').textContent = 'Edit Owner';
            M.updateTextFields();
            var modal = document.getElementById('ownerModal');
            M.Modal.getInstance(modal).open();
        }
    </script> 
</body> 
</html>