<?php
require_once "../model/database.php";
require_once "../model/AppointmentModel.php";
require_once "../bl/OwnerManager.php";
require_once "../helper/sendEmail.php";

class AppointmentManager {
    private $appointmentModel;

    public function __construct() {
        $db = (new Database())->connectDB();
        $this->appointmentModel = new AppointmentModel($db);
    }

    public function addAppointment($petID, $ownerID, $appointmentDate, $appointmentTime, $status, $reason, $notes) {
        $reason = trim($reason);
        $notes = trim($notes);

        // Server-side validation
        if ($petID === "" || $ownerID === "" || $appointmentDate === "" || $appointmentTime === "" || $status === "") {
            echo "error";
            return;
        }

        if (!is_numeric($petID) || $petID <= 0 || !is_numeric($ownerID) || $ownerID <= 0) {
            echo "invalid_data";
            return;
        }

        // Validate date format
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $appointmentDate)) {
            echo "invalid_date";
            return;
        }

        // Validate time format
        if (!preg_match('/^\d{2}:\d{2}$/', $appointmentTime)) {
            echo "invalid_time";
            return;
        }

        // Validate appointment date
        $appDate = new DateTime($appointmentDate);
        $today = new DateTime();
        $today->setTime(0, 0, 0);
        if ($appDate < $today) {
            echo "past_date_not_allowed";
            return;
        }

        if ($this->appointmentModel->createAppointment($petID, $ownerID, $appointmentDate, $appointmentTime, $status, $reason, $notes)) {
            echo "success";
        } else {
            echo "error";
        }
    }

    public function updateAppointment($appointmentID, $petID, $ownerID, $appointmentDate, $appointmentTime, $status, $reason, $notes) {
        $reason = trim($reason);
        $notes = trim($notes);

        if ($petID === "" || $ownerID === "" || $appointmentDate === "" || $appointmentTime === "" || $status === "") {
            echo "error";
            return;
        }

        // Validate appointment date
        $appDate = new DateTime($appointmentDate);
        $today = new DateTime();
        $today->setTime(0, 0, 0);
        if ($appDate < $today) {
            echo "past_date_not_allowed";
            return;
        }

        if ($this->appointmentModel->updateAppointment($appointmentID, $petID, $ownerID, $appointmentDate, $appointmentTime, $status, $reason, $notes)) {
            echo "success";
        } else {
            echo "error";
        }
    }

    public function deleteAppointment($appointmentID) {
        if ($this->appointmentModel->deleteAppointment($appointmentID)) {
            echo "success";
        } else {
            echo "error";
        }
    }

    public function getAppointments() {
        return $this->appointmentModel->getAllAppointments()->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAppointment($appointmentID) {
        return $this->appointmentModel->getAppointmentById($appointmentID);
    }

    public function getAppointmentsByOwner($ownerID) {
        return $this->appointmentModel->getAppointmentsByOwner($ownerID);
    }

    public function getTotalAppointments() {
        return $this->appointmentModel->getTotalAppointments();
    }

    public function sendConfirmationEmail($appointmentID, $ownerID) {
        $appointment = $this->appointmentModel->getAppointmentById($appointmentID);
        
        if (!$appointment) {
            echo "appointment_not_found";
            return;
        }

        $ownerManager = new OwnerManager();
        $owner = $ownerManager->getOwner($ownerID);
        
        if (!$owner) {
            echo "owner_not_found";
            return;
        }

        $ownerName = $owner['firstName'] . ' ' . $owner['lastName'];
        $ownerEmail = $owner['email'];
        $subject = "Appointment Confirmation - VetHub";
        
        $body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #2196F3; color: white; padding: 20px; border-radius: 5px; }
                .content { padding: 20px; background-color: #f9f9f9; margin-top: 20px; border-radius: 5px; }
                .detail-row { margin: 10px 0; }
                .label { font-weight: bold; color: #2196F3; }
                .footer { margin-top: 20px; text-align: center; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Appointment Confirmation</h2>
                </div>
                <div class='content'>
                    <p>Dear $ownerName,</p>
                    <p>Your appointment has been scheduled successfully. Please find the details below:</p>
                    
                    <div class='detail-row'>
                        <span class='label'>Pet Name:</span> " . htmlspecialchars($appointment['petName']) . "
                    </div>
                    <div class='detail-row'>
                        <span class='label'>Appointment Date:</span> " . date('F d, Y', strtotime($appointment['appointmentDate'])) . "
                    </div>
                    <div class='detail-row'>
                        <span class='label'>Appointment Time:</span> " . htmlspecialchars($appointment['appointmentTime']) . "
                    </div>
                    <div class='detail-row'>
                        <span class='label'>Reason:</span> " . htmlspecialchars($appointment['reason']) . "
                    </div>
                    <div class='detail-row'>
                        <span class='label'>Status:</span> " . htmlspecialchars($appointment['status']) . "
                    </div>
                    
                    <p style='margin-top: 20px;'>If you need to reschedule or cancel, please contact us as soon as possible.</p>
                    <p>Thank you!</p>
                </div>
                <div class='footer'>
                    <p>This is an automated message from VetHub. Please do not reply to this email.</p>
                </div>
            </div>
        </body>
        </html>
        ";

        $result = sendEmail($ownerEmail, $ownerName, $subject, $body);
        
        if ($result === true) {
            echo "success";
        } else {
            echo "email_failed";
        }
    }
}
?>