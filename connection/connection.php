<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'database_scv');    
define('DB_USER', 'root');      
define('DB_PASS', '023111');         
define('DB_CHARSET', 'utf8mb4');

/**
 * Get PDO Database Connection
 * 
 * @return PDO|null
 */
function getDBConnection() {
    static $pdo = null;
    
    if ($pdo !== null) {
        return $pdo;
    }
    
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_PERSISTENT         => false,
        ];
        
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
        
    } catch (PDOException $e) {
        error_log("Database Connection Error: " . $e->getMessage());
        
        // ERROR MESSAGE
        die("Sorry, we couldn't connect to the database. Please try again later.");
    }
}

try {
    $pdo = getDBConnection();
} catch (Exception $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}
?>
