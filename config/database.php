<?php
/**
 * ডাটাবেস কনফিগারেশন
 * সবারকথা নিউজ পোর্টাল
 */

// পরিবেশ সেটিংস
define('ENVIRONMENT', 'production'); // 'development' বা 'production'

// ডাটাবেস কনফিগারেশন
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');  // cPanel-এ আপনার পাসওয়ার্ড সেট করুন
define('DB_NAME', 'bangla_news');
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATION', 'utf8mb4_unicode_ci');

// সাইট ���নফিগারেশন
define('SITE_URL', 'https://sabarkotha.com'); // আপনার ডোমেইন
define('SITE_NAME', 'সবারকথা');
define('SITE_DESC', 'সব খবর এক সাথে');
define('SITE_EMAIL', 'info@sabarkotha.com');
define('ADMIN_EMAIL', 'admin@sabarkotha.com');

// ব্র্যান্ড কালার (হালকা লাল + অ্যাশ হোয়াইট)
define('PRIMARY_COLOR', '#E8504B');      // হালকা লাল
define('SECONDARY_COLOR', '#F5F5F5');   // অ্যাশ হোয়াইট
define('TEXT_COLOR', '#333333');
define('LIGHT_GRAY', '#EEEEEE');
define('DARK_GRAY', '#666666');

// ফাইল আপলোড কনফিগারেশন
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('MAX_UPLOAD_SIZE', 5242880); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
$GLOBALS['ALLOWED_IMAGE_TYPES'] = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

// পেজিনেশন
define('POSTS_PER_PAGE', 12);
define('NEWS_CARDS_PER_ROW', 3);

// ক্যাশ সেটিংস
define('CACHE_ENABLED', true);
define('CACHE_LIFETIME', 3600); // 1 ঘণ্টা

// নিরাপত্তা সেটিংস
define('SESSION_LIFETIME', 1800); // ৩০ মিনিট
define('PASSWORD_MIN_LENGTH', 8);

// সোশ্যাল মিডিয়া লিংক
define('FACEBOOK_URL', 'https://www.facebook.com/sabarkotha');
define('TWITTER_URL', 'https://twitter.com/sabarkotha');
define('YOUTUBE_URL', 'https://www.youtube.com/@sabarkotha');
define('INSTAGRAM_URL', 'https://www.instagram.com/sabarkotha');

// নিউজলেটার কনফিগারেশন
define('NEWSLETTER_SENDER', 'noreply@sabarkotha.com');
define('NEWSLETTER_SENDER_NAME', 'সবারকথা নিউজলেটার');

// PDO কানেকশন তৈরি করুন
function getDatabaseConnection() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            
            $pdo = new PDO(
                $dsn,
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_PERSISTENT => false,
                ]
            );
        } catch (PDOException $e) {
            if (ENVIRONMENT === 'development') {
                die("ডাটাবেস সংযোগ ব্যর্থ: " . $e->getMessage());
            } else {
                die("একটি সিস্টেম এরর ঘটেছে। পরে আবার চেষ্টা করুন।");
            }
        }
    }
    
    return $pdo;
}

// গ্লোবাল ডাটাবেস অবজেক্ট
$pdo = getDatabaseConnection();
