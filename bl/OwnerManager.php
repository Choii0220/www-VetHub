<?php
require_once "../model/database.php";
require_once "../model/OwnerModel.php";

class OwnerManager {
    private $ownerModel;

    public function __construct() {
        $db = (new Database())->connectDB();
        $this->ownerModel = new OwnerModel($db);
    }

    public function addOwner($firstName, $lastName, $email, $phone, $address, $city, $province, $postalCode) {
        $firstName = trim($firstName);
        $lastName = trim($lastName);
        $email = strtolower(trim($email));
        $phone = trim($phone);
        $address = trim($address);
        $city = trim($city);
        $province = trim($province);
        $postalCode = trim($postalCode);

        if ($firstName === "" || $lastName === "" || $email === "" || $phone === "") {
            echo "error";
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "invalid_email";
            return;
        }

        if ($this->ownerModel->emailExists($email)) {
            echo "exists";
            return;
        }

        if (!preg_match('/^[0-9\-\+\(\)\s]+$/', $phone)) {
            echo "invalid_phone";
            return;
        }

        if ($this->ownerModel->createOwner($firstName, $lastName, $email, $phone, $address, $city, $province, $postalCode)) {
            echo "success";
        } else {
            echo "error";
        }
    }

    public function updateOwner($ownerID, $firstName, $lastName, $email, $phone, $address, $city, $province, $postalCode) {
        $firstName = trim($firstName);
        $lastName = trim($lastName);
        $email = strtolower(trim($email));
        $phone = trim($phone);
        $address = trim($address);
        $city = trim($city);
        $province = trim($province);
        $postalCode = trim($postalCode);

        if ($firstName === "" || $lastName === "" || $email === "" || $phone === "") {
            echo "error";
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "invalid_email";
            return;
        }

        if ($this->ownerModel->updateOwner($ownerID, $firstName, $lastName, $email, $phone, $address, $city, $province, $postalCode)) {
            echo "success";
        } else {
            echo "error";
        }
    }

    public function deleteOwner($ownerID) {
        if ($this->ownerModel->deleteOwner($ownerID)) {
            echo "success";
        } else {
            echo "error";
        }
    }

    public function getOwners() {
        return $this->ownerModel->getAllOwners()->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOwner($ownerID) {
        return $this->ownerModel->getOwnerById($ownerID);
    }

    public function getTotalOwners() {
        return $this->ownerModel->getTotalOwners();
    }
}
?>