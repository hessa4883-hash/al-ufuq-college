<?php
$page_title = 'إنشاء حساب طالب جديد - كلية الأفق';
require_once __DIR__ . '/config/db.php';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_submit'])) {
    $full_name = clean_input($_POST['full_name'] ?? '');
    $username = clean_input($_POST['username'] ?? '');
    $email = clean_input($_POST['email'] ?? '');
    $student_id = clean_input($_POST['student_id'] ?? '');
    $phone = clean_input($_POST['phone'] ?? '');
    $department_id = (int)($_POST['department_id'] ?? 1);
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if (empty($full_name) || empty($username) || empty($email) || empty($student_id) || empty($password)) {
        $error = 'يرجى ملء جميع الحقول المطلوبة.';
    } elseif ($password !== $password_confirm) {
        $error = 'كلمتا المرور غير متطابقتين.';
    } elseif (strlen($password) < 6) {
        $error = 'يجب ألا تقل كلمة المرور عن 6 خانات.';
    } else {
        // التحقق من تكرار اسم المستخدم أو البريد
        $stmt_check = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ? OR student_id = ?");
        $stmt_check->execute([$username, $email, $student_id]);
        if ($stmt_check->fetch()) {
            $error = 'اسم المستخدم أو البريد الإلكتروني أو رقم القيد مسجل مسبقاً!';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            try {
                $stmt_insert = $pdo->prepare("INSERT INTO users (username, full_name, email, password, role, student_id, phone, department_id) VALUES (?, ?, ?, ?, 'student', ?, ?, ?)");
                $stmt_insert->execute([$username, $full_name, $email, $hash, $student_id, $phone, $department_id]);

                $new_id = $pdo->lastInsertId();
                $_SESSION['user_id'] = $new_id;
                $_SESSION['user'] = [
                    'id' => $new_id,
                    'username' => $username,
                    'full_name' => $full_name,
                    'email' => $email,
                    'role' => 'student',
                    'student_id' => $student_id,
                    'department_id' => $department_id
                ];

                set_flash('success', "أهلاً بك يا {$full_name}! تم إنشاء حسابك بنجاح في بوابة كلية الأفق.");
                header("Location: student_dashboard.php");
                exit;
            } catch (PDOException $e) {
                $error = 'حدث خطأ أثناء التسجيل: ' . $e->getMessage();
            }
        }
    }
}

$departments = $pdo->query("SELECT id, name_ar FROM departments ORDER BY id ASC")->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div class="max-w-xl mx-auto px-4 py-14">
    <div class="glass-card rounded-3xl p-8 border border-slate-200 shadow-xl">
        <div class="text-center mb-8">
            <div class="w-16 h-16 rounded-2xl bg-blue-600 text-white flex items-center justify-center text-3xl mx-auto mb-3 shadow-lg">
                <i class="fa-solid fa-user-plus"></i>
            </div>
            <h1 class="text-2xl font-black text-slate-900">إنشاء حساب طالب جديد</h1>
            <p class="text-xs text-slate-500 mt-1">انضم لأسرة كلية الأفق وتمتع بالوصول لبوابتك وخدماتك الأكاديمية</p>
        </div>

        <?php if ($error): ?>
            <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation text-rose-600 text-base"></i>
                <span><?= $error ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="register.php" class="space-y-4">
            
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">الاسم الرباعي للطالب *</label>
                <input type="text" name="full_name" required placeholder="أحمد علي محمد" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-blue-600">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">اسم المستخدم (للدخول) *</label>
                    <input type="text" name="username" required placeholder="ahmed2026" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-blue-600">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">رقم القيد الجامعي *</label>
                    <input type="text" name="student_id" required placeholder="STD-2026-105" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-blue-600">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">البريد الإلكتروني *</label>
                    <input type="email" name="email" required placeholder="student@alufuq.edu" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-blue-600">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">رقم الهاتف / الواتساب</label>
                    <input type="tel" name="phone" placeholder="771234567" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-blue-600">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">القسم / التخصص الأكاديمي *</label>
                <select name="department_id" required class="w-full px-4 py-3 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-blue-600 bg-white">
                    <?php foreach ($departments as $d): ?>
                        <option value="<?= $d['id'] ?>">قسم <?= htmlspecialchars($d['name_ar']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">كلمة المرور *</label>
                    <input type="password" name="password" required placeholder="••••••••" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-blue-600">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">تأكيد كلمة المرور *</label>
                    <input type="password" name="password_confirm" required placeholder="••••••••" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-blue-600">
                </div>
            </div>

            <button type="submit" name="register_submit" class="w-full py-3.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-black text-xs shadow-md transition-all">
                إنشاء الحساب والدخول للبوابة
            </button>
        </form>

        <div class="mt-6 text-center text-xs text-slate-500">
            لديك حساب بالفعل؟ 
            <a href="login.php" class="text-blue-600 font-bold hover:underline">تسجيل الدخول</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
