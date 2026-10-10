<?php
$page_title = 'النشاط الطلابي والمبادرات والبطولات';
require_once __DIR__ . '/config/db.php';

$cat_filter = clean_input($_GET['cat'] ?? 'all');

// معالجة تسجيل الطالب في نشاط
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup_activity'])) {
    $activity_id = (int)($_POST['activity_id'] ?? 0);
    $student_name = clean_input($_POST['student_name'] ?? '');
    $student_id = clean_input($_POST['student_id'] ?? '');
    $phone = clean_input($_POST['phone'] ?? '');
    $department = clean_input($_POST['department'] ?? '');
    $notes = clean_input($_POST['notes'] ?? '');

    if ($activity_id > 0 && !empty($student_name) && !empty($student_id) && !empty($phone)) {
        try {
            $stmt_reg = $pdo->prepare("INSERT INTO activity_signups (activity_id, student_name, student_id, phone, department, notes) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt_reg->execute([$activity_id, $student_name, $student_id, $phone, $department, $notes]);
            
            // تحديث عدد المسجلين
            $pdo->prepare("UPDATE activities SET registered_count = registered_count + 1 WHERE id = ?")->execute([$activity_id]);

            set_flash('success', "تم تسجيل مشاركتك بنجاح في النشاط! ستتواصل معك عمادة شؤون الطلاب عبر الواتساب.");
        } catch (PDOException $e) {
            set_flash('error', "حدث خطأ أثناء التسجيل: " . $e->getMessage());
        }
    } else {
        set_flash('error', "يرجى تعبئة كافة الحقول المطلوبة للانضمام إلى النشاط.");
    }
    header("Location: activities.php?cat={$cat_filter}");
    exit;
}

// جلب الأنشطة حسب الفئة
$sql = "SELECT * FROM activities WHERE 1=1";
$params = [];
if ($cat_filter !== 'all') {
    $sql .= " AND category = ?";
    $params[] = $cat_filter;
}
$sql .= " ORDER BY event_date ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$activities = $stmt->fetchAll();

// جلب مباريات دوري كرة القدم
$matches = $pdo->query("SELECT * FROM football_matches ORDER BY match_date ASC")->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<!-- هيدر الصفحة والتحفيز الطلابي -->
<section class="bg-gradient-to-b from-blue-900 to-indigo-900 text-white py-14 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <span class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-emerald-400 text-blue-950 font-black text-xs shadow-sm mb-3">
            <i class="fa-solid fa-sparkles"></i> روح الشباب وإبداع الأفق
        </span>
        <h1 class="text-3xl sm:text-5xl font-black mb-4">النشاط الطلابي والمبادرات والبطولات</h1>
        <p class="text-blue-200 text-sm sm:text-base max-w-3xl mx-auto leading-relaxed">
            التعليم لا يقتصر على قاعات المحاضرات! انضم إلى مبادراتنا الإنسانية، بطولات خماسيات كرة القدم، معارض الأيام الثقافية، والحملات الصحية التوعوية واصنع ذكريات جامعية لا تُنسى.
        </p>

        <!-- أزرار الفلترة الخمسة المحددة بالطلب -->
        <div class="flex flex-wrap justify-center gap-2 mt-8">
            <a href="activities.php?cat=all" class="px-4 py-2.5 rounded-xl font-extrabold text-xs transition-all <?= $cat_filter == 'all' ? 'bg-white text-blue-950 shadow-lg scale-105' : 'bg-white/10 text-white hover:bg-white/20' ?>">
                الكل (جميع الفعاليات)
            </a>
            <a href="activities.php?cat=<?= urlencode('مبادرات طلابية') ?>" class="px-4 py-2.5 rounded-xl font-extrabold text-xs transition-all <?= $cat_filter == 'مبادرات طلابية' ? 'bg-white text-blue-950 shadow-lg scale-105' : 'bg-white/10 text-white hover:bg-white/20' ?>">
                <i class="fa-solid fa-hand-holding-heart ml-1 text-emerald-400"></i> المبادرات الطلابية
            </a>
            <a href="activities.php?cat=<?= urlencode('دورات كرة القدم') ?>" class="px-4 py-2.5 rounded-xl font-extrabold text-xs transition-all <?= $cat_filter == 'دورات كرة القدم' ? 'bg-white text-blue-950 shadow-lg scale-105' : 'bg-white/10 text-white hover:bg-white/20' ?>">
                <i class="fa-solid fa-futbol ml-1 text-amber-400"></i> دورات كرة القدم
            </a>
            <a href="activities.php?cat=<?= urlencode('الأيام الثقافية') ?>" class="px-4 py-2.5 rounded-xl font-extrabold text-xs transition-all <?= $cat_filter == 'الأيام الثقافية' ? 'bg-white text-blue-950 shadow-lg scale-105' : 'bg-white/10 text-white hover:bg-white/20' ?>">
                <i class="fa-solid fa-masks-theater ml-1 text-purple-400"></i> الأيام الثقافية
            </a>
            <a href="activities.php?cat=<?= urlencode('الحملات التوعوية') ?>" class="px-4 py-2.5 rounded-xl font-extrabold text-xs transition-all <?= $cat_filter == 'الحملات التوعوية' ? 'bg-white text-blue-950 shadow-lg scale-105' : 'bg-white/10 text-white hover:bg-white/20' ?>">
                <i class="fa-solid fa-bullhorn ml-1 text-sky-400"></i> الحملات التوعوية
            </a>
            <a href="activities.php?cat=<?= urlencode('المشاركات الطلابية') ?>" class="px-4 py-2.5 rounded-xl font-extrabold text-xs transition-all <?= $cat_filter == 'المشاركات الطلابية' ? 'bg-white text-blue-950 shadow-lg scale-105' : 'bg-white/10 text-white hover:bg-white/20' ?>">
                <i class="fa-solid fa-users-rays ml-1 text-rose-400"></i> المشاركات الطلابية
            </a>
        </div>
    </div>
