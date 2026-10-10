<?php
/**
 * Al-Ufuq College - Database Configuration & Helpers
 * ملف إعداد الاتصال بقاعدة بيانات كلية الأفق والدوال المساعدة
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$db_host = '127.0.0.1';
$db_port = '3306';
$db_name = 'alufuq_college';
$db_user = 'root';
$db_pass = '';

try {
    $dsn = "mysql:host={$db_host};port={$db_port};dbname={$db_name};charset=utf8mb4";
    $pdo = new PDO($dsn, $db_user, $db_pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ]);
} catch (PDOException $e) {
    // محاولة إنشاء قاعدة البيانات إذا لم تكن موجودة
    try {
        $temp_pdo = new PDO("mysql:host={$db_host};port={$db_port};charset=utf8mb4", $db_user, $db_pass);
        $temp_pdo->exec("CREATE DATABASE IF NOT EXISTS `{$db_name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
        $sql_file = __DIR__ . '/../sql/database.sql';
        if (file_exists($sql_file)) {
            $sql = file_get_contents($sql_file);
            $temp_pdo->exec($sql);
        }
        $pdo = new PDO($dsn, $db_user, $db_pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    } catch (PDOException $ex) {
        die("<div style='direction:rtl; font-family:tahoma; padding:20px; background:#fee2e2; color:#991b1b; border-radius:12px; margin:20px;'>
            <h3>تعذر الاتصال بقاعدة البيانات في تطبيق XAMPP!</h3>
            <p>يرجى التأكد من تشغيل خادم MySQL في لوحة تحكم XAMPP.</p>
            <p><b>رسالة الخطأ:</b> " . htmlspecialchars($ex->getMessage()) . "</p>
        </div>");
    }
}

// دالة تنظيف وتأمين المدخلات
function clean_input($data) {
    if (is_array($data)) {
        return array_map('clean_input', $data);
    }
    return htmlspecialchars(trim((string)$data), ENT_QUOTES, 'UTF-8');
}

// دوال المصادقة والجلسة
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function current_user() {
    return $_SESSION['user'] ?? null;
}

function is_admin() {
    return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
}

function is_student() {
    return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'student';
}

function require_login($redirect = 'login.php') {
    if (!is_logged_in()) {
        $_SESSION['flash_error'] = 'يرجى تسجيل الدخول للوصول إلى هذه الصفحة.';
        header("Location: {$redirect}");
        exit;
    }
}

function require_admin() {
    require_login();
    if (!is_admin()) {
        $_SESSION['flash_error'] = 'هذه الصفحة مخصصة لمدير النظام وإدارة الكلية فقط!';
        header("Location: student_dashboard.php");
        exit;
    }
}

function set_flash($type, $message) {
    $_SESSION['flash_' . $type] = $message;
}

function get_flash($type) {
    if (isset($_SESSION['flash_' . $type])) {
        $msg = $_SESSION['flash_' . $type];
        unset($_SESSION['flash_' . $type]);
        return $msg;
    }
    return null;
}

// عبارات تشجيعية للطلاب في واجهة الكلية
function get_motivational_quotes() {
    return [
        [
            'quote' => 'طموحك يبدأ من هنا نحو آفاق لا حدود لها 🚀',
            'author' => 'كلية الأفق - بوابتك نحو المستقبل'
        ],
        [
            'quote' => 'التعليم ليس ملء دلو، بل إيقاد شعلة شغف وإبداع ✨',
            'author' => 'رسالة الكلية لطلبتها'
        ],
        [
            'quote' => 'لكل مجتهد نصيب.. وأفقك يتسع لكل أحلامك الكبيرة 🎓',
            'author' => 'عمادة شؤون الطلاب'
        ],
        [
            'quote' => 'اصنع أثرك اليوم.. الغد ملك لأولئك الذين يؤمنون بجمال أحلامهم 🌟',
            'author' => 'ملهمي الأفق'
        ],
        [
            'quote' => 'معارف اليوم تصنع ريادة الغد.. أهلاً بصناع التغيير 💡',
            'author' => 'أسرة كلية الأفق'
        ]
    ];
}

// دالة لتنسيق التاريخ العربي
function format_date_ar($date_str) {
    if (!$date_str) return '';
    $timestamp = strtotime($date_str);
    $months = [
        1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
        5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
        9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
    ];
    $day = date('j', $timestamp);
    $m = (int)date('n', $timestamp);
    $year = date('Y', $timestamp);
    return "{$day} " . ($months[$m] ?? '') . " {$year}";
}
