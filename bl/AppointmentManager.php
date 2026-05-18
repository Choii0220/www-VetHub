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
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body { 
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
                    color: #2c3e50; 
                    background-color: #f5f5f5;
                    line-height: 1.6;
                }
                .container { 
                    max-width: 600px; 
                    margin: 0 auto; 
                    padding: 20px;
                    background-color: white;
                    border-radius: 8px;
                    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                }
                .header { 
                    background: linear-gradient(135deg, #7A8F6B 0%, #6b7e5e 100%);
                    color: white; 
                    padding: 30px 20px;
                    border-radius: 8px 8px 0 0;
                    text-align: center;
                    margin: -20px -20px 30px -20px;
                }
                .header h2 { 
                    font-size: 28px;
                    font-weight: 600;
                    letter-spacing: 0.5px;
                }
                .greeting {
                    margin-top: 20px;
                    font-size: 16px;
                    margin-bottom: 15px;
                    color: #2c3e50;
                }
                .confirmation-message {
                    font-size: 15px;
                    color: #555;
                    margin-bottom: 25px;
                    line-height: 1.7;
                }
                .details-section {
                    background-color: #f9fafb;
                    padding: 20px;
                    border-radius: 6px;
                    margin: 20px 0;
                    border-left: 4px solid #7A8F6B;
                }
                .detail-row { 
                    margin: 12px 0;
                    display: flex;
                    align-items: baseline;
                }
                .detail-row:last-child {
                    margin-bottom: 0;
                }
                .label { 
                    font-weight: 600;
                    color: #7A8F6B; 
                    min-width: 140px;
                    font-size: 14px;
                }
                .value {
                    color: #2c3e50;
                    font-size: 14px;
                }
                .closing-message {
                    margin-top: 25px;
                    font-size: 14px;
                    color: #555;
                    line-height: 1.7;
                }
                .footer { 
                    margin-top: 30px; 
                    padding-top: 20px;
                    border-top: 1px solid #e1e8ed;
                    text-align: center; 
                    font-size: 12px; 
                    color: #999; 
                }
                .footer p {
                    margin: 0;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Appointment Confirmed</h2>
                </div>

                <p class='greeting'>Dear $ownerName,</p>
                
                <p class='confirmation-message'>
                    Your appointment has been scheduled successfully. Here are your appointment details:
                </p>
                
                <div class='details-section'>
                    <div class='detail-row'>
                        <span class='label'>Pet Name:</span>
                        <span class='value'>" . htmlspecialchars($appointment['petName']) . "</span>
                    </div>
                    <div class='detail-row'>
                        <span class='label'>Appointment Date:</span>
                        <span class='value'>" . date('F d, Y', strtotime($appointment['appointmentDate'])) . "</span>
                    </div>
                    <div class='detail-row'>
                        <span class='label'>Appointment Time:</span>
                        <span class='value'>" . htmlspecialchars($appointment['appointmentTime']) . "</span>
                    </div>
                    <div class='detail-row'>
                        <span class='label'>Reason:</span>
                        <span class='value'>" . htmlspecialchars($appointment['reason']) . "</span>
                    </div>
                    <div class='detail-row'>
                        <span class='label'>Status:</span>
                        <span class='value'>" . htmlspecialchars($appointment['status']) . "</span>
                    </div>
                </div>
                
                <div class='closing-message'>
                    <p>If you need to reschedule or cancel your appointment, please contact us as soon as possible.</p>
                    <p style='margin-top: 15px;'>Thank you!</p>
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