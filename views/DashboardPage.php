<?php
session_start();
require_once "../bl/UserManager.php";
require_once "../bl/OwnerManager.php";
require_once "../bl/PetManager.php";
require_once "../bl/AppointmentManager.php";

$usermanager = new UserManager();
$ownermanager = new OwnerManager();
$petmanager = new PetManager();
$appointmentmanager = new AppointmentManager();

$users = $usermanager->getUsers();
$totalUsers = $usermanager->getTotalUsers();
$totalOwners = $ownermanager->getTotalOwners();
$totalPets = $petmanager->getTotalPets();
$totalAppointments = $appointmentmanager->getTotalAppointments();

$allAppointments = $appointmentmanager->getAppointments();
$statusCounts = ['Scheduled' => 0, 'Completed' => 0, 'Cancelled' => 0];
$appointmentsByDate = [];

foreach ($allAppointments as $apt) {
    $status = ucfirst(strtolower($apt['status']));
    if (isset($statusCounts[$status])) {
        $statusCounts[$status]++;
    }
    $date = $apt['appointmentDate'];
    if (!isset($appointmentsByDate[$date])) {
        $appointmentsByDate[$date] = 0;
    }
    $appointmentsByDate[$date]++;
}

// Sort appointments by date
ksort($appointmentsByDate);

// Get pet species for pie chart
$allPets = $petmanager->getPets();
$speciesCounts = [];

foreach ($allPets as $pet) {

    $species = !empty($pet['species'])
        ? strtolower(trim($pet['species']))
        : 'unknown';

    if (!isset($speciesCounts[$species])) {
        $speciesCounts[$species] = 0;
    }

    $speciesCounts[$species]++;
}

// Convert data to JSON for charts
$statusChartData = json_encode($statusCounts);
$appointmentChartLabels = json_encode(array_keys($appointmentsByDate));
$appointmentChartData = json_encode(array_values($appointmentsByDate));
$speciesChartLabels = json_encode(array_keys($speciesCounts));
$speciesChartData = json_encode(array_values($speciesCounts));

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
    <title>VetHub Dashboard</title> 
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script> 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.material.min.css">
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.material.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
    <link rel="stylesheet" href="../css/Dashboard.css">
    <link rel="stylesheet" href="../css/Charts.css">

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
                <li class="nav-item active" onclick="setActive(this)"> 
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
            <span><?= htmlspecialchars("Dr. " . " " . $user["lastName"]) ?></span>
        </div>
    </header> 

    <!-- MAIN CONTENT --> 
    <main id="content"> 
        <div class="page-header">
            <h1 class="header-title">Dashboard</h1>
        </div>

        <div class="welcome-card"> 
            <h3>Welcome, <?= htmlspecialchars($user["firstName"]) ?>!</h3>
            <p style="color: var(--text-light); margin: 0;">Manage your veterinary practice efficiently</p>
            <div class="user-info">
                <div class="info-item">
                    <label>Full Name</label>
                    <p><?= htmlspecialchars($user["firstName"] . " " . $user["lastName"]) ?></p>
                </div>
                <div class="info-item">
                    <label>Email</label>
                    <p><?= htmlspecialchars($user["email"]) ?></p>
                </div>
                <div class="info-item">
                    <label>PRC License</label>
                    <p><?= htmlspecialchars($user["prcNumber"]) ?></p>
                </div>
            </div>
        </div> 

        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon active-icon">
                    <i class="material-icons">check_circle</i>
                </div>
                <div class="stat-content">
                    <p class="stat-label">Practitioners</p>
                    <h3 class="stat-value"><?= $totalUsers ?></h3>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon appointments-icon">
                    <i class="material-icons">event</i>
                </div>
                <div class="stat-content">
                    <p class="stat-label">Appointments</p>
                    <h3 class="stat-value"><?= $totalAppointments ?></h3>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon pets-icon">
                    <i class="material-icons">pets</i>
                </div>
                <div class="stat-content">
                    <p class="stat-label">Active Patients</p>
                    <h3 class="stat-value"><?= $totalPets ?></h3>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon owners-icon">
                    <i class="material-icons">people</i>
                </div>
                <div class="stat-content">
                    <p class="stat-label">Pet Owners</p>
                    <h3 class="stat-value"><?= $totalOwners ?></h3>
                </div>
            </div>
        </div>
        
        <!-- CHARTS SECTION -->
        <div class="charts-container">
            <div class="chart-card">
                <h3>
                    <i class="material-icons">pie_chart</i>
                    Appointment Status Distribution
                </h3>
                <div class="chart-wrapper">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>

            <div class="chart-card">
                <h3>
                    <i class="material-icons">timeline</i>
                    Appointments Over Time
                </h3>
                <div class="chart-wrapper">
                    <canvas id="appointmentLineChart"></canvas>
                </div>
            </div>

            <div class="chart-card">
                <h3>
                    <i class="material-icons">pets</i>
                    Pet Species Distribution
                </h3>
                <div class="chart-wrapper">
                    <canvas id="speciesChart"></canvas>
                </div>
            </div>
        </div>
        
        <div class="table-card">
            <h3>
                <i class="material-icons">people</i>
                Registered Users
            </h3>
            <table id="myTable" class="striped"> 
                <thead> 
                    <tr> 
                        <th>ID</th> 
                        <th>Name</th> 
                        <th>Email</th> 
                        <th>PRC License</th> 
                    </tr> 
                    
                </thead> 

                <tbody> 
                    <?php foreach ($users as $u): ?> 
                    <tr> 
                        <td><?= htmlspecialchars($u["userID"]) ?></td> 
                        <td><?= htmlspecialchars($u["firstName"] . " " . $u["lastName"]) ?></td> 
                        <td><?= htmlspecialchars($u["email"]) ?></td> 
                        <td><?= htmlspecialchars($u["prcNumber"]) ?></td> 
                    </tr> 
                    <?php endforeach; ?> 
                </tbody> 
            </table> 
        </div> 
    </main>
    
    <script src="../scripts/Service.js"></script>
    <script>
        // Status Chart (Doughnut)
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        const statusData = <?php echo $statusChartData; ?>;
        
        const statusChart = new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(statusData),
                datasets: [{
                    data: Object.values(statusData),
                    backgroundColor: [
                        '#4CAF50',  
                        '#2196F3',
                        '#f44336' 
                    ],
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            font: {
                                size: 14
                            }
                        }
                    }
                }
            }
        });

        // Line Chart (Appointments Over Time)
        const lineCtx = document.getElementById('appointmentLineChart').getContext('2d');
        const appointmentLabels = <?php echo $appointmentChartLabels; ?>;
        const appointmentData = <?php echo $appointmentChartData; ?>;
        
        const appointmentLineChart = new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: appointmentLabels,
                datasets: [{
                    label: 'Appointments',
                    data: appointmentData,
                    borderColor: '#2196F3',
                    backgroundColor: 'rgba(33, 150, 243, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#2196F3',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        labels: {
                            font: {
                                size: 14
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Pie Chart (Pet Species - Dynamic)
        const speciesCtx = document.getElementById('speciesChart').getContext('2d');
        const speciesLabels = <?php echo $speciesChartLabels; ?>;
        const speciesData = <?php echo $speciesChartData; ?>;
        
        // color random
        const generateColors = (count) => {
            const colors = [];
            const hues = [];
            for (let i = 0; i < count; i++) {
                hues.push((i * 360 / count) % 360);
            }
            hues.forEach(hue => {
                colors.push(`hsl(${hue}, 70%, 50%)`);
            });
            return colors;
        };
        
        const speciesChart = new Chart(speciesCtx, {
            type: 'pie',
            data: {
                labels: speciesLabels,
                datasets: [{
                    data: speciesData,
                    backgroundColor: generateColors(speciesLabels.length),
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            font: {
                                size: 14
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>