<?php 
session_start(); 
require_once "../bl/PetManager.php"; 
require_once "../bl/OwnerManager.php"; 

$petmanager = new PetManager(); 
$ownermanager = new OwnerManager();
$pets = $petmanager->getPets(); 
$owners = $ownermanager->getOwners();
$totalPets = $petmanager->getTotalPets();

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
    <title>VetHub - Pets Management</title> 
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script> 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css"> 
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.material.min.css">
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.material.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"> 
    <link rel="stylesheet" href="../css/Pets.css">

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

                <li class="nav-item active" onclick="setActive(this)"> 
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
            <h1 class="header-title">Pets</h1>
        </div>

        <div class="table-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3>
                    <i class="material-icons">pets</i>
                    Patient Pets (Total: <?= $totalPets ?>)
                </h3>
                <button class="btn waves-effect waves-light" onclick="openAddPetModal()">
                    <i class="material-icons left">add</i>Add Pet
                </button>
            </div>
            <table id="petsTable" class="striped"> 
                <thead> 
                    <tr> 
                        <th>ID</th> 
                        <th>Pet Name</th> 
                        <th>Species</th> 
                        <th>Breed</th>
                        <th>Owner</th>
                        <th>Date of Birth</th>
                        <th>Actions</th>
                    </tr> 
                </thead> 

                <tbody> 
                    <?php foreach ($pets as $pet): ?> 
                    <tr> 
                        <td><?= htmlspecialchars($pet["petID"]) ?></td> 
                        <td><?= htmlspecialchars($pet["petName"]) ?></td> 
                        <td><?= htmlspecialchars($pet["species"]) ?></td> 
                        <td><?= htmlspecialchars($pet["breed"]) ?></td>
                        <td><?= htmlspecialchars($pet["ownerName"]) ?></td>
                        <td><?= htmlspecialchars($pet["dateOfBirth"]) ?></td>
                        <td>
                            <button class="btn-small waves-effect waves-light blue" onclick="editPet(<?= $pet['petID'] ?>, <?= $pet['ownerID'] ?>, '<?= htmlspecialchars($pet['petName']) ?>', '<?= htmlspecialchars($pet['species']) ?>', '<?= htmlspecialchars($pet['breed']) ?>', '<?= htmlspecialchars($pet['dateOfBirth']) ?>', '<?= htmlspecialchars($pet['color']) ?>', '<?= htmlspecialchars($pet['microchipID']) ?>')">
                                <i class="material-icons">edit</i>
                            </button>
                            <button class="btn-small waves-effect waves-light red" onclick="deletePet(<?= $pet['petID'] ?>)">
                                <i class="material-icons">delete</i>
                            </button>
                        </td>
                    </tr> 
                    <?php endforeach; ?> 
                </tbody> 
            </table> 
        </div> 
    </main>

    <!-- Add/Edit Pet Modal -->
    <div id="petModal" class="modal">
        <div class="modal-content">
            <h4 id="modalTitle">Add New Pet</h4>
            <form id="petForm">
                <input type="hidden" id="petID">
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
                    <label for="petName">Pet Name</label>
                    <input type="text" id="petName" placeholder="Pet Name" required>
                </div>
                <div class="input-field">
                    <label for="species">Species</label>
                    <input type="text" id="species" placeholder="Species (Dog, Cat, etc.)" required>
                </div>
                <div class="input-field">
                    <label for="breed">Breed</label>
                    <input type="text" id="breed" placeholder="Breed">
                </div>
                <div class="input-field">
                    <label for="dateOfBirth">Date of Birth</label>
                    <input type="date" id="dateOfBirth" max="<?= date('Y-m-d') ?>">
                </div>
                <div class="input-field">
                    <label for="color">Color/Markings</label>
                    <input type="text" id="color" placeholder="Color/Markings">
                </div>
                <div class="input-field">
                    <label for="microchipID">Microchip ID</label>
                    <input type="text" id="microchipID" placeholder="Microchip ID (N/A if none)">
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-close waves-effect waves-green btn-flat">Cancel</a>
            <button class="waves-effect waves-green btn" onclick="savePet()">Save</button>
        </div>
    </div>
    
    <script src="../scripts/Service.js"></script>
    <script> 
        function openAddPetModal() {
            document.getElementById('petForm').reset();
            document.getElementById('petID').value = '';
            document.getElementById('modalTitle').textContent = 'Add New Pet';
            M.FormSelect.getInstance(document.getElementById('ownerID')).destroy();
            document.getElementById('ownerID').innerHTML = '<option value="">Select Owner</option><?php foreach ($owners as $owner): ?><option value="<?= $owner['ownerID'] ?>"><?= htmlspecialchars($owner['firstName'] . " " . $owner['lastName']) ?></option><?php endforeach; ?>';
            M.FormSelect.init(document.getElementById('ownerID'));
            var modal = document.getElementById('petModal');
            M.Modal.getInstance(modal).open();
        }

        function editPet(id, ownerID, name, species, breed, dob, color, microchip) {
            document.getElementById('petID').value = id;
            document.getElementById('petName').value = name;
            document.getElementById('species').value = species;
            document.getElementById('breed').value = breed;
            document.getElementById('dateOfBirth').value = dob;
            document.getElementById('color').value = color;
            document.getElementById('microchipID').value = microchip;
            document.getElementById('modalTitle').textContent = 'Edit Pet';
            
            let selectElem = document.getElementById('ownerID');
            selectElem.value = ownerID;
            M.FormSelect.getInstance(selectElem).destroy();
            M.FormSelect.init(selectElem);
            
            M.updateTextFields();
            var modal = document.getElementById('petModal');
            M.Modal.getInstance(modal).open();
        }
    </script> 
</body> 
</html>