<?php
class AppointmentModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createAppointment($petID, $ownerID, $appointmentDate, $appointmentTime, $status, $reason, $notes) {
        $query = "INSERT INTO tbl_appointments
        (petID, ownerID, appointmentDate, appointmentTime, status, reason, notes, createdAt, updatedAt)
        VALUES (:petID, :ownerID, :appDate, :appTime, :status, :reason, :notes, NOW(), NOW())";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":petID", $petID);
        $stmt->bindParam(":ownerID", $ownerID);
        $stmt->bindParam(":appDate", $appointmentDate);
        $stmt->bindParam(":appTime", $appointmentTime);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":reason", $reason);
        $stmt->bindParam(":notes", $notes);

        return $stmt->execute();
    }

    public function getAppointmentById($appointmentID) {
        $query = "SELECT a.*, p.petName, CONCAT(o.firstName, ' ', o.lastName) as ownerName
                  FROM tbl_appointments a
                  JOIN tbl_pets p ON a.petID = p.petID
                  JOIN tbl_owners o ON a.ownerID = o.ownerID
                  WHERE a.appointmentID = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $appointmentID);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAppointmentsByOwner($ownerID) {
        $query = "SELECT a.*, p.petName, CONCAT(o.firstName, ' ', o.lastName) as ownerName
                  FROM tbl_appointments a
                  JOIN tbl_pets p ON a.petID = p.petID
                  JOIN tbl_owners o ON a.ownerID = o.ownerID
                  WHERE a.ownerID = :ownerID
                  ORDER BY a.appointmentDate DESC, a.appointmentTime DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":ownerID", $ownerID);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllAppointments() {
        $query = "SELECT a.*, p.petName, CONCAT(o.firstName, ' ', o.lastName) as ownerName
                  FROM tbl_appointments a
                  JOIN tbl_pets p ON a.petID = p.petID
                  JOIN tbl_owners o ON a.ownerID = o.ownerID
                  ORDER BY a.appointmentDate DESC, a.appointmentTime DESC";
        return $this->conn->query($query);
    }

    public function getTotalAppointments() {
        $query = "SELECT COUNT(*) as total FROM tbl_appointments";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function updateAppointment($appointmentID, $petID, $ownerID, $appointmentDate, $appointmentTime, $status, $reason, $notes) {
        $query = "UPDATE tbl_appointments 
        SET petID = :petID, ownerID = :ownerID, appointmentDate = :appDate, appointmentTime = :appTime,
            status = :status, reason = :reason, notes = :notes, updatedAt = NOW()
        WHERE appointmentID = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $appointmentID);
        $stmt->bindParam(":petID", $petID);
        $stmt->bindParam(":ownerID", $ownerID);
        $stmt->bindParam(":appDate", $appointmentDate);
        $stmt->bindParam(":appTime", $appointmentTime);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":reason", $reason);
        $stmt->bindParam(":notes", $notes);

        return $stmt->execute();
    }

    public function deleteAppointment($appointmentID) {
        $query = "DELETE FROM tbl_appointments WHERE appointmentID = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $appointmentID);
        return $stmt->execute();
    }
}
?>