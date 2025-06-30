<?php

require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';
require_once __DIR__ . '/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailService
{
    private static array $config;

    private static function loadConfig(): void
    {
        if (!isset(self::$config)) {
            self::$config = require __DIR__ . '/../config/config.php';
        }
    }

    public static function send(string $to, string $subject, string $body): bool
    {
        self::loadConfig();

        $mail = new PHPMailer(true);
        $mail->CharSet = 'UTF-8';

        try {
            $mail->isSMTP();
            $mail->Host = self::$config['mail']['smtp_host'];
            $mail->SMTPAuth = true;
            $mail->Username = self::$config['mail']['smtp_user'];
            $mail->Password = self::$config['mail']['smtp_pass'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = self::$config['mail']['smtp_port'];

            $fromEmail = self::$config['mail']['from_email'];
            $fromName = self::$config['mail']['from_name'];
            $mail->setFrom($fromEmail, $fromName);

            $mail->addAddress($to);

            $mail->isHTML(false);
            $mail->Subject = $subject;
            $mail->Body = $body;

            return $mail->send();
        } catch (Exception $e) {
            error_log("Mailer Error: " . $mail->ErrorInfo);
            return false;
        }
    }
}
