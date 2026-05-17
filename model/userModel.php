<?php
class UserModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createUser($fName, $lName, $email, $password, $prc) {
        $query = "INSERT INTO tbl_users
        (firstName, lastName, email, password, prcNumber, createdAt, updatedAt)
        VALUES (:f, :l, :e, :p, :prc, NOW(), NOW())";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":f", $fName);
        $stmt->bindParam(":l", $lName);
        $stmt->bindParam(":e", $email);
        $stmt->bindParam(":p", $password);
        $stmt->bindParam(":prc", $prc);

        return $stmt->execute();
    }

    public function getUserByEmail($email) {
        $query = "SELECT * FROM tbl_users WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function emailExists($email) {
        $query = "SELECT userID FROM tbl_users WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function getAllUsers() {
        $query = "SELECT * FROM tbl_users";
        return $this->conn->query($query);
    }

    public function getTotalUsers() {
        $query = "SELECT COUNT(*) as total FROM tbl_users";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
}
?>
