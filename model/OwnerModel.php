<?php
class OwnerModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createOwner($firstName, $lastName, $email, $phone, $address, $city, $province, $postalCode) {
        $query = "INSERT INTO tbl_owners
        (firstName, lastName, email, phone, address, city, province, postalCode, createdAt, updatedAt)
        VALUES (:fName, :lName, :email, :phone, :address, :city, :province, :postalCode, NOW(), NOW())";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":fName", $firstName);
        $stmt->bindParam(":lName", $lastName);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":phone", $phone);
        $stmt->bindParam(":address", $address);
        $stmt->bindParam(":city", $city);
        $stmt->bindParam(":province", $province);
        $stmt->bindParam(":postalCode", $postalCode);

        return $stmt->execute();
    }

    public function getOwnerById($ownerID) {
        $query = "SELECT * FROM tbl_owners WHERE ownerID = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $ownerID);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getOwnerByEmail($email) {
        $query = "SELECT * FROM tbl_owners WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function emailExists($email) {
        $query = "SELECT ownerID FROM tbl_owners WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function getAllOwners() {
        $query = "SELECT * FROM tbl_owners ORDER BY firstName, lastName";
        return $this->conn->query($query);
    }

    public function getTotalOwners() {
        $query = "SELECT COUNT(*) as total FROM tbl_owners";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function updateOwner($ownerID, $firstName, $lastName, $email, $phone, $address, $city, $province, $postalCode) {
        $query = "UPDATE tbl_owners 
        SET firstName = :fName, lastName = :lName, email = :email, phone = :phone, 
            address = :address, city = :city, province = :province, postalCode = :postalCode,
            updatedAt = NOW()
        WHERE ownerID = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $ownerID);
        $stmt->bindParam(":fName", $firstName);
        $stmt->bindParam(":lName", $lastName);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":phone", $phone);
        $stmt->bindParam(":address", $address);
        $stmt->bindParam(":city", $city);
        $stmt->bindParam(":province", $province);
        $stmt->bindParam(":postalCode", $postalCode);

        return $stmt->execute();
    }

    public function deleteOwner($ownerID) {
        $query = "DELETE FROM tbl_owners WHERE ownerID = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $ownerID);
        return $stmt->execute();
    }
}
?>