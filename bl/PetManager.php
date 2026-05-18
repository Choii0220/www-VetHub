<?php
require_once "../model/database.php";
require_once "../model/PetModel.php";

class PetManager {
    private $petModel;

    public function __construct() {
        $db = (new Database())->connectDB();
        $this->petModel = new PetModel($db);
    }

    public function addPet($ownerID, $petName, $species, $breed, $dateOfBirth, $color, $microchipID) {
        $petName = trim($petName);
        $species = trim($species);
        $breed = trim($breed);
        $color = trim($color);
        $microchipID = trim($microchipID);

        if ($ownerID === "" || $petName === "" || $species === "") {
            echo "error";
            return;
        }

        if (!is_numeric($ownerID) || $ownerID <= 0) {
            echo "invalid_owner";
            return;
        }

        // Validate birthdate
        if ($dateOfBirth !== "" && !empty($dateOfBirth)) {
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateOfBirth)) {
                echo "invalid_date_format";
                return;
            }
            $birthDate = new DateTime($dateOfBirth);
            $today = new DateTime();
            if ($birthDate > $today) {
                echo "future_date_not_allowed";
                return;
            }
        }

        if ($this->petModel->createPet($ownerID, $petName, $species, $breed, $dateOfBirth, $color, $microchipID)) {
            echo "success";
        } else {
            echo "error";
        }
    }

    public function updatePet($petID, $ownerID, $petName, $species, $breed, $dateOfBirth, $color, $microchipID) {
        $petName = trim($petName);
        $species = trim($species);
        $breed = trim($breed);
        $color = trim($color);
        $microchipID = trim($microchipID);

        if ($petName === "" || $species === "") {
            echo "error";
            return;
        }

        // Validate birthdate
        if ($dateOfBirth !== "" && !empty($dateOfBirth)) {
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateOfBirth)) {
                echo "invalid_date_format";
                return;
            }
            $birthDate = new DateTime($dateOfBirth);
            $today = new DateTime();
            if ($birthDate > $today) {
                echo "future_date_not_allowed";
                return;
            }
        }

        if ($this->petModel->updatePet($petID, $ownerID, $petName, $species, $breed, $dateOfBirth, $color, $microchipID)) {
            echo "success";
        } else {
            echo "error";
        }
    }

    public function deletePet($petID) {
        if ($this->petModel->deletePet($petID)) {
            echo "success";
        } else {
            echo "error";
        }
    }

    public function getPets() {
        return $this->petModel->getAllPets()->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPet($petID) {
        return $this->petModel->getPetById($petID);
    }

    public function getPetsByOwner($ownerID) {
        return $this->petModel->getPetsByOwner($ownerID);
    }

    public function getTotalPets() {
        return $this->petModel->getTotalPets();
    }
}
?>