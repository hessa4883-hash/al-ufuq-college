<?php
$page_title = 'التقديمات لبداية العام الجامعي 2026/2027';
require_once __DIR__ . '/config/db.php';

$success_app = null;
$error = null;

// معالجة إرسال طلب التقديم
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_application'])) {
    $full_name = clean_input($_POST['full_name'] ?? '');
    $gender = clean_input($_POST['gender'] ?? 'ذكر');
    $national_id = clean_input($_POST['national_id'] ?? '');
    $phone = clean_input($_POST['phone'] ?? '');
    $email = clean_input($_POST['email'] ?? '');
    $address = clean_input($_POST['address'] ?? '');
    $high_school_branch = clean_input($_POST['high_school_branch'] ?? 'علمي');
    $seat_number = clean_input($_POST['seat_number'] ?? '');
    $graduation_year = (int)($_POST['graduation_year'] ?? 2026);
    $percentage = (float)($_POST['percentage'] ?? 0);
    $department_id = (int)($_POST['department_id'] ?? 0);
    $notes = clean_input($_POST['notes'] ?? '');

    if (empty($full_name) || empty($national_id) || empty($phone) || empty($seat_number) || $percentage <= 0 || $department_id <= 0) {
        $error = 'يرجى تعبئة جميع الحقول الإلزامية والتأكد من صحة النسبة ورقم الجلوس.';
    } else {
        // التحقق من توافق الفرع مع التخصص
        $stmt_check = $pdo->prepare("SELECT * FROM departments WHERE id = ?");
        $stmt_check->execute([$department_id]);
        $dept_info = $stmt_check->fetch();

        if ($dept_info && $dept_info['track_type'] === 'علمي' && $high_school_branch === 'أدبي') {
            $error = "عذراً، تخصص {$dept_info['name_ar']} متاح فقط لخريجي الثانوية العامة بالقسم العلمي.";
        } else {
            // توليد رقم طلب فريد
            $app_number = 'OFQ-2026-' . rand(1100, 9999);
            
            // تحديد الحالة الأولية
            $initial_status = ($percentage >= $dept_info['min_percentage']) ? 'مقبول مبدئياً' : 'قيد المراجعة';
            $initial_reply = ($initial_status === 'مقبول مبدئياً') 
                ? "تهانينا! نسبتك تؤهلك مبدئياً للالتحاق بقسم {$dept_info['name_ar']}. يرجى التوجه لمقر الكلية خلال 48 ساعة لتسليم الوثائق الأصلية."
                : "تم استلام طلبك وهو قيد المراجعة من لجنة القبول للنظر في استيفاء الشروط أو البرامج البديلة.";

            try {
                $stmt_insert = $pdo->prepare("INSERT INTO applications 
                    (app_number, full_name, gender, national_id, phone, email, address, high_school_branch, seat_number, graduation_year, percentage, department_id, notes, status, admin_reply) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                
                $stmt_insert->execute([
                    $app_number, $full_name, $gender, $national_id, $phone, $email, $address,
                    $high_school_branch, $seat_number, $graduation_year, $percentage, $department_id,
                    $notes, $initial_status, $initial_reply
                ]);

                $success_app = [
                    'app_number' => $app_number,
                    'full_name' => $full_name,
                    'dept_name' => $dept_info['name_ar'],
                    'percentage' => $percentage,
                    'status' => $initial_status,
                    'reply' => $initial_reply
                ];

                set_flash('success', "تم تسجيل طلبك بنجاح برقم استمارة: {$app_number}");
            } catch (PDOException $e) {
                $error = 'حدث خطأ أثناء حفظ الطلب: ' . $e->getMessage();
            }
        }
    }
}

// جلب التخصصات للقائمة المنسدلة
$departments = $pdo->query("SELECT * FROM departments ORDER BY id ASC")->fetchAll();
$selected_dept = isset($_GET['dept']) ? (int)$_GET['dept'] : 0;

require_once __DIR__ . '/includes/header.php';
?>

