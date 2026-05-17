<?php
session_start();

require_once "../bl/UserManager.php";
require_once "../bl/OwnerManager.php";
require_once "../bl/PetManager.php";
require_once "../bl/AppointmentManager.php";

$usermanager = new UserManager();
$ownerManager = new OwnerManager();
$petManager = new PetManager();
$appointmentManager = new AppointmentManager();

if (isset($_POST["register"])) {

    $usermanager->registerUser(
        $_POST["fName"] ?? "",
        $_POST["lName"] ?? "",
        $_POST["email"] ?? "",
        $_POST["password"] ?? "",
        $_POST["prc"] ?? ""
    );

    exit;
}

if (isset($_POST["login"])) {

    $usermanager->loginUserFunc(
        $_POST["email"] ?? "",
        $_POST["password"] ?? ""
    );

    exit;
}

if (isset($_POST["checkEmail"])) {

    $email = trim($_POST["email"] ?? "");

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "invalid_email";
        exit;
    }

    echo $usermanager->checkEmail($email)
        ? "exists"
        : "ok";

    exit;
}

if (isset($_POST["addOwner"])) {

    $ownerManager->addOwner(
        $_POST["firstName"] ?? "",
        $_POST["lastName"] ?? "",
        $_POST["email"] ?? "",
        $_POST["phone"] ?? "",
        $_POST["address"] ?? "",
        $_POST["city"] ?? "",
        $_POST["province"] ?? "",
        $_POST["postalCode"] ?? ""
    );

    exit;
}

if (isset($_POST["updateOwner"])) {

    $ownerManager->updateOwner(
        $_POST["ownerID"] ?? "",
        $_POST["firstName"] ?? "",
        $_POST["lastName"] ?? "",
        $_POST["email"] ?? "",
        $_POST["phone"] ?? "",
        $_POST["address"] ?? "",
        $_POST["city"] ?? "",
        $_POST["province"] ?? "",
        $_POST["postalCode"] ?? ""
    );

    exit;
}

if (isset($_POST["deleteOwner"])) {

    $ownerManager->deleteOwner(
        $_POST["ownerID"] ?? ""
    );

    exit;
}

if (isset($_POST["addPet"])) {

    $petManager->addPet(
        $_POST["ownerID"] ?? "",
        $_POST["petName"] ?? "",
        $_POST["species"] ?? "",
        $_POST["breed"] ?? "",
        $_POST["dateOfBirth"] ?? "",
        $_POST["color"] ?? "",
        $_POST["microchipID"] ?? ""
    );

    exit;
}

if (isset($_POST["updatePet"])) {

    $petManager->updatePet(
        $_POST["petID"] ?? "",
        $_POST["ownerID"] ?? "",
        $_POST["petName"] ?? "",
        $_POST["species"] ?? "",
        $_POST["breed"] ?? "",
        $_POST["dateOfBirth"] ?? "",
        $_POST["color"] ?? "",
        $_POST["microchipID"] ?? ""
    );

    exit;
}

if (isset($_POST["deletePet"])) {

    $petManager->deletePet(
        $_POST["petID"] ?? ""
    );

    exit;
}

if (isset($_POST["addAppointment"])) {

    $appointmentManager->addAppointment(
        $_POST["petID"] ?? "",
        $_POST["ownerID"] ?? "",
        $_POST["appointmentDate"] ?? "",
        $_POST["appointmentTime"] ?? "",
        $_POST["status"] ?? "Scheduled",
        $_POST["reason"] ?? "",
        $_POST["notes"] ?? ""
    );

    exit;
}

if (isset($_POST["updateAppointment"])) {

    $appointmentManager->updateAppointment(
        $_POST["appointmentID"] ?? "",
        $_POST["petID"] ?? "",
        $_POST["ownerID"] ?? "",
        $_POST["appointmentDate"] ?? "",
        $_POST["appointmentTime"] ?? "",
        $_POST["status"] ?? "",
        $_POST["reason"] ?? "",
        $_POST["notes"] ?? ""
    );

    exit;
}

if (isset($_POST["deleteAppointment"])) {

    $appointmentManager->deleteAppointment(
        $_POST["appointmentID"] ?? ""
    );

    exit;
}

if (isset($_POST["sendConfirmationEmail"])) {

    $appointmentManager->sendConfirmationEmail(
        $_POST["appointmentID"] ?? "",
        $_POST["ownerID"] ?? ""
    );

    exit;
}
?>