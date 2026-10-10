<?php
$page_title = 'لوحة تحكم إدارة الكلية ومدير النظام';
require_once __DIR__ . '/config/db.php';
require_admin();

$active_tab = clean_input($_GET['tab'] ?? 'applications');

// 1. معالجة تحديث حالة طلب القبول
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_app_status'])) {
    $app_id = (int)$_POST['app_id'];
    $new_status = clean_input($_POST['status']);
    $admin_reply = clean_input($_POST['admin_reply']);
    $interview_date = !empty($_POST['interview_date']) ? $_POST['interview_date'] : null;

    $stmt_up = $pdo->prepare("UPDATE applications SET status = ?, admin_reply = ?, interview_date = ? WHERE id = ?");
    $stmt_up->execute([$new_status, $admin_reply, $interview_date, $app_id]);
    set_flash('success', 'تم تحديث حالة طلب القبول وإشعار الطالب بنجاح.');
    header("Location: admin_dashboard.php?tab=applications");
    exit;
}

// 2. معالجة إضافة نتيجة امتحان جديدة
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_exam_result'])) {
    $student_id = clean_input($_POST['student_id']);
    $student_name = clean_input($_POST['student_name']);
    $seat_number = clean_input($_POST['seat_number']);
    $semester = clean_input($_POST['semester']);
    $department_id = (int)$_POST['department_id'];
    $course_code = clean_input($_POST['course_code']);
    $course_name = clean_input($_POST['course_name']);
    $credit_hours = (int)$_POST['credit_hours'];
    $coursework_grade = (float)$_POST['coursework_grade'];
    $final_grade = (float)$_POST['final_grade'];

    $total_grade = $coursework_grade + $final_grade;
    $letter = 'F';
    $points = 0.00;
    if ($total_grade >= 90) { $letter = 'A+'; $points = 4.00; }
    elseif ($total_grade >= 85) { $letter = 'A'; $points = 3.75; }
    elseif ($total_grade >= 80) { $letter = 'B+'; $points = 3.50; }
    elseif ($total_grade >= 75) { $letter = 'B'; $points = 3.00; }
    elseif ($total_grade >= 70) { $letter = 'C+'; $points = 2.50; }
    elseif ($total_grade >= 65) { $letter = 'C'; $points = 2.00; }
    elseif ($total_grade >= 60) { $letter = 'D'; $points = 1.50; }

    $stmt_ex = $pdo->prepare("INSERT INTO exam_results (student_id, student_name, seat_number, semester, department_id, course_code, course_name, credit_hours, coursework_grade, final_grade, total_grade, letter_grade, gpa_points) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt_ex->execute([$student_id, $student_name, $seat_number, $semester, $department_id, $course_code, $course_name, $credit_hours, $coursework_grade, $final_grade, $total_grade, $letter, $points]);

    set_flash('success', "تم رصد نتيجة المقرر {$course_name} للطالب {$student_name} بنجاح.");
    header("Location: admin_dashboard.php?tab=results");
    exit;
}

// 3. معالجة الرد على الشكاوى والمقترحات
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_complaint'])) {
    $comp_id = (int)$_POST['complaint_id'];
    $response = clean_input($_POST['admin_response']);
    $comp_status = clean_input($_POST['status']);

    $stmt_rep = $pdo->prepare("UPDATE complaints SET admin_response = ?, status = ?, response_date = NOW() WHERE id = ?");
    $stmt_rep->execute([$response, $comp_status, $comp_id]);
    set_flash('success', 'تم إرسال رد الإدارة واعتماد الإجراء بنجاح.');
    header("Location: admin_dashboard.php?tab=complaints");
    exit;
}

