<?php
require_once "../model/database.php";
require_once "../model/userModel.php";

class UserManager {
    private $userModel;

    public function __construct() {
        $db = (new Database())->connectDB();
        $this->userModel = new UserModel($db);
    }

    public function registerUser($f, $l, $email, $p, $prc) {
        $email = strtolower(trim($email));
        $f = trim($f);
        $l = trim($l);
        $prc = trim($prc);

        // Server-side validation
        if ($f === "" || $l === "" || $email === "" || $p === "" || $prc === "") {
            echo "error";
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "invalid_email";
            return;
        }

        if (strlen($p) < 8) {
            echo "error";
            return;
        }

        if (!preg_match('/^\d{7}$/', $prc)) {
            echo "error";
            return;
        }

        if ($this->userModel->emailExists($email)) {
            echo "exists";
            return;
        }

        $hashedPassword = password_hash($p, PASSWORD_DEFAULT);

        if ($this->userModel->createUser($f, $l, $email, $hashedPassword, $prc)) {
            echo "success";
        } else {
            echo "error";
        }
    }


    public function loginUserFunc($email, $p) {
        $email = strtolower(trim($email));

        // Server-side validation
        if ($email === "" || $p === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "false";
            return;
        }

        $user = $this->userModel->getUserByEmail($email);

        if ($user && password_verify($p, $user["password"])) {
            $_SESSION["user"] = $user;
            echo "true";
        } else {
            echo "false";
        }
    }

    public function checkEmail($email) {
        $email = strtolower(trim($email));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        return $this->userModel->emailExists($email);
    }

    public function getUsers() {
        return $this->userModel->getAllUsers()->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalUsers() {
        return $this->userModel->getTotalUsers();
    }
}
?>
