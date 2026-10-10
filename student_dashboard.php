<?php
$page_title = 'بوابة الطالب - لوحة التحكم الأكاديمية';
require_once __DIR__ . '/config/db.php';
require_login();

// التحقق من أن المستخدم طالب أو مدير
$user = current_user();
$student_id = $user['student_id'] ?? 'STD-2026-101';

// جلب تفاصيل قسم الطالب
$stmt_dept = $pdo->prepare("SELECT * FROM departments WHERE id = ?");
$stmt_dept->execute([$user['department_id'] ?? 1]);
$my_dept = $stmt_dept->fetch();

// جلب نتائج الامتحانات الخاصة بالطالب
$stmt_res = $pdo->prepare("SELECT * FROM exam_results WHERE student_id = ? ORDER BY course_code ASC");
$stmt_res->execute([$student_id]);
$my_results = $stmt_res->fetchAll();

// حساب المعدل
$total_credits = 0;
$total_points = 0;
foreach ($my_results as $r) {
    $total_credits += $r['credit_hours'];
    $total_points += ($r['gpa_points'] * $r['credit_hours']);
}
$my_gpa = $total_credits > 0 ? round($total_points / $total_credits, 2) : 3.85;

// جلب مشاركات الطالب في الأنشطة
$stmt_act = $pdo->prepare("SELECT s.*, a.title AS act_title, a.category, a.event_date, a.location 
    FROM activity_signups s 
    JOIN activities a ON s.activity_id = a.id 
    WHERE s.student_id = ? OR s.student_name = ?
    ORDER BY s.id DESC");
$stmt_act->execute([$student_id, $user['full_name']]);
$my_activities = $stmt_act->fetchAll();

// جلب شكاوى ومقترحات الطالب
$stmt_comp = $pdo->prepare("SELECT * FROM complaints WHERE student_id = ? OR student_name = ? ORDER BY id DESC");
$stmt_comp->execute([$student_id, $user['full_name']]);
$my_complaints = $stmt_comp->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<!-- هيدر البوابة والترحيب بالطالب -->
<section class="bg-gradient-to-b from-blue-900 to-indigo-900 text-white py-12 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <div class="w-20 h-20 rounded-3xl bg-white/10 backdrop-blur-md border-2 border-white/20 flex items-center justify-center text-4xl shadow-xl">
                    🎓
                </div>
                <div>
                    <span class="inline-block px-3 py-1 rounded-full bg-emerald-400 text-blue-950 font-black text-xs mb-2">
                        طالب منتظم - العام الجامعي 2026 / 2027
                    </span>
                    <h1 class="text-2xl sm:text-3xl font-black">مرحباً بك يا <?= htmlspecialchars($user['full_name']) ?>! 🌟</h1>
                    <p class="text-blue-200 text-xs sm:text-sm mt-1">
                        رقم القيد: <b class="text-white font-mono"><?= htmlspecialchars($student_id) ?></b> | 
                        القسم: <b class="text-amber-300">قسم <?= htmlspecialchars($my_dept['name_ar'] ?? 'تقنية معلومات') ?></b>
                    </p>
                </div>
            </div>

            <!-- بطاقة المعدل الفصلي GPA -->
            <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-4 sm:p-5 text-center flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-amber-400 text-blue-950 flex items-center justify-center text-2xl font-black shadow-md">
                    <i class="fa-solid fa-award"></i>
                </div>
                <div class="text-right">
                    <div class="text-xs text-blue-200 font-bold">المعدل التراكمي (GPA)</div>
                    <div class="text-2xl font-black text-white font-mono"><?= $my_gpa ?> / 4.00</div>
                    <div class="text-[11px] text-emerald-300 font-bold">تقدير: ممتاز مع مرتبة الشرف ✨</div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-12">

    <!-- كروت الاختصارات السريعة للطالب -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <a href="exam_results.php?student_id=<?= urlencode($student_id) ?>" class="glass-card rounded-3xl p-6 border border-slate-200 flex items-center gap-4 hover:border-blue-300 group">
            <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-square-poll-vertical"></i>
            </div>
            <div>
                <div class="text-xs text-slate-400 font-bold">كشف الدرجات</div>
                <div class="text-sm font-black text-slate-900">نتائج الامتحانات</div>
            </div>
        </a>

        <a href="activities.php" class="glass-card rounded-3xl p-6 border border-slate-200 flex items-center gap-4 hover:border-emerald-300 group">
            <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-futbol"></i>
            </div>
            <div>
                <div class="text-xs text-slate-400 font-bold">المبادرات والبطولات</div>
                <div class="text-sm font-black text-slate-900">النشاط الطلابي</div>
            </div>
        </a>

        <a href="calendar.php" class="glass-card rounded-3xl p-6 border border-slate-200 flex items-center gap-4 hover:border-amber-300 group">
            <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                <i class="fa-regular fa-calendar-check"></i>
            </div>
            <div>
                <div class="text-xs text-slate-400 font-bold">الجداول والمواعيد</div>
                <div class="text-sm font-black text-slate-900">التقويم الدراسي</div>
            </div>
        </a>

        <a href="complaints.php" class="glass-card rounded-3xl p-6 border border-slate-200 flex items-center gap-4 hover:border-rose-300 group">
            <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-comment-dots"></i>
            </div>
            <div>
                <div class="text-xs text-slate-400 font-bold">صوتك مسموع</div>
                <div class="text-sm font-black text-slate-900">شكوى أو مقترح</div>
            </div>
        </a>
    </div>

    <!-- جدول نتائج الامتحانات والمقررات المسجلة -->
    <div class="glass-card rounded-3xl p-6 sm:p-8 border border-slate-200">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
            <div>
                <h3 class="text-lg font-black text-slate-900">المقررات الأكاديمية والدرجات المرصودة</h3>
                <p class="text-xs text-slate-500 mt-0.5">الفصل الدراسي الثاني - العام 2025/2026</p>
            </div>
            <a href="exam_results.php?student_id=<?= urlencode($student_id) ?>" class="text-xs font-bold text-blue-600 hover:underline">
                طباعة كشف الدرجات الرسمي ←
            </a>
        </div>

        <?php if (!empty($my_results)): ?>
            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 font-bold border-b border-slate-200">
                            <th class="p-3.5 rounded-r-xl">رمز المقرر</th>
                            <th class="p-3.5">اسم المقرر</th>
                            <th class="p-3.5">الساعات</th>
                            <th class="p-3.5">أعمال الفصل</th>
                            <th class="p-3.5">النهائي</th>
                            <th class="p-3.5">المجموع</th>
                            <th class="p-3.5 rounded-l-xl">التقدير</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($my_results as $res): ?>
                            <tr class="hover:bg-blue-50/40 transition-colors">
                                <td class="p-3.5 font-mono font-bold text-slate-600"><?= htmlspecialchars($res['course_code']) ?></td>
                                <td class="p-3.5 font-extrabold text-slate-900"><?= htmlspecialchars($res['course_name']) ?></td>
                                <td class="p-3.5 font-bold"><?= $res['credit_hours'] ?></td>
                                <td class="p-3.5 text-slate-600"><?= $res['coursework_grade'] ?></td>
                                <td class="p-3.5 text-slate-600"><?= $res['final_grade'] ?></td>
                                <td class="p-3.5 font-black text-blue-700"><?= $res['total_grade'] ?>%</td>
                                <td class="p-3.5">
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                        <?= htmlspecialchars($res['letter_grade']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-8 text-slate-500 text-xs">
                لا توجد نتائج مسجلة لهذا الفصل حتى الآن، وسيتم رفعها عند اعتمادها من الكنترول.
            </div>
        <?php endif; ?>
    </div>

    <!-- قسم الأنشطة المسجلة والشكاوى المرسلة -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- مشاركاتي في الأنشطة -->
        <div class="glass-card rounded-3xl p-6 sm:p-7 border border-slate-200 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                    <h4 class="font-black text-slate-900 text-base flex items-center gap-2">
                        <i class="fa-solid fa-trophy text-amber-500"></i> مشاركاتي في الأنشطة الطلابية
                    </h4>
                    <span class="text-xs bg-amber-100 text-amber-800 font-bold px-2.5 py-0.5 rounded-full"><?= count($my_activities) ?> مشاركات</span>
                </div>

                <?php if (!empty($my_activities)): ?>
                    <div class="space-y-3">
                        <?php foreach ($my_activities as $act): ?>
                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between text-xs">
                                <div>
                                    <h5 class="font-bold text-slate-900"><?= htmlspecialchars($act['act_title']) ?></h5>
                                    <span class="text-[11px] text-slate-400"><?= format_date_ar($act['event_date']) ?> - <?= htmlspecialchars($act['location']) ?></span>
                                </div>
                                <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px]">مقبول ومسجل</span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-6 text-slate-400 text-xs">
                        لم تشارك في أي نشاط بعد. بادر بالتسجيل في بطولة خماسيات كرة القدم أو المبادرات التطوعية!
                    </div>
                <?php endif; ?>
            </div>

            <div class="pt-4 mt-4 border-t border-slate-100 text-center">
                <a href="activities.php" class="inline-block px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs">
                    تصفح جميع الأنشطة المتاحة للانضمام
                </a>
            </div>
        </div>

        <!-- تذاكر الشكاوى والمقترحات التي أرسلتها -->
        <div class="glass-card rounded-3xl p-6 sm:p-7 border border-slate-200 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                    <h4 class="font-black text-slate-900 text-base flex items-center gap-2">
                        <i class="fa-solid fa-envelope-open-text text-blue-600"></i> مقترحاتي وشكاوى تذاكري
                    </h4>
                    <span class="text-xs bg-blue-100 text-blue-800 font-bold px-2.5 py-0.5 rounded-full"><?= count($my_complaints) ?> تذاكر</span>
                </div>

                <?php if (!empty($my_complaints)): ?>
                    <div class="space-y-3">
                        <?php foreach ($my_complaints as $c): ?>
                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 space-y-1 text-xs">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-slate-900"><?= htmlspecialchars($c['title']) ?></span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold <?= $c['status'] == 'تم الحل والرد' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' ?>">
                                        <?= htmlspecialchars($c['status']) ?>
                                    </span>
                                </div>
                                <div class="text-[11px] text-slate-400 font-mono"><?= htmlspecialchars($c['ticket_number']) ?> - <?= $c['created_at'] ?></div>
                                <?php if (!empty($c['admin_response'])): ?>
                                    <div class="p-2 rounded-lg bg-emerald-50 text-emerald-900 text-[11px] mt-2">
                                        <b>رد الإدارة:</b> <?= htmlspecialchars($c['admin_response']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-6 text-slate-400 text-xs">
                        لا توجد تذاكر مرسلة حالياً. هل لديك فكرة أو مقترح لتطوير الكلية؟
                    </div>
                <?php endif; ?>
            </div>

            <div class="pt-4 mt-4 border-t border-slate-100 text-center">
                <a href="complaints.php" class="inline-block px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-sm">
                    إرسال مقترح أو ملاحظة جديدة
                </a>
            </div>
        </div>

    </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
