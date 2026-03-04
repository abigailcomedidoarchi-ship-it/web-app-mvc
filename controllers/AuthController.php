<?php

require_once __DIR__ . '/../models/User.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class AuthController {

    public $userModel;

    public function __construct() {
        $this->userModel = new User();
        date_default_timezone_set('Asia/Manila'); 
        if(session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // ---------------------
    // Reusable Mailer Setup
    // ---------------------
    private function configureMailer() {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'abigailcomedidoarchi@gmail.com';
        $mail->Password   = 'zjcslfvitzhqwaoy'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->setFrom('abigailcomedidoarchi@gmail.com', 'No-Reply | WebApp');
        $mail->isHTML(true);
        return $mail;
    }

    // ---------------------
    // Dynamic Verification Link
    // ---------------------
    private function generateVerificationLink($token) {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'];
        $basePath = rtrim(dirname($_SERVER['PHP_SELF']), '/\\'); 
        return "$protocol://$host$basePath/index.php?action=verify&token=$token";
    }

    // ---------------------
    // Send Verification Email
    // ---------------------
    private function sendVerificationEmail($email, $token) {
        try {
            $mail = $this->configureMailer();
            $verificationLink = $this->generateVerificationLink($token);

            $mail->addAddress($email);
            $mail->Subject = 'Verify Your Email';
            $mail->Body = "
                <h2>Email Verification</h2>
                <p>Click the button below to verify your email:</p>
                <a href='$verificationLink'
                   style='padding:10px 20px;background:#6a5acd;color:white;text-decoration:none;border-radius:5px;'>
                   Verify Email
                </a>
            ";
            $mail->send();
            return true;
        } catch (Exception $e) {
            echo "Mailer Error: " . $mail->ErrorInfo;
            return false;
        }
    }

    // ---------------------
    // Send OTP Email
    // ---------------------
    private function sendOTPEmail($email, $otp) {
        try {
            $mail = $this->configureMailer();
            $mail->addAddress($email);
            $mail->Subject = 'Your OTP Code';
            $mail->Body = "
                <h2>Your OTP Code</h2>
                <h1 style='color:#6a5acd;'>$otp</h1>
                <p>This code expires in 5 minutes.</p>
            ";
            $mail->send();
            return true;
        } catch (Exception $e) {
            echo "Mailer Error: " . $mail->ErrorInfo;
            return false;
        }
    }

    // ---------------------
    // Register
    // ---------------------
    public function register() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Trim inputs
            $fullname = trim($_POST['fullname'] ?? '');
            $email    = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            // Validate inputs
            if(empty($fullname) || empty($email) || empty($password)) {
                $error = "All fields are required.";
                require __DIR__ . '/../views/register.php';
                return;
            }

            if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "Invalid email format.";
                require __DIR__ . '/../views/register.php';
                return;
            }

            $userId = $this->userModel->register($fullname, $email, $password);

            if($userId) {
                $token = bin2hex(random_bytes(32));
                $this->userModel->storeToken($userId, $token);
                $this->sendVerificationEmail($email, $token);

                $message = "Registration successful! Please check your email to verify your account.";
                require __DIR__ . '/../views/message.php'; 
                return;
            } else {
                $error = "Registration failed! Email may already exist.";
            }
        }

        require __DIR__ . '/../views/register.php';
    }

    // ---------------------
    // Verify Email
    // ---------------------
    public function verify() {
        $token = trim($_GET['token'] ?? '');
        if($token) {
            if($this->userModel->verifyEmail($token)) {
                $message = "Email verified successfully! You can now login.";
            } else {
                $error = "Invalid or expired verification link.";
            }
        } else {
            $error = "No token provided!";
        }

        require __DIR__ . '/../views/verify_email.php';
    }

    // ---------------------
    // Login
    // ---------------------
    public function login() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email    = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if(empty($email) || empty($password)) {
                $error = "Email and password are required!";
                require __DIR__ . '/../views/login.php';
                return;
            }

            $userResult = $this->userModel->login($email);
            $user = $userResult ? $userResult->fetch_assoc() : null;

            if(!$user) {
                $error = "Email not found or not verified!";
                require __DIR__ . '/../views/login.php';
                return;
            }

            if(!password_verify($password, $user['password'])) {
                $error = "Incorrect password!";
                require __DIR__ . '/../views/login.php';
                return;
            }

            // Generate OTP and save
            $otp = rand(100000, 999999);
            $expiry = date('Y-m-d H:i:s', strtotime('+5 minutes'));
            $this->userModel->saveOTP($user['id'], $otp, $expiry);

            // Save session info
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['email'] = $user['email'];

            $this->sendOTPEmail($email, $otp);

            header("Location: index.php?action=otp");
            exit();
        }

        require __DIR__ . '/../views/login.php';
    }

    // ---------------------
    // OTP Verification
    // ---------------------
    public function otp() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $otpInput = trim($_POST['otp'] ?? '');
            $user_id = $_SESSION['user_id'] ?? null;

            if(!$user_id) {
                header("Location: index.php?action=login");
                exit();
            }

            if($this->userModel->verifyOTP($user_id, $otpInput)) {
                header("Location: index.php?action=home");
                exit();
            } else {
                $error = "Invalid or expired OTP.";
            }
        }

        require __DIR__ . '/../views/otp.php';
    }

    // ---------------------
    // Resend OTP
    // ---------------------
    public function resendOTP() {
        $user_id = $_SESSION['user_id'] ?? null;
        $email = $_SESSION['email'] ?? null;

        if(!$user_id || !$email) {
            header("Location: index.php?action=login");
            exit();
        }

        $otp = rand(100000, 999999);
        $expiry = date('Y-m-d H:i:s', strtotime('+5 minutes'));
        $this->userModel->saveOTP($user_id, $otp, $expiry);
        $this->sendOTPEmail($email, $otp);

        $_SESSION['otp_message'] = "A new OTP has been sent to your email.";
        header("Location: index.php?action=otp");
        exit();
    }

    // ---------------------
    // Home
    // ---------------------
    public function home() {
        if(!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=login");
            exit();
        }
        require __DIR__ . '/../views/home.php';
    }

    // ---------------------
    // Logout
    // ---------------------
    public function logout() {
        session_unset();
        session_destroy();
        header("Location: index.php?action=login");
        exit();
    }
}