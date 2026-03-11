<?php

function generateTOTPSecret($length = 16) {
    $chars  = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $secret = '';
    for ($i = 0; $i < $length; $i++) {
        $secret .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $secret;
}

function base32Decode($secret) {
    $base32chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $secret      = strtoupper($secret);
    $buffer      = 0;
    $bitsLeft    = 0;
    $result      = '';
    for ($i = 0; $i < strlen($secret); $i++) {
        $val = strpos($base32chars, $secret[$i]);
        if ($val === false) continue;
        $buffer   = ($buffer << 5) | $val;
        $bitsLeft += 5;
        if ($bitsLeft >= 8) {
            $result   .= chr(($buffer >> ($bitsLeft - 8)) & 0xFF);
            $bitsLeft -= 8;
        }
    }
    return $result;
}

function getTOTPCode($secret, $timeStep = null) {
    if ($timeStep === null) $timeStep = floor(time() / 30);
    $key    = base32Decode($secret);
    $time   = pack('N*', 0) . pack('N*', $timeStep);
    $hash   = hash_hmac('sha1', $time, $key, true);
    $offset = ord($hash[19]) & 0x0F;
    $otp    = (
        ((ord($hash[$offset])     & 0x7F) << 24) |
        ((ord($hash[$offset + 1]) & 0xFF) << 16) |
        ((ord($hash[$offset + 2]) & 0xFF) << 8)  |
         (ord($hash[$offset + 3]) & 0xFF)
    ) % 1000000;
    return str_pad($otp, 6, '0', STR_PAD_LEFT);
}

function verifyTOTPCode($secret, $code) {
    $timeStep = floor(time() / 30);
    for ($i = -1; $i <= 1; $i++) {
        if (getTOTPCode($secret, $timeStep + $i) === trim($code)) return true;
    }
    return false;
}

function getTOTPQRUrl($email, $secret, $issuer = 'PTCI Student Council') {
    $otpauth = "otpauth://totp/" . rawurlencode($issuer) . ":" . rawurlencode($email)
             . "?secret={$secret}&issuer=" . rawurlencode($issuer) . "&algorithm=SHA1&digits=6&period=30";
    return 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . rawurlencode($otpauth);
}

function saveTOTPSecret($email, $secret) {
    $pdo  = getDBConnection();
    $stmt = $pdo->prepare("UPDATE admins SET totp_secret = ? WHERE email = ?");
    $stmt->execute([$secret, $email]);
}

function getTOTPSecret($email) {
    $pdo  = getDBConnection();
    $stmt = $pdo->prepare("SELECT totp_secret FROM admins WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $row  = $stmt->fetch();
    return $row['totp_secret'] ?? null;
}