<?php
/**
 * নিরাপত্তা এবং ভ্যালিডেশন ফাংশন
 * সবারকথা নিউজ পোর্টাল
 */

/**
 * SQL ইনজেকশন থেকে সুরক্ষিত করতে প্রিপেয়ার্ড স্টেটমেন্ট ব্যবহার করুন
 * 
 * @param PDO $pdo - ডাটাবেস কানেকশন
 * @param string $query - SQL কোয়েরি
 * @param array $params - কোয়েরি পরামিতি
 * @return PDOStatement|false
 */
function executeQuery($pdo, $query, $params = []) {
    try {
        $stmt = $pdo->prepare($query);
        if (!empty($params)) {
            foreach ($params as $key => $value) {
                $type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
                $stmt->bindValue($key, $value, $type);
            }
        }
        $stmt->execute();
        return $stmt;
    } catch (PDOException $e) {
        error_log("কোয়েরি এরর: " . $e->getMessage());
        return false;
    }
}

/**
 * ইনপুট ডেটা স্যানিটাইজ করুন
 * 
 * @param string $input
 * @return string
 */
function sanitizeInput($input) {
    if (is_array($input)) {
        return array_map('sanitizeInput', $input);
    }
    
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    
    return $input;
}

/**
 * XSS থেকে সুরক্ষার জন্য আউটপুট এনকোড করুন
 * 
 * @param string $data
 * @return string
 */
function escapeOutput($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/**
 * CSRF টোকেন তৈরি এবং যাচাই করুন
 * 
 * @return void
 */
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

/**
 * CSRF টোকেন যাচাই করুন
 * 
 * @param string $token
 * @return bool
 */
function verifyCSRFToken($token) {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * ইমেইল যাচাই করুন
 * 
 * @param string $email
 * @return bool
 */
function isValidEmail($email) {
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * URL স্লাগ তৈরি করুন
 * 
 * @param string $text
 * @return string
 */
function generateSlug($text) {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/i', '-', $text);
    $text = trim($text, '-');
    return $text;
}

/**
 * ফাইল আপলোড যাচাই করুন
 * 
 * @param array $file - $_FILES এরে থেকে ফাইল
 * @param int $max_size - সর্বোচ্চ সাইজ (বাইট)
 * @return array - ['success' => bool, 'message' => string, 'filename' => string]
 */
function validateFileUpload($file, $max_size = 5242880) {
    $response = ['success' => false, 'message' => '', 'filename' => ''];
    
    if (empty($file) || $file['error'] !== UPLOAD_ERR_OK) {
        $response['message'] = 'ফাইল আপলোড ব্যর্থ হয়েছে';
        return $response;
    }
    
    // ফাইল সাইজ চেক করুন
    if ($file['size'] > $max_size) {
        $response['message'] = 'ফাইল সাইজ অনুমতির চেয়ে বেশি (সর্বোচ্চ ' . ($max_size / 1024 / 1024) . 'MB)';
        return $response;
    }
    
    // MIME টাইপ চেক করুন
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mime_type, ALLOWED_IMAGE_TYPES)) {
        $response['message'] = 'শুধুমাত্র ছবি ফাইল আপলোড করতে পারবেন (JPG, PNG, GIF, WebP)';
        return $response;
    }
    
    // ফাইলের এক্সটেনশন চেক করুন
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    if (!in_array(strtolower($ext), ALLOWED_EXTENSIONS)) {
        $response['message'] = 'অনুমতিহীন ফাইল এক্সটেনশন';
        return $response;
    }
    
    // ইমেজ ফাইল যাচাই করুন
    $image_info = @getimagesize($file['tmp_name']);
    if ($image_info === false) {
        $response['message'] = 'ফাইলটি একটি বৈধ ছবি নয়';
        return $response;
    }
    
    // নিরাপদ ফাইলনাম তৈরি করুন
    $safe_filename = uniqid() . '_' . time() . '.' . $ext;
    
    $response['success'] = true;
    $response['filename'] = $safe_filename;
    
    return $response;
}

/**
 * পাসওয়ার্ড হ্যাশ করুন
 * 
 * @param string $password
 * @return string
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

/**
 * পাসওয়ার্ড যাচাই করুন
 * 
 * @param string $password
 * @param string $hash
 * @return bool
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * লগ রেকর্ড করুন
 * 
 * @param string $message
 * @param string $level - 'info', 'warning', 'error'
 * @return void
 */
function logMessage($message, $level = 'info') {
    $log_dir = __DIR__ . '/../logs/';
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    
    $log_file = $log_dir . date('Y-m-d') . '.log';
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[$timestamp] [$level] $message\n";
    
    file_put_contents($log_file, $log_entry, FILE_APPEND);
}

/**
 * ডেটাবেস এরর হ্যান্ডলার
 * 
 * @param Exception $e
 * @param string $context
 * @return void
 */
function handleDatabaseError($e, $context = '') {
    logMessage("Database Error in $context: " . $e->getMessage(), 'error');
    
    if (ENVIRONMENT === 'development') {
        die("ডাটাবেস এরর: " . escapeOutput($e->getMessage()));
    } else {
        die("একটি সিস্টেম এরর হয়েছে। পরে আবার চেষ্টা করুন।");
    }
}
