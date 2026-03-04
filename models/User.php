<?php
require_once __DIR__ . '/../config/db.php';

class User {

    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    
    public function register($fullname, $email, $password) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare(
            "INSERT INTO users (fullname, email, password, is_verified, created_at) VALUES (?, ?, ?, 0, NOW())"
        );
        $stmt->bind_param("sss", $fullname, $email, $hashed);
        if($stmt->execute()) {
            return $this->conn->insert_id;
        }
        return false;
    }

    
    
    public function storeToken($user_id, $token) {
        $stmt = $this->conn->prepare(
            "INSERT INTO email_verifications (user_id, token, created_at) VALUES (?, ?, NOW())"
        );
        $stmt->bind_param("is", $user_id, $token);
        return $stmt->execute();
    }

    public function verifyEmail($token) {
        $stmt = $this->conn->prepare(
            "SELECT user_id FROM email_verifications WHERE token=?"
        );
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        if($row = $result->fetch_assoc()) {
            $user_id = $row['user_id'];

           
            $update = $this->conn->prepare(
                "UPDATE users SET is_verified=1 WHERE id=?"
            );
            $update->bind_param("i", $user_id);
            $update->execute();

         
            $delete = $this->conn->prepare(
                "DELETE FROM email_verifications WHERE user_id=?"
            );
            $delete->bind_param("i", $user_id);
            $delete->execute();

            return true;
        }
        return false;
    }

    
    public function login($email) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM users WHERE email=? AND is_verified=1"
        );
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result();
    }

    
    public function saveOTP($user_id, $otp, $expiry) {
        
        $del = $this->conn->prepare("DELETE FROM otp_codes WHERE user_id=?");
        $del->bind_param("i", $user_id);
        $del->execute();

       
        $stmt = $this->conn->prepare(
            "INSERT INTO otp_codes (user_id, otp_code, expires_at) VALUES (?, ?, ?)"
        );
        $stmt->bind_param("iss", $user_id, $otp, $expiry);
        return $stmt->execute();
    }

    public function verifyOTP($user_id, $otpInput) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM otp_codes 
             WHERE user_id=? AND otp_code=? AND expires_at >= NOW() 
             ORDER BY id DESC LIMIT 1"
        );
        $stmt->bind_param("is", $user_id, $otpInput);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0) {
            
            $del = $this->conn->prepare(
                "DELETE FROM otp_codes WHERE user_id=? AND otp_code=?"
            );
            $del->bind_param("is", $user_id, $otpInput);
            $del->execute();
            return true;
        }
        return false;
    }
}
?>