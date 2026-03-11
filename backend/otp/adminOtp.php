<?php

require_once __DIR__ . '/../config/mailer.php';

function generateAdminOTP() {
    return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
}

function storeAdminOTP($email, $otp) {
    $pdo     = getDBConnection();
    $expires = time() + 600; // 10 minutes

    // Remove any existing OTP for this email
    $pdo->prepare("DELETE FROM otp_verifications WHERE email = ?")->execute([$email]);

    // Insert new OTP
    $stmt = $pdo->prepare("INSERT INTO otp_verifications (email, otp, expires_at) VALUES (?, ?, ?)");
    $stmt->execute([$email, $otp, $expires]);
}

function sendAdminOTP($email) {
    try {
        $otp = generateAdminOTP();
        storeAdminOTP($email, $otp);

        $subject = 'Admin Login OTP - PTCI Student Council';
        $body    = '
        <div style="font-family: Arial, sans-serif; max-width: 480px; margin: 0 auto; padding: 32px; background: #f9f9f9; border-radius: 8px;">
            <div style="text-align: center; margin-bottom: 24px;">
                <h2 style="color: #111; margin: 0;">Admin Login Verification</h2>
                <p style="color: #555; margin-top: 8px;">PTCI Student Council Election System</p>
            </div>
            <div style="background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 24px; text-align: center;">
                <p style="color: #333; margin: 0 0 16px;">Your one-time login code is:</p>
                <div style="font-size: 40px; font-weight: 700; letter-spacing: 12px; color: #111; padding: 16px 0;">
                    ' . $otp . '
                </div>
                <p style="color: #888; font-size: 13px; margin: 16px 0 0;">
                    This code expires in <strong>10 minutes</strong>.
                </p>
            </div>
            <p style="color: #aaa; font-size: 12px; text-align: center; margin-top: 24px;">
                If you did not request this, please ignore this email.
            </p>
        </div>';

        $sent = sendOTPEmail($email, $subject, $body);

        if ($sent) {
            return ['success' => true];
        } else {
            return ['success' => false, 'message' => 'Failed to send email.'];
        }
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

function verifyAdminOTP($email, $otp) {
    $pdo  = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM otp_verifications WHERE email = ? ORDER BY id DESC LIMIT 1");
    $stmt->execute([$email]);
    $record = $stmt->fetch();

    if (!$record) {
        return ['success' => false, 'message' => 'OTP not found. Please request a new one.'];
    }

    if (time() > $record['expires_at']) {
        $pdo->prepare("DELETE FROM otp_verifications WHERE email = ?")->execute([$email]);
        return ['success' => false, 'message' => 'OTP has expired. Please log in again.'];
    }

    if ($record['otp'] !== $otp) {
        return ['success' => false, 'message' => 'Incorrect OTP. Please try again.'];
    }

    // Delete used OTP
    $pdo->prepare("DELETE FROM otp_verifications WHERE email = ?")->execute([$email]);
    return ['success' => true];
}