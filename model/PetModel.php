<?php
class PetModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createPet($ownerID, $petName, $species, $breed, $dateOfBirth, $color, $microchipID) {
        $query = "INSERT INTO tbl_pets
        (ownerID, petName, species, breed, dateOfBirth, color, microchipID, createdAt, updatedAt)
        VALUES (:ownerID, :petName, :species, :breed, :dob, :color, :microchip, NOW(), NOW())";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":ownerID", $ownerID);
        $stmt->bindParam(":petName", $petName);
        $stmt->bindParam(":species", $species);
        $stmt->bindParam(":breed", $breed);
        $stmt->bindParam(":dob", $dateOfBirth);
        $stmt->bindParam(":color", $color);
        $stmt->bindParam(":microchip", $microchipID);

        return $stmt->execute();
    }

    public function getPetById($petID) {
        $query = "SELECT * FROM tbl_pets WHERE petID = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $petID);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getPetsByOwner($ownerID) {
        $query = "SELECT * FROM tbl_pets WHERE ownerID = :ownerID ORDER BY petName";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":ownerID", $ownerID);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllPets() {
        $query = "SELECT p.*, CONCAT(o.firstName, ' ', o.lastName) as ownerName 
                  FROM tbl_pets p 
                  JOIN tbl_owners o ON p.ownerID = o.ownerID 
                  ORDER BY p.petName";
        return $this->conn->query($query);
    }

    public function getTotalPets() {
        $query = "SELECT COUNT(*) as total FROM tbl_pets";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function updatePet($petID, $ownerID, $petName, $species, $breed, $dateOfBirth, $color, $microchipID) {
        $query = "UPDATE tbl_pets 
        SET ownerID = :ownerID, petName = :petName, species = :species, breed = :breed, 
            dateOfBirth = :dob, color = :color, microchipID = :microchip, updatedAt = NOW()
        WHERE petID = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $petID);
        $stmt->bindParam(":ownerID", $ownerID);
        $stmt->bindParam(":petName", $petName);
        $stmt->bindParam(":species", $species);
        $stmt->bindParam(":breed", $breed);
        $stmt->bindParam(":dob", $dateOfBirth);
        $stmt->bindParam(":color", $color);
        $stmt->bindParam(":microchip", $microchipID);

        return $stmt->execute();
    }

    public function deletePet($petID) {
        $query = "DELETE FROM tbl_pets WHERE petID = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $petID);
        return $stmt->execute();
    }
}
?>