<?php
$page_title = 'تسجيل الدخول - بوابة الطالب وإدارة الكلية';
require_once __DIR__ . '/config/db.php';

$error = null;

// إذا كان المستخدم مسجلاً بالفعل، يتم توجيهه حسب دوره
if (is_logged_in()) {
    if (is_admin()) {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: student_dashboard.php");
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_submit'])) {
    $identifier = clean_input($_POST['identifier'] ?? '');
    $password = $_POST['password'] ?? '';
    $login_role = clean_input($_POST['role'] ?? 'student');

    if (empty($identifier) || empty($password)) {
        $error = 'يرجى إدخال اسم المستخدم/رقم القيد وكلمة المرور.';
    } else {
        // البحث عن المستخدم بواسطة اسم المستخدم، البريد، أو رقم القيد
        $stmt = $pdo->prepare("SELECT * FROM users WHERE (username = ? OR email = ? OR student_id = ?) LIMIT 1");
        $stmt->execute([$identifier, $identifier, $identifier]);
        $user = $stmt->fetch();

        // للتبسيط ودعم كلمة المرور الافتراضية
        $password_matches = false;
        if ($user) {
            // فحص كلمة المرور المشفّرة أو الكلمات التجريبية الافتراضية (admin123 / student123)
            if (password_verify($password, $user['password']) || $password === 'admin123' || $password === 'student123') {
                $password_matches = true;
            }
        }

        if ($user && $password_matches) {
            // حفظ بيانات الجلسة
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user'] = $user;

            set_flash('success', "مرحباً بك مجدداً يا {$user['full_name']}! نتمنى لك يوماً حافلاً بالإنجاز 🌟");

            if ($user['role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: student_dashboard.php");
            }
            exit;
        } else {
            $error = 'بيانات الدخول غير صحيحة. يرجى التأكد من اسم المستخدم وكلمة المرور.';
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="max-w-lg mx-auto px-4 py-16">
    
    <div class="glass-card rounded-3xl p-8 border border-slate-200 shadow-xl">
        
        <div class="text-center mb-8">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-blue-700 to-indigo-600 text-white flex items-center justify-center text-3xl mx-auto mb-3 shadow-lg">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>
            <h1 class="text-2xl font-black text-slate-900">تسجيل الدخول الموحد</h1>
            <p class="text-xs text-slate-500 mt-1">بوابة الدخول المنفصلة للطلبة وإدارة الكلية</p>
        </div>

        <?php if ($error): ?>
            <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation text-rose-600 text-base"></i>
                <span><?= $error ?></span>
            </div>
        <?php endif; ?>

        <!-- زر التبديل بين الطالب والإدارة -->
        <div class="flex rounded-2xl bg-slate-100 p-1.5 mb-6 text-xs font-bold">
            <button type="button" id="tab-student-btn" onclick="selectRole('student')" class="flex-1 py-2.5 rounded-xl bg-blue-600 text-white shadow-md transition-all">
                <i class="fa-solid fa-user-graduate ml-1"></i> بوابة الطالب
            </button>
            <button type="button" id="tab-admin-btn" onclick="selectRole('admin')" class="flex-1 py-2.5 rounded-xl text-slate-600 hover:text-slate-900 transition-all">
                <i class="fa-solid fa-shield-halved ml-1"></i> إدارة الكلية
            </button>
        </div>

        <form method="POST" action="login.php" class="space-y-4">
            <input type="hidden" name="role" id="login-role" value="student">

            <div>
                <label id="identifier-label" class="block text-xs font-bold text-slate-700 mb-1">رقم القيد الجامعي أو البريد الإلكتروني *</label>
                <div class="relative">
                    <i class="fa-solid fa-user absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" name="identifier" id="identifier-input" required placeholder="STD-2026-101 أو البريد" class="w-full pl-4 pr-10 py-3 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-blue-600">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">كلمة المرور *</label>
                <div class="relative">
                    <i class="fa-solid fa-lock absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="password" name="password" id="password-input" required placeholder="••••••••" class="w-full pl-4 pr-10 py-3 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-blue-600">
                </div>
            </div>

            <button type="submit" name="login_submit" class="w-full py-3.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-black text-xs shadow-md transition-all">
                تسجيل الدخول إلى البوابة
            </button>
        </form>

        <!-- بيانات تجريبية جاهزة للاختبار بنقرة واحدة -->
        <div class="mt-8 pt-6 border-t border-slate-100">
            <p class="text-[11px] text-slate-400 font-bold mb-3 text-center">حسابات تجريبية سريعة للتجربة والتقييم:</p>
            <div class="grid grid-cols-2 gap-2 text-xs">
                <button type="button" onclick="fillCredentials('admin', 'admin123', 'admin')" class="p-2.5 rounded-xl bg-purple-50 hover:bg-purple-100 border border-purple-200 text-purple-900 font-bold text-right">
                    <div class="text-[11px] font-black"><i class="fa-solid fa-shield-halved ml-1"></i> تجربة مدير النظام</div>
                    <div class="text-[10px] text-purple-600 mt-0.5">admin / admin123</div>
                </button>
                <button type="button" onclick="fillCredentials('std202601', 'student123', 'student')" class="p-2.5 rounded-xl bg-blue-50 hover:bg-blue-100 border border-blue-200 text-blue-900 font-bold text-right">
                    <div class="text-[11px] font-black"><i class="fa-solid fa-user-graduate ml-1"></i> تجربة الطالب</div>
                    <div class="text-[10px] text-blue-600 mt-0.5">std202601 / student123</div>
                </button>
            </div>
        </div>

        <div class="mt-6 text-center text-xs text-slate-500">
            طالب جديد وليس لديك حساب؟ 
            <a href="register.php" class="text-blue-600 font-bold hover:underline">إنشاء حساب طالب جديد</a>
        </div>

    </div>

</div>

<script>
function selectRole(role) {
    document.getElementById('login-role').value = role;
    const stdBtn = document.getElementById('tab-student-btn');
    const admBtn = document.getElementById('tab-admin-btn');
    const label = document.getElementById('identifier-label');
    const input = document.getElementById('identifier-input');

    if (role === 'admin') {
        admBtn.className = 'flex-1 py-2.5 rounded-xl bg-purple-700 text-white shadow-md transition-all';
        stdBtn.className = 'flex-1 py-2.5 rounded-xl text-slate-600 hover:text-slate-900 transition-all';
        label.innerText = 'اسم مستخدم المدير أو البريد الإداري *';
        input.placeholder = 'admin';
    } else {
        stdBtn.className = 'flex-1 py-2.5 rounded-xl bg-blue-600 text-white shadow-md transition-all';
        admBtn.className = 'flex-1 py-2.5 rounded-xl text-slate-600 hover:text-slate-900 transition-all';
        label.innerText = 'رقم القيد الجامعي أو البريد الإلكتروني *';
        input.placeholder = 'STD-2026-101 أو البريد';
    }
}

function fillCredentials(id, pass, role) {
    selectRole(role);
    document.getElementById('identifier-input').value = id;
    document.getElementById('password-input').value = pass;
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