</section>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 space-y-16">

    <!-- قسم دورة كرة القدم المميز (إذا كان الفلتر يشمله) -->
    <?php if ($cat_filter === 'all' || $cat_filter === 'دورات كرة القدم'): ?>
        <div id="football" class="rounded-3xl bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white p-8 lg:p-10 shadow-2xl relative overflow-hidden border border-slate-800">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-amber-400 text-slate-950 flex items-center justify-center text-2xl font-black shadow-lg">
                        <i class="fa-solid fa-trophy"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl sm:text-3xl font-black">بطولة كأس الأفق لخماسيات كرة القدم 2026</h2>
                        <p class="text-amber-200 text-xs sm:text-sm font-semibold">بمشاركة فرق أقسام التمريض، المختبرات، تقنية المعلومات، الإدارة، اللغات، والمحاسبة</p>
                    </div>
                </div>
                <span class="bg-red-500 text-white text-xs px-3 py-1 rounded-full font-bold animate-pulse">مباريات حماسية ⚽</span>
            </div>

            <!-- جدول المباريات -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <?php foreach ($matches as $m): ?>
                    <div class="bg-white/10 backdrop-blur-md rounded-2xl p-5 border border-white/10 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between text-xs text-slate-300 mb-3">
                                <span class="bg-white/15 px-2.5 py-0.5 rounded-md font-bold"><?= htmlspecialchars($m['stage']) ?></span>
                                <span class="text-amber-300 font-bold"><?= date('d M - H:i', strtotime($m['match_date'])) ?></span>
                            </div>

                            <div class="flex items-center justify-between text-sm sm:text-base font-extrabold my-3">
                                <div class="text-right flex-1 truncate"><?= htmlspecialchars($m['team_a']) ?></div>
                                <div class="px-3 py-1 mx-2 rounded-lg bg-black/50 text-amber-300 font-mono text-base font-black">
                                    <?= $m['score_a'] !== null ? $m['score_a'] . ' - ' . $m['score_b'] : 'VS' ?>
                                </div>
                                <div class="text-left flex-1 truncate"><?= htmlspecialchars($m['team_b']) ?></div>
                            </div>

                            <?php if (!empty($m['highlights'])): ?>
                                <p class="text-[11px] text-slate-300 bg-black/20 p-2.5 rounded-xl mt-2 leading-relaxed">
                                    <i class="fa-solid fa-circle-info text-amber-400 ml-1"></i> <?= htmlspecialchars($m['highlights']) ?>
                                </p>
                            <?php endif; ?>
                        </div>

                        <div class="mt-4 pt-3 border-t border-white/10 flex items-center justify-between text-xs text-slate-400">
                            <span><i class="fa-solid fa-location-dot ml-1 text-rose-400"></i> <?= htmlspecialchars($m['stadium']) ?></span>
                            <span class="font-bold <?= $m['status'] == 'انتهت' ? 'text-emerald-400' : 'text-blue-300' ?>"><?= htmlspecialchars($m['status']) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- شبكة الأنشطة والمبادرات والأيام الثقافية والحملات -->
    <div>
        <div class="flex items-center justify-between mb-8">
            <div>
                <h3 class="text-2xl font-black text-slate-900">سجل الأنشطة والبرامج الطلابية</h3>
                <p class="text-slate-500 text-xs sm:text-sm mt-1">اختر النشاط واضغط "انضم إلى النشاط" للمشاركة الفاعلة وحصد شهادات التميز.</p>
            </div>
            <span class="text-xs font-bold text-slate-400 bg-slate-100 px-3 py-1.5 rounded-xl"><?= count($activities) ?> نشاط متاح</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($activities as $act): ?>
                <div id="act-<?= $act['id'] ?>" class="glass-card rounded-3xl p-7 border border-slate-200 flex flex-col justify-between scroll-mt-24">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-xs font-black px-3 py-1 rounded-full <?= $act['category'] == 'مبادرات طلابية' ? 'bg-emerald-100 text-emerald-800' : ($act['category'] == 'الأيام الثقافية' ? 'bg-purple-100 text-purple-800' : ($act['category'] == 'الحملات التوعوية' ? 'bg-sky-100 text-sky-800' : 'bg-amber-100 text-amber-800')) ?>">
                                <?= htmlspecialchars($act['category']) ?>
                            </span>
                            <span class="text-xs font-bold text-slate-400 flex items-center gap-1">
                                <i class="fa-regular fa-calendar ml-1"></i>
                                <?= format_date_ar($act['event_date']) ?>
                            </span>
                        </div>

                        <h4 class="text-lg font-black text-slate-900 mb-2 leading-snug">
                            <?= htmlspecialchars($act['title']) ?>
                        </h4>

                        <div class="text-xs font-bold text-blue-600 mb-3 flex items-center gap-1">
                            <i class="fa-solid fa-users ml-1"></i> الجهة المنظمة: <?= htmlspecialchars($act['organizer']) ?>
                        </div>

                        <p class="text-slate-600 text-xs sm:text-sm leading-relaxed mb-4">
                            <?= htmlspecialchars($act['description']) ?>
                        </p>

                        <?php if (!empty($act['goals'])): ?>
                            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-700 mb-4">
                                <span class="font-bold text-slate-900 block mb-1"><i class="fa-solid fa-bullseye text-rose-500 ml-1"></i> الأهداف والمخرجات:</span>
                                <?= htmlspecialchars($act['goals']) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div>
                        <!-- شريط سعة المسجلين -->
                        <div class="py-3 border-t border-slate-100 text-xs text-slate-500 mb-4">
                            <div class="flex justify-between font-bold mb-1">
                                <span>المشاركون: <?= $act['registered_count'] ?> طالب/ـة</span>
                                <span>السعة: <?= $act['max_capacity'] ?></span>
                            </div>
                            <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: <?= min(100, round(($act['registered_count'] / $act['max_capacity']) * 100)) ?>%;"></div>
                            </div>
                        </div>

                        <!-- زر الانضمام -->
                        <button onclick="openSignupModal(<?= $act['id'] ?>, '<?= addslashes(htmlspecialchars($act['title'])) ?>')" class="w-full py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs shadow-md transition-all flex items-center justify-center gap-2">
                            <i class="fa-solid fa-user-plus"></i>
                            <span>سجل الآن في هذا النشاط</span>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

