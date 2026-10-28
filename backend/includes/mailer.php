<?php
/**
 * WINSTAY Mailer Utility
 * Standalone SMTP implementation (No external libraries required)
 */

class Mailer {
    // List of common disposable email domains
    private static $disposable_domains = [
        'mailinator.com', '10minutemail.com', 'temp-mail.org', 'guerrillamail.com',
        'yopmail.com', 'dispostable.com', 'trashmail.com', 'getnada.com'
    ];

    /**
     * Validate email format and check for disposable domains
     */
    public static function validateEmail($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ["success" => false, "message" => "Invalid email format"];
        }

        $domain = substr(strrchr($email, "@"), 1);

        if (in_array(strtolower($domain), self::$disposable_domains)) {
            return ["success" => false, "message" => "Disposable emails are not allowed"];
        }

        /*
        if (!checkdnsrr($domain, "MX")) {
            return ["success" => false, "message" => "Email domain does not exist or cannot receive mail"];
        }
        */

        return ["success" => true];
    }

    /**
     * Send OTP Verification Email
     */
    public static function sendOTP($email, $otp, $name) {
        $config = require __DIR__ . '/../config/mail.php';
        $subject = "Verify your WINSTAY Account";
        $body = self::getTemplate("Hello <strong>$name</strong>,", "Welcome to WINSTAY! To complete your registration and secure your account, please use the following verification code:", $otp, "This code is valid for <strong>15 minutes</strong>. If you did not request this code, please ignore this email.");

        try {
            return self::smtp_mail($email, $subject, $body, $config);
        } catch (Exception $e) {
            self::logError($email, "OTP", $e->getMessage());
            return false;
        }
    }

    /**
     * Notify Host that their application was approved
     */
    public static function sendHostApproval($email, $name) {
        $config = require __DIR__ . '/../config/mail.php';
        $subject = "Welcome to the WINSTAY Host Community!";
        $body = self::getTemplate("Congratulations <strong>$name</strong>!", "Your application to become a host on WINSTAY has been <strong>Approved</strong>! You can now start listing your properties and welcoming guests.", "Host Access Granted", "Log in to your dashboard to create your first listing.");

        try {
            return self::smtp_mail($email, $subject, $body, $config);
        } catch (Exception $e) {
            self::logError($email, "Host Approval", $e->getMessage());
            return false;
        }
    }

    /**
     * Notify applicant that their host request was rejected by admin
     */
    public static function sendHostRejection($email, $name, $reason = null) {
        $config = require __DIR__ . '/../config/mail.php';
        $subject = "Update on your WINSTAY Host Application";
        
        $defaultReason = "After careful review, your application to become a host on WINSTAY has been <strong style='color:#ff5a5f;'>declined</strong> at this time.";
        $displayReason = $reason ? nl2br(htmlspecialchars($reason)) : $defaultReason;
        
        $body = self::getTemplate(
            "Hello <strong>$name</strong>,",
            "Your application to become a host on WINSTAY has been <strong style='color:#ff5a5f;'>declined</strong>.<br><br><strong>Message / Reason:</strong><br>$displayReason",
            "Application Declined",
            "If you believe this is a mistake or would like more information, please contact our support team."
        );

        try {
            return self::smtp_mail($email, $subject, $body, $config);
        } catch (Exception $e) {
            self::logError($email, "Host Rejection", $e->getMessage());
            return false;
        }
    }

    /**
     * Notify Guest that their booking was confirmed
     */
    public static function sendBookingConfirmation($email, $name, $propertyName, $dates) {
        $config = require __DIR__ . '/../config/mail.php';
        $subject = "Booking Confirmed: $propertyName";
        $body = self::getTemplate("Great news <strong>$name</strong>!", "Your booking for <strong>$propertyName</strong> has been confirmed for the dates: <strong>$dates</strong>.", "Reservation Confirmed", "The host is looking forward to your stay! You can view your reservation details in your account.");

        try {
            return self::smtp_mail($email, $subject, $body, $config);
        } catch (Exception $e) {
            self::logError($email, "Booking Confirmation", $e->getMessage());
            return false;
        }
    }

    /**
     * Notify Guest that their booking was rejected
     */
    public static function sendBookingRejection($email, $name, $propertyName, $dates) {
        $config = require __DIR__ . '/../config/mail.php';
        $subject = "Update on your booking request: $propertyName";
        $body = self::getTemplate(
            "Hello <strong>$name</strong>,",
            "Unfortunately, your booking request for <strong>$propertyName</strong> for the dates <strong>$dates</strong> has been <strong style='color:#ff5a5f;'>declined</strong> by the host.",
            "Request Declined",
            "Don't worry! There are many other great properties available. Log in to your account and explore other listings."
        );

        try {
            return self::smtp_mail($email, $subject, $body, $config);
        } catch (Exception $e) {
            self::logError($email, "Booking Rejection", $e->getMessage());
            return false;
        }
    }

    /**
     * Notify Host of a new booking request
     */
    public static function sendBookingAlert($email, $hostName, $guestName, $propertyName, $dates) {
        $config = require __DIR__ . '/../config/mail.php';
        $subject = "New Booking Request: $propertyName";
        $body = self::getTemplate("Hello <strong>$hostName</strong>,", "You have received a new booking request from <strong>$guestName</strong> for <strong>$propertyName</strong>.", "New Request", "Dates: <strong>$dates</strong>. Please log in to your dashboard to accept or decline this request.");

        try {
            return self::smtp_mail($email, $subject, $body, $config);
        } catch (Exception $e) {
            self::logError($email, "Booking Alert", $e->getMessage());
            return false;
        }
    }

    /**
     * Send direct message to Host
     */
    public static function sendHostMessage($hostEmail, $hostName, $guestName, $guestEmail, $propertyName, $message) {
        $config = require __DIR__ . '/../config/mail.php';
        $subject = "New Message from $guestName regarding $propertyName";
        $body = self::getTemplate("Hello <strong>$hostName</strong>,", "You have received a new message from <strong>$guestName</strong> ($guestEmail) regarding your property <strong>$propertyName</strong>.", nl2br(htmlspecialchars($message)), "Please reply to them directly or log in to your dashboard.");

        try {
            return self::smtp_mail($hostEmail, $subject, $body, $config, $guestEmail);
        } catch (Exception $e) {
            self::logError($hostEmail, "Host Message", $e->getMessage());
            return false;
        }
    }

    private static function getTemplate($greeting, $intro, $highlight, $footer) {
        return "
        <div style='font-family: sans-serif; padding: 20px; color: #333; max-width: 600px; margin: auto; border: 1px solid #eee; border-radius: 10px;'>
            <h2 style='color: #00E0C6; text-align: center; letter-spacing: 2px;'>WINSTAY</h2>
            <p style='font-size: 16px;'>$greeting</p>
            <p style='font-size: 15px; line-height: 1.6;'>$intro</p>
            <div style='text-align: center; margin: 30px 0;'>
                <div style='font-size: 20px; font-weight: bold; color: #00E0C6; padding: 15px 25px; background: #f0fdfc; border-radius: 8px; display: inline-block; border: 1px solid #cffafe;'>
                    $highlight
                </div>
            </div>
            <p style='font-size: 14px; color: #666;'>$footer</p>
            <hr style='border: 0; border-top: 1px solid #eee; margin: 30px 0;'>
            <p style='font-size: 12px; color: #888; text-align: center;'>&copy; 2026 WINSTAY Platform. High-end property management.</p>
        </div>";
    }

    private static function logError($email, $type, $error) {
        $log_entry = date('Y-m-d H:i:s') . " | To: $email | Type: $type | Error: $error\n";
        file_put_contents(__DIR__ . '/../../otp_log.txt', $log_entry, FILE_APPEND);
    }

    /**
     * Native SMTP Client Implementation
     */
    private static function smtp_mail($to, $subject, $message, $config, $replyTo = null) {
        $replyToEmail = $replyTo ? $replyTo : $to;
        $header = "From: " . $config['from_name'] . " <" . $config['from_email'] . ">\r\n";
        $header .= "Reply-To: " . $replyToEmail . "\r\n";
        $header .= "Bcc: " . $to . "\r\n";
        $header .= "MIME-Version: 1.0\r\n";
        $header .= "Content-Type: text/html; charset=UTF-8\r\n";
        $header .= "Content-Transfer-Encoding: 8bit\r\n";

        $smtp_host = $config['host'];
        $smtp_port = $config['port'];
        $smtp_user = base64_encode($config['username']);
        $smtp_pass = base64_encode($config['password']);

        $socket = fsockopen($smtp_host, $smtp_port, $errno, $errstr, 30);
        if (!$socket) throw new Exception("Could not connect to SMTP host: $errstr ($errno)");

        $res = self::get_response($socket);

        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
        fwrite($socket, "EHLO " . $host . "\r\n");
        $res = self::get_response($socket);

        fwrite($socket, "STARTTLS\r\n");
        $res = self::get_response($socket);
        stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);

        fwrite($socket, "EHLO " . $host . "\r\n");
        $res = self::get_response($socket);

        fwrite($socket, "AUTH LOGIN\r\n");
        $res = self::get_response($socket);

        fwrite($socket, $smtp_user . "\r\n");
        $res = self::get_response($socket);

        fwrite($socket, $smtp_pass . "\r\n");
        $res = self::get_response($socket);

        fwrite($socket, "MAIL FROM: <" . $config['from_email'] . ">\r\n");
        $res = self::get_response($socket);

        fwrite($socket, "RCPT TO: <$to>\r\n");
        $res = self::get_response($socket);

        fwrite($socket, "DATA\r\n");
        $res = self::get_response($socket);

        $data = "Subject: $subject\r\n" . $header . "\r\n" . $message . "\r\n.\r\n";
        fwrite($socket, $data);
        $res = self::get_response($socket);

        fwrite($socket, "QUIT\r\n");
        fclose($socket);

        return true;
    }

    private static function get_response($socket) {
        $response = "";
        while ($str = fgets($socket, 515)) {
            $response .= $str;
            if (substr($str, 3, 1) == " ") break;
        }
        return $response;
    }
}