// 4. معالجة إضافة نشاط جديد
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_activity'])) {
    $title = clean_input($_POST['title']);
    $category = clean_input($_POST['category']);
    $organizer = clean_input($_POST['organizer']);
    $event_date = $_POST['event_date'];
    $location = clean_input($_POST['location']);
    $description = clean_input($_POST['description']);
    $goals = clean_input($_POST['goals']);

    $stmt_ac = $pdo->prepare("INSERT INTO activities (title, category, organizer, event_date, location, description, goals) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt_ac->execute([$title, $category, $organizer, $event_date, $location, $description, $goals]);
    set_flash('success', 'تمت إضافة الفعالية الطلابية بنجاح إلى جدول الأنشطة.');
    header("Location: admin_dashboard.php?tab=activities");
    exit;
}

// جلب الإحصائيات
$stat_apps = $pdo->query("SELECT COUNT(*) FROM applications")->fetchColumn();
$stat_accepted = $pdo->query("SELECT COUNT(*) FROM applications WHERE status IN ('مقبول نهائياً', 'مقبول مبدئياً')")->fetchColumn();
$stat_pending = $pdo->query("SELECT COUNT(*) FROM applications WHERE status = 'قيد المراجعة'")->fetchColumn();
$stat_complaints = $pdo->query("SELECT COUNT(*) FROM complaints WHERE status != 'تم الحل والرد'")->fetchColumn();

// جلب القوائم
$applications = $pdo->query("SELECT a.*, d.name_ar AS dept_name FROM applications a JOIN departments d ON a.department_id = d.id ORDER BY a.id DESC")->fetchAll();
$all_results = $pdo->query("SELECT er.*, d.name_ar AS dept_name FROM exam_results er JOIN departments d ON er.department_id = d.id ORDER BY er.id DESC LIMIT 20")->fetchAll();
$all_complaints = $pdo->query("SELECT * FROM complaints ORDER BY id DESC")->fetchAll();
$all_activities = $pdo->query("SELECT * FROM activities ORDER BY id DESC")->fetchAll();
$departments = $pdo->query("SELECT id, name_ar FROM departments ORDER BY id ASC")->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<!-- هيدر لوحة الإدارة -->
<section class="bg-gradient-to-r from-slate-900 via-indigo-950 to-purple-950 text-white py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-purple-600 text-white flex items-center justify-center text-3xl shadow-lg">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div>
                    <span class="bg-purple-500/30 text-purple-200 text-xs px-2.5 py-0.5 rounded-full font-bold border border-purple-400/30">لوحة الإدارة المركزية</span>
                    <h1 class="text-2xl sm:text-3xl font-black mt-1">نظام إدارة كلية الأفق والكنترول</h1>
                    <p class="text-slate-400 text-xs">مرحباً بك، د. منصور الأحمدي | صلاحيات الإدارة الكاملة</p>
                </div>
            </div>

            <!-- إحصائيات سريعة في الهيدر -->
            <div class="flex items-center gap-3 text-xs">
                <div class="bg-white/10 px-4 py-2 rounded-xl text-center">
                    <div class="font-black text-amber-300 text-base"><?= $stat_apps ?></div>
                    <div class="text-slate-300 text-[10px]">طلبات القبول</div>
                </div>
                <div class="bg-white/10 px-4 py-2 rounded-xl text-center">
                    <div class="font-black text-emerald-400 text-base"><?= $stat_accepted ?></div>
                    <div class="text-slate-300 text-[10px]">المقبولون</div>
                </div>
                <div class="bg-white/10 px-4 py-2 rounded-xl text-center">
                    <div class="font-black text-rose-400 text-base"><?= $stat_complaints ?></div>
                    <div class="text-slate-300 text-[10px]">شكاوى معلقة</div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <!-- شريط التبويبات الرئيسي -->
    <div class="flex flex-wrap border-b border-slate-200 gap-2 mb-8 text-xs sm:text-sm font-bold">
        <a href="admin_dashboard.php?tab=applications" class="px-5 py-3 rounded-t-2xl transition-all flex items-center gap-2 <?= $active_tab == 'applications' ? 'bg-white text-blue-600 border-t-2 border-r border-l border-blue-600 shadow-sm' : 'text-slate-600 hover:bg-slate-100' ?>">
            <i class="fa-solid fa-users"></i>
            <span>طلبات القبول (<?= count($applications) ?>)</span>
        </a>
        <a href="admin_dashboard.php?tab=results" class="px-5 py-3 rounded-t-2xl transition-all flex items-center gap-2 <?= $active_tab == 'results' ? 'bg-white text-blue-600 border-t-2 border-r border-l border-blue-600 shadow-sm' : 'text-slate-600 hover:bg-slate-100' ?>">
            <i class="fa-solid fa-square-poll-vertical"></i>
            <span>رصد الامتحانات</span>
        </a>
        <a href="admin_dashboard.php?tab=complaints" class="px-5 py-3 rounded-t-2xl transition-all flex items-center gap-2 <?= $active_tab == 'complaints' ? 'bg-white text-blue-600 border-t-2 border-r border-l border-blue-600 shadow-sm' : 'text-slate-600 hover:bg-slate-100' ?>">
            <i class="fa-solid fa-comment-dots"></i>
            <span>الشكاوى والمقترحات (<?= count($all_complaints) ?>)</span>
        </a>
        <a href="admin_dashboard.php?tab=activities" class="px-5 py-3 rounded-t-2xl transition-all flex items-center gap-2 <?= $active_tab == 'activities' ? 'bg-white text-blue-600 border-t-2 border-r border-l border-blue-600 shadow-sm' : 'text-slate-600 hover:bg-slate-100' ?>">
            <i class="fa-solid fa-trophy"></i>
            <span>الأنشطة والبطولات</span>
        </a>
    </div>

    <!-- 1. تبويب طلبات القبول والتسجيل -->
    <?php if ($active_tab === 'applications'): ?>
        <div class="glass-card rounded-3xl p-6 border border-slate-200">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
                <div>
                    <h3 class="text-lg font-black text-slate-900">إدارة طلبات القبول للعام الجديد 2026/2027</h3>
                    <p class="text-xs text-slate-500 mt-0.5">مراجعة بيانات المتقدمين وتحديث حالة القبول وإرسال التوجيهات</p>
                </div>
                <span class="text-xs bg-blue-100 text-blue-800 font-bold px-3 py-1 rounded-full">إجمالي الطلبات: <?= count($applications) ?></span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 font-bold border-b border-slate-200">
                            <th class="p-3.5 rounded-r-xl">رقم الطلب</th>
                            <th class="p-3.5">اسم الطالب</th>
                            <th class="p-3.5">التخصص</th>
                            <th class="p-3.5">النسبة</th>
                            <th class="p-3.5">الهاتف</th>
                            <th class="p-3.5">الحالة الحالية</th>
                            <th class="p-3.5 rounded-l-xl text-center">إجراء الإدارة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($applications as $app): ?>
                            <tr class="hover:bg-blue-50/30 transition-colors">
                                <td class="p-3.5 font-mono font-black text-blue-600"><?= htmlspecialchars($app['app_number']) ?></td>
                                <td class="p-3.5 font-extrabold text-slate-900"><?= htmlspecialchars($app['full_name']) ?></td>
                                <td class="p-3.5 font-semibold text-slate-700">قسم <?= htmlspecialchars($app['dept_name']) ?></td>
                                <td class="p-3.5 font-black text-slate-900"><?= $app['percentage'] ?>%</td>
                                <td class="p-3.5 font-mono text-slate-600"><?= htmlspecialchars($app['phone']) ?></td>
                                <td class="p-3.5">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black <?= $app['status'] == 'مقبول نهائياً' ? 'bg-emerald-100 text-emerald-800' : ($app['status'] == 'مقبول مبدئياً' ? 'bg-blue-100 text-blue-800' : ($app['status'] == 'قيد المراجعة' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800')) ?>">
                                        <?= htmlspecialchars($app['status']) ?>
                                    </span>
                                </td>
                                <td class="p-3.5 text-center">
                                    <button onclick="openAppModal(<?= htmlspecialchars(json_encode($app)) ?>)" class="px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-[11px] transition-all">
                                        تعديل الحالة والرد
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- مودال تعديل حالة القبول -->
        <div id="app-modal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                    <h3 class="text-base font-black text-slate-900">تحديث حالة طلب القبول</h3>
                    <button onclick="closeAppModal()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center">✕</button>
                </div>
                <form method="POST" action="admin_dashboard.php" class="space-y-4">
                    <input type="hidden" name="app_id" id="modal-app-id">

                    <div>
                        <span class="text-xs text-slate-400 block">الطالب:</span>
                        <div id="modal-app-name" class="font-extrabold text-sm text-slate-900"></div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">تحديد قرار القبول *</label>
                        <select name="status" id="modal-app-status" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-blue-600 bg-white">
                            <option value="مقبول نهائياً">مقبول نهائياً (استوفى كافة الشروط) 🟢</option>
                            <option value="مقبول مبدئياً">مقبول مبدئياً (بانتظار المقابلة وتسليم الملف) 🔵</option>
                            <option value="قيد المراجعة">قيد المراجعة والتدقيق 🟡</option>
                            <option value="معلق للاستيفاء">معلق للاستيفاء (نقص وثائق) 🟣</option>
                            <option value="مرفوض">مرفوض (عدم استيفاء النسبة أو المسار) 🔴</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">موعد المقابلة الشخصية (إن وجد)</label>
                        <input type="date" name="interview_date" id="modal-app-date" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-blue-600">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">ملاحظة ورد لجنة القبول للطالب</label>
                        <textarea name="admin_reply" id="modal-app-reply" rows="3" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-blue-600"></textarea>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                        <button type="button" onclick="closeAppModal()" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-bold text-xs">إلغاء</button>
                        <button type="submit" name="update_app_status" class="px-5 py-2 rounded-xl bg-blue-600 text-white font-bold text-xs">حفظ القرار</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <!-- 2. تبويب رصد نتائج الامتحانات -->
    <?php if ($active_tab === 'results'): ?>
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- نموذج رصد نتيجة جديدة (5 أعمدة) -->
            <div class="lg:col-span-5">
                <div class="glass-card rounded-3xl p-6 border border-slate-200">
                    <h3 class="text-base font-black text-slate-900 mb-4 pb-2 border-b border-slate-100">
                        <i class="fa-solid fa-pen-nib text-blue-600 ml-1"></i> رصد نتيجة مقرر جديدة للطالب
                    </h3>

                    <form method="POST" action="admin_dashboard.php" class="space-y-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">رقم القيد الجامعي *</label>
                            <input type="text" name="student_id" required placeholder="STD-2026-101" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">اسم الطالب الرباعي *</label>
                            <input type="text" name="student_name" required placeholder="أحمد علي السقاف" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">رقم الجلوس *</label>
                                <input type="text" name="seat_number" required placeholder="88124" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">الفصل الدراسي *</label>
                                <select name="semester" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white">
                                    <option value="الفصل الثاني">الفصل الثاني</option>
                                    <option value="الفصل الأول">الفصل الأول</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">القسم *</label>
                            <select name="department_id" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white">
                                <?php foreach ($departments as $d): ?>
                                    <option value="<?= $d['id'] ?>">قسم <?= htmlspecialchars($d['name_ar']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">رمز المقرر *</label>
                                <input type="text" name="course_code" required placeholder="IT-201" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">الساعات *</label>
                                <input type="number" name="credit_hours" value="3" min="1" max="6" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">اسم المقرر الأكاديمي *</label>
                            <input type="text" name="course_name" required placeholder="برمجة الويب وتطبيقات السحاب" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">أعمال الفصل (40) *</label>
                                <input type="number" step="0.5" name="coursework_grade" required placeholder="35" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">الامتحان النهائي (60) *</label>
                                <input type="number" step="0.5" name="final_grade" required placeholder="55" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                            </div>
                        </div>

                        <button type="submit" name="add_exam_result" class="w-full py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md mt-2">
                            رصد الدرجة واعتمادها
                        </button>
                    </form>
                </div>
            </div>

            <!-- جدول آخر الدرجات المرصودة (7 أعمدة) -->
            <div class="lg:col-span-7">
                <div class="glass-card rounded-3xl p-6 border border-slate-200">
                    <h3 class="text-base font-black text-slate-900 mb-4 pb-2 border-b border-slate-100">
                        آخر النتائج والدرجات المرصودة في الكنترول
                    </h3>

                    <div class="overflow-x-auto">
                        <table class="w-full text-right text-xs">
                            <thead>
                                <tr class="bg-slate-50 text-slate-500 font-bold border-b border-slate-200">
                                    <th class="p-2.5">رقم القيد</th>
                                    <th class="p-2.5">الطالب</th>
                                    <th class="p-2.5">المقرر</th>
                                    <th class="p-2.5">المجموع</th>
                                    <th class="p-2.5">التقدير</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php foreach ($all_results as $r): ?>
                                    <tr class="hover:bg-slate-50">
                                        <td class="p-2.5 font-mono text-blue-600"><?= htmlspecialchars($r['student_id']) ?></td>
                                        <td class="p-2.5 font-bold"><?= htmlspecialchars($r['student_name']) ?></td>
                                        <td class="p-2.5"><?= htmlspecialchars($r['course_name']) ?></td>
                                        <td class="p-2.5 font-black text-blue-700"><?= $r['total_grade'] ?>%</td>
                                        <td class="p-2.5 font-bold text-emerald-700"><?= htmlspecialchars($r['letter_grade']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    <?php endif; ?>

    <!-- 3. تبويب الشكاوى والمقترحات -->
    <?php if ($active_tab === 'complaints'): ?>
        <div class="glass-card rounded-3xl p-6 border border-slate-200">
            <h3 class="text-lg font-black text-slate-900 mb-2">إدارة الشكاوى والمقترحات والرد عليها</h3>
            <p class="text-xs text-slate-500 mb-6">متابعة رسائل الطلبة والرد عليها لتعزيز جودة التعليم والخدمات</p>

            <div class="space-y-4">
                <?php foreach ($all_complaints as $c): ?>
                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-slate-200 gap-2 mb-3">
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded text-[11px] font-bold <?= $c['type'] == 'مقترح تطويري' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800' ?>">
                                    <?= htmlspecialchars($c['type']) ?>
                                </span>
                                <span class="text-xs font-mono font-bold text-blue-600"><?= htmlspecialchars($c['ticket_number']) ?></span>
                                <span class="text-xs font-bold text-slate-900"><?= htmlspecialchars($c['title']) ?></span>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-black <?= $c['status'] == 'تم الحل والرد' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' ?>">
                                <?= htmlspecialchars($c['status']) ?>
                            </span>
                        </div>

                        <div class="text-xs text-slate-500 mb-2">
                            بواسطة: <b><?= htmlspecialchars($c['student_name']) ?></b> 
                            <?= $c['student_id'] ? "(قيد: {$c['student_id']})" : '' ?> | 
                            التاريخ: <?= $c['created_at'] ?> | 
                            التصنيف: <?= htmlspecialchars($c['category']) ?>
                        </div>

                        <p class="text-xs text-slate-700 leading-relaxed bg-white p-3 rounded-xl border border-slate-200 mb-3">
                            <?= nl2br(htmlspecialchars($c['content'])) ?>
                        </p>

                        <!-- نموذج الرد المباشر -->
                        <form method="POST" action="admin_dashboard.php" class="bg-blue-50/50 p-3.5 rounded-xl border border-blue-100 space-y-2">
                            <input type="hidden" name="complaint_id" value="<?= $c['id'] ?>">
                            <div class="flex flex-col sm:flex-row items-center gap-2">
                                <textarea name="admin_response" rows="2" placeholder="اكتب رد عمادة الكلية والإجراء المتخذ هنا..." class="flex-1 w-full px-3 py-2 rounded-lg border border-slate-200 text-xs focus:outline-none focus:border-blue-600 bg-white"><?= htmlspecialchars($c['admin_response'] ?? '') ?></textarea>
                                <div class="flex sm:flex-col gap-2 w-full sm:w-auto">
                                    <select name="status" class="px-3 py-2 rounded-lg border border-slate-200 text-xs bg-white font-bold">
                                        <option value="تم الحل والرد" <?= $c['status'] == 'تم الحل والرد' ? 'selected' : '' ?>>تم الحل والرد 🟢</option>
                                        <option value="قيد المعالجة" <?= $c['status'] == 'قيد المعالجة' ? 'selected' : '' ?>>قيد المعالجة 🔵</option>
                                        <option value="قيد المراجعة" <?= $c['status'] == 'قيد المراجعة' ? 'selected' : '' ?>>قيد المراجعة 🟡</option>
                                    </select>
                                    <button type="submit" name="reply_complaint" class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs whitespace-nowrap">
                                        إرسال الرد
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- 4. تبويب الأنشطة وبطولة كرة القدم -->
    <?php if ($active_tab === 'activities'): ?>
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <div class="lg:col-span-5">
                <div class="glass-card rounded-3xl p-6 border border-slate-200">
                    <h3 class="text-base font-black text-slate-900 mb-4 pb-2 border-b border-slate-100">
                        <i class="fa-solid fa-plus-circle text-emerald-600 ml-1"></i> إضافة نشاط أو مبادرة جديدة
                    </h3>
                    <form method="POST" action="admin_dashboard.php" class="space-y-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">عنوان الفعالية أو المبادرة *</label>
                            <input type="text" name="title" required placeholder="مثال: دورة الإسعافات الأولية" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">التصنيف *</label>
                                <select name="category" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white">
                                    <option value="مبادرات طلابية">مبادرات طلابية</option>
                                    <option value="دورات كرة القدم">دورات كرة القدم</option>
                                    <option value="الأيام الثقافية">الأيام الثقافية</option>
                                    <option value="الحملات التوعوية">الحملات التوعوية</option>
                                    <option value="المشاركات الطلابية">المشاركات الطلابية</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">تاريخ الفعالية *</label>
                                <input type="date" name="event_date" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">الجهة المنظمة / النادي</label>
                            <input type="text" name="organizer" placeholder="عمادة شؤون الطلاب" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">الموقع / القاعة *</label>
                            <input type="text" name="location" required placeholder="الملعب الجامعي أو القاعة الكبرى" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">وصف النشاط بالتفصيل *</label>
                            <textarea name="description" rows="3" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">الأهداف المتوقعة</label>
                            <input type="text" name="goals" placeholder="صقل المهارات، خدمة المجتمع..." class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                        </div>
                        <button type="submit" name="add_activity" class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md mt-2">
                            نشر الفعالية
                        </button>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-7">
                <div class="glass-card rounded-3xl p-6 border border-slate-200">
                    <h3 class="text-base font-black text-slate-900 mb-4 pb-2 border-b border-slate-100">
                        الأنشطة والفعاليات المنشورة في الكلية
                    </h3>
                    <div class="space-y-3">
                        <?php foreach ($all_activities as $ac): ?>
                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between text-xs">
                                <div>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-blue-100 text-blue-800"><?= htmlspecialchars($ac['category']) ?></span>
                                    <h5 class="font-extrabold text-slate-900 text-sm mt-1"><?= htmlspecialchars($ac['title']) ?></h5>
                                    <span class="text-slate-400 text-[11px]"><?= $ac['event_date'] ?> - <?= htmlspecialchars($ac['location']) ?> (المسجلون: <?= $ac['registered_count'] ?>)</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

</div>

<script>
function openAppModal(app) {
    document.getElementById('modal-app-id').value = app.id;
    document.getElementById('modal-app-name').innerText = app.full_name + ' (' + app.app_number + ') - نسبة: ' + app.percentage + '%';
    document.getElementById('modal-app-status').value = app.status;
    document.getElementById('modal-app-date').value = app.interview_date || '';
    document.getElementById('modal-app-reply').value = app.admin_reply || '';
    const modal = document.getElementById('app-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}
function closeAppModal() {
    const modal = document.getElementById('app-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