<!-- هيدر الصفحة والترحيب التشجيعي -->
<section class="bg-gradient-to-b from-blue-900 to-indigo-900 text-white py-14 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <span class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-amber-400 text-blue-950 font-black text-xs shadow-sm mb-3">
            <i class="fa-solid fa-graduation-cap"></i> العام الجامعي 2026 / 2027
        </span>
        <h1 class="text-3xl sm:text-5xl font-black mb-4">بوابة التقديم الإلكتروني للكلية</h1>
        <p class="text-blue-200 text-sm sm:text-base max-w-2xl mx-auto leading-relaxed">
            خطوتك الأولى نحو مستقبل استثنائي تبدأ هنا! املأ استمارة التقديم واحصل فوراً على رقم استمارة القبول لمتابعة حالتك وتثبيت مقعدك الجامعي.
        </p>
    </div>
</section>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <?php if ($success_app): ?>
        <!-- بطاقة نجاح التقديم القابلة للطباعة -->
        <div class="max-w-3xl mx-auto result-certificate rounded-3xl p-8 mb-12 shadow-xl border-2 border-emerald-400 bg-white">
            <div class="text-center pb-6 border-b border-slate-200">
                <div class="w-16 h-16 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-3xl mx-auto mb-3">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">إشعار استلام استمارة التقديم</span>
                <h3 class="text-2xl font-black text-slate-900 mt-1">تهانينا يا <?= htmlspecialchars($success_app['full_name']) ?>! 🎉</h3>
                <p class="text-slate-600 text-sm mt-1">تم تسجيل طلب التحاقك بكلية الأفق بنجاح</p>
            </div>

            <div class="my-6 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm bg-slate-50 p-5 rounded-2xl border border-slate-200">
                <div>
                    <span class="text-slate-400 block text-xs">رقم استمارة التقديم:</span>
                    <span class="font-black text-lg text-blue-600 tracking-wider"><?= $success_app['app_number'] ?></span>
                </div>
                <div>
                    <span class="text-slate-400 block text-xs">التخصص المطلوب:</span>
                    <span class="font-extrabold text-slate-800 text-base">قسم <?= htmlspecialchars($success_app['dept_name']) ?></span>
                </div>
                <div>
                    <span class="text-slate-400 block text-xs">نسبة الثانوية العامة:</span>
                    <span class="font-extrabold text-slate-800"><?= $success_app['percentage'] ?>%</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-xs">حالة الطلب الأولية:</span>
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-800">
                        <?= $success_app['status'] ?>
                    </span>
                </div>
            </div>

            <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-900 text-xs leading-relaxed mb-6">
                <p class="font-bold mb-1"><i class="fa-solid fa-circle-info ml-1 text-blue-600"></i> ملاحظة وتوجيهات لجنة القبول:</p>
                <?= htmlspecialchars($success_app['reply']) ?>
            </div>

            <div class="flex flex-wrap items-center justify-center gap-3 no-print">
                <button onclick="window.print()" class="px-6 py-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs flex items-center gap-2 shadow-sm">
                    <i class="fa-solid fa-print"></i> طباعة إشعار التقديم
                </button>
                <a href="admission_results.php?app=<?= $success_app['app_number'] ?>" class="px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs flex items-center gap-2 shadow-sm">
                    <i class="fa-solid fa-magnifying-glass"></i> تتبع الطلب من صفحة النتائج
                </a>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="max-w-4xl mx-auto mb-8 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm font-bold flex items-center gap-3">
            <i class="fa-solid fa-circle-exclamation text-rose-600 text-xl"></i>
            <span><?= $error ?></span>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        
        <!-- نموذج التقديم الرئيسي (8 أعمدة) -->
        <div class="lg:col-span-8">
            <div class="glass-card rounded-3xl p-8 border border-slate-200">
                <div class="flex items-center justify-between pb-5 border-b border-slate-100 mb-6">
                    <div>
                        <h2 class="text-xl font-black text-slate-900">استمارة الالتحاق للعام الجديد</h2>
                        <p class="text-xs text-slate-500 mt-0.5">يرجى التأكد من دقة البيانات المدخلة ومطابقتها لوثيقة الثانوية</p>
                    </div>
                    <span class="text-xs bg-blue-100 text-blue-800 font-bold px-3 py-1 rounded-full">خطوة واحدة سهلة ⚡</span>
                </div>

                <form method="POST" action="admissions.php" class="space-y-6">
                    
                    <!-- البيانات الشخصية -->
                    <div class="space-y-4">
                        <h4 class="text-sm font-black text-blue-700 flex items-center gap-2 pb-2 border-b border-blue-50">
                            <i class="fa-solid fa-user-circle"></i> 1. البيانات الشخصية للطالب/ـة
                        </h4>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">الاسم الرباعي واللقب (كما في الشهادة) *</label>
                                <input type="text" name="full_name" required placeholder="مثال: أحمد محمد علي السقاف" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 text-sm">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">الجنس *</label>
                                <select name="gender" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:border-blue-600 text-sm bg-white">
                                    <option value="ذكر">ذكر</option>
                                    <option value="أنثى">أنثى</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">رقم الهوية الوطنية / جواز السفر *</label>
                                <input type="text" name="national_id" required placeholder="الرقم الوطني المكون من 10 أرقام" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:border-blue-600 text-sm">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">رقم الهاتف / الواتساب للتواصل *</label>
                                <input type="tel" name="phone" required placeholder="مثال: 771234567" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:border-blue-600 text-sm">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">البريد الإلكتروني *</label>
                                <input type="email" name="email" required placeholder="student@example.com" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:border-blue-600 text-sm">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">عنوان السكن الحالي *</label>
                                <input type="text" name="address" required placeholder="المدينة - الحي - الشارع" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:border-blue-600 text-sm">
                            </div>
                        </div>
                    </div>

                    <!-- بيانات المؤهل الثانوي -->
                    <div class="space-y-4 pt-4">
                        <h4 class="text-sm font-black text-blue-700 flex items-center gap-2 pb-2 border-b border-blue-50">
                            <i class="fa-solid fa-graduation-cap"></i> 2. بيانات المؤهل الثانوي (الثانوية العامة)
                        </h4>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">مسار الثانوية العامة *</label>
                                <select name="high_school_branch" id="branch-select" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:border-blue-600 text-sm bg-white">
                                    <option value="علمي">القسم العلمي (متاح لكل التخصصات)</option>
                                    <option value="أدبي">القسم الأدبي (إدارة، لغات، محاسبة)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">رقم الجلوس في الثانوية *</label>
                                <input type="text" name="seat_number" required placeholder="مثال: 88124" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:border-blue-600 text-sm">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">سنة التخرج *</label>
                                <select name="graduation_year" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:border-blue-600 text-sm bg-white">
                                    <option value="2026">2026 (دفعة هذا العام)</option>
                                    <option value="2025">2025</option>
                                    <option value="2024">2024</option>
                                    <option value="2023">2023 وما قبلها</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">معدل / نسبة الثانوية العامة (%) *</label>
                                <input type="number" step="0.01" min="50" max="100" name="percentage" id="percentage-input" required placeholder="مثال: 85.50" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:border-blue-600 text-sm">
                            </div>
                        </div>
                    </div>

                    <!-- التخصص المرغوب -->
                    <div class="space-y-4 pt-4">
                        <h4 class="text-sm font-black text-blue-700 flex items-center gap-2 pb-2 border-b border-blue-50">
                            <i class="fa-solid fa-shapes"></i> 3. اختيار التخصص الأكاديمي المطلوب
                        </h4>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">اختر القسم / التخصص *</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <?php foreach ($departments as $d): ?>
                                    <label class="flex items-center gap-3 p-3.5 rounded-2xl border border-slate-200 hover:border-blue-500 hover:bg-blue-50/50 cursor-pointer transition-all">
                                        <input type="radio" name="department_id" value="<?= $d['id'] ?>" <?= ($selected_dept == $d['id'] || ($selected_dept == 0 && $d['id'] == 1)) ? 'checked' : '' ?> class="text-blue-600 focus:ring-blue-500 h-4 w-4">
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between">
                                                <span class="font-extrabold text-sm text-slate-900">قسم <?= htmlspecialchars($d['name_ar']) ?></span>
                                                <span class="text-[11px] font-bold text-blue-600"><?= $d['min_percentage'] ?>%+</span>
                                            </div>
                                            <div class="text-[11px] text-slate-400"><?= htmlspecialchars($d['degree']) ?> (<?= $d['track_type'] ?>)</div>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">ملاحظات إضافية أو هوايات متميزة (اختياري)</label>
                            <textarea name="notes" rows="3" placeholder="إذا كنت حاصلاً على جوائز، أو لديك أنشطة تطوعية، أو أسئلة للجنة القبول اكتبها هنا..." class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:border-blue-600 text-sm"></textarea>
                        </div>
                    </div>

                    <!-- زر الإرسال -->
                    <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="text-xs text-slate-500 flex items-center gap-2">
                            <i class="fa-solid fa-shield-check text-emerald-500 text-base"></i>
                            <span>جميع بياناتك مشفرة ومحمية بموجب معايير خصوصية الطالب.</span>
                        </div>
                        <button type="submit" name="submit_application" class="w-full sm:w-auto px-10 py-4 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-black text-sm shadow-xl shadow-blue-500/20 hover:scale-105 transition-all flex items-center justify-center gap-2">
                            <i class="fa-solid fa-paper-plane"></i>
                            <span>إرسال استمارة التقديم الآن</span>
                        </button>
                    </div>

                </form>
            </div>
        </div>

        <!-- الشريط الجانبي (الشروط، المستندات، المنح) 4 أعمدة -->
        <div class="lg:col-span-4 space-y-6">
            
            <!-- بطاقة تشجيعية جانبية -->
            <div class="p-6 rounded-3xl bg-gradient-to-br from-amber-400 to-amber-500 text-slate-950 shadow-md">
                <span class="text-xs font-black uppercase tracking-wider bg-black/10 px-2.5 py-1 rounded-full">نصيحة للمتقدمين</span>
                <h4 class="text-lg font-black mt-2 mb-1">أنت تصنع خطوتك الأولى! 🚀</h4>
                <p class="text-xs leading-relaxed font-semibold opacity-90">
                    لا تتردد في اختيار التخصص الذي يلهب شغفك ويخدم مجتمعك. طلاب كلية الأفق يحظون بفرص تدريب ميداني متميزة منذ السنة الثانية!
                </p>
            </div>

            <!-- بطاقة الوثائق المطلوبة -->
            <div class="glass-card rounded-3xl p-6 border border-slate-200">
                <h4 class="font-black text-slate-900 text-base mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-folder-open text-blue-600"></i> الوثائق المطلوبة للمطابقة
                </h4>
                <ul class="space-y-3 text-xs text-slate-600">
                    <li class="flex items-start gap-2">
                        <i class="fa-solid fa-check text-emerald-500 mt-0.5"></i>
                        <span>أصل استمارة الثانوية العامة مع 4 صور طبق الأصل.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fa-solid fa-check text-emerald-500 mt-0.5"></i>
                        <span>صورة واضحة من البطاقة الشخصية أو جواز السفر أو بطاقة ولي الأمر.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fa-solid fa-check text-emerald-500 mt-0.5"></i>
                        <span>عدد 6 صور شمسية حديثة خلفية بيضاء مقاس 4×6.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fa-solid fa-check text-emerald-500 mt-0.5"></i>
                        <span>استمارة التقديم الإلكترونية ورقم الطلب.</span>
                    </li>
                </ul>
            </div>

            <!-- بطاقة المنح والتخفيضات -->
            <div class="glass-card rounded-3xl p-6 border border-slate-200">
                <h4 class="font-black text-slate-900 text-base mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-award text-amber-500"></i> المنح وخصومات التفوق
                </h4>
                <div class="space-y-2.5 text-xs text-slate-600">
                    <div class="p-2.5 rounded-xl bg-emerald-50 text-emerald-900 flex justify-between font-bold">
                        <span>أوائل المحافظات في الثانوية:</span>
                        <span class="text-emerald-700">خصم 30%</span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-blue-50 text-blue-900 flex justify-between font-bold">
                        <span>معدل 90% فأعلى:</span>
                        <span class="text-blue-700">خصم 20%</span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-amber-50 text-amber-900 flex justify-between font-bold">
                        <span>خصم الإخوة والأقارب:</span>
                        <span class="text-amber-700">خصم 15%</span>
                    </div>
                </div>
            </div>

            <!-- زر الاستعلام السريع عن طلب سابق -->
            <div class="p-5 rounded-2xl bg-slate-100 border border-slate-200 text-center">
                <p class="text-xs text-slate-600 font-bold mb-3">هل قمت بالتقديم مسبقاً وتريد معرفة حالتك؟</p>
                <a href="admission_results.php" class="inline-block w-full py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition-all">
                    الاستعلام عن نتيجة القبول
                </a>
            </div>

        </div>

    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