</div>

<!-- نافذة التسجيل المنبثقة في النشاط (Modal) -->
<div id="signup-modal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 animate-scale-up">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
            <div>
                <h3 class="text-lg font-black text-slate-900">الانضمام للنشاط الطلابي</h3>
                <p id="modal-activity-title" class="text-xs text-blue-600 font-bold mt-0.5 truncate max-w-xs"></p>
            </div>
            <button onclick="closeSignupModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form method="POST" action="activities.php" class="space-y-4">
            <input type="hidden" name="activity_id" id="modal-activity-id">

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">اسم الطالب الرباعي *</label>
                <input type="text" name="student_name" required placeholder="أحمد محمد علي" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-blue-600">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">رقم القيد الجامعي *</label>
                    <input type="text" name="student_id" required placeholder="STD-2026-101" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-blue-600">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">رقم الواتساب *</label>
                    <input type="tel" name="phone" required placeholder="771234567" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-blue-600">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">القسم / التخصص *</label>
                <select name="department" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-blue-600 bg-white">
                    <option value="تقنية معلومات">تقنية معلومات</option>
                    <option value="تمريض">تمريض</option>
                    <option value="طب ومختبرات">طب ومختبرات</option>
                    <option value="إدارة أعمال">إدارة أعمال</option>
                    <option value="لغات إنجليزي">لغات إنجليزي</option>
                    <option value="نظم معلومات محاسبية">نظم معلومات محاسبية</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">ملاحظات أو مهارات خاصة (اختياري)</label>
                <input type="text" name="notes" placeholder="لاعب هجوم، متحدث في المناظرات، مصمم..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-blue-600">
            </div>

            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="closeSignupModal()" class="px-5 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-bold text-xs">إلغاء</button>
                <button type="submit" name="signup_activity" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs shadow-md">تأكيد التسجيل</button>
            </div>
        </form>
    </div>
</div>

<script>
function openSignupModal(id, title) {
    document.getElementById('modal-activity-id').value = id;
    document.getElementById('modal-activity-title').innerText = title;
    const modal = document.getElementById('signup-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}
function closeSignupModal() {
    const modal = document.getElementById('signup-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
