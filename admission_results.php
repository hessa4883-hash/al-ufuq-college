<?php
$page_title = 'عرض نتائج القبول للعام الجامعي 2026/2027';
require_once __DIR__ . '/config/db.php';

$search_query = clean_input($_GET['query'] ?? $_GET['app'] ?? '');
$result = null;
$searched = false;

if (!empty($search_query)) {
    $searched = true;
    $stmt = $pdo->prepare("SELECT a.*, d.name_ar AS dept_name, d.name_en AS dept_en, d.degree, d.color_accent, d.min_percentage 
        FROM applications a 
        JOIN departments d ON a.department_id = d.id 
        WHERE a.app_number = ? OR a.seat_number = ? OR a.national_id = ? OR a.phone = ? OR a.full_name LIKE ?
        LIMIT 1");
    $stmt->execute([$search_query, $search_query, $search_query, $search_query, "%{$search_query}%"]);
    $result = $stmt->fetch();
}

// إحصائيات عامة للقبول
$stats_total = $pdo->query("SELECT COUNT(*) FROM applications")->fetchColumn();
$stats_accepted = $pdo->query("SELECT COUNT(*) FROM applications WHERE status IN ('مقبول نهائياً', 'مقبول مبدئياً')")->fetchColumn();
$stats_review = $pdo->query("SELECT COUNT(*) FROM applications WHERE status = 'قيد المراجعة'")->fetchColumn();

// قائمة المقبولين حديثاً (لإضفاء الشفافية والحيوية)
$recent_accepted = $pdo->query("SELECT a.app_number, a.full_name, a.percentage, a.status, d.name_ar AS dept_name 
    FROM applications a 
    JOIN departments d ON a.department_id = d.id 
    ORDER BY a.id DESC LIMIT 6")->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<!-- هيدر الصفحة والبحث -->
<section class="bg-gradient-to-b from-blue-900 to-indigo-900 text-white py-14 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <span class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-emerald-400 text-blue-950 font-black text-xs shadow-sm mb-3">
            <i class="fa-solid fa-square-check"></i> نتائج المفاضلة والقبول الفورية
        </span>
        <h1 class="text-3xl sm:text-5xl font-black mb-4">بوابة الاستعلام عن نتائج القبول</h1>
        <p class="text-blue-200 text-sm sm:text-base max-w-2xl mx-auto leading-relaxed mb-8">
            أدخل رقم الاستمارة (مثل OFQ-2026-1001) أو رقم الجلوس أو الرقم الوطني للتعرف على نتيجة قبولك فوراً وموعد المقابلة وتثبيت التسجيل.
        </p>

        <!-- صندوق البحث البارز -->
        <div class="max-w-2xl mx-auto bg-white rounded-2xl sm:rounded-3xl p-2 sm:p-3 shadow-2xl flex flex-col sm:flex-row gap-2">
            <form method="GET" action="admission_results.php" class="flex-1 flex flex-col sm:flex-row gap-2">
                <div class="relative flex-1">
                    <i class="fa-solid fa-magnifying-glass absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input type="text" name="query" value="<?= htmlspecialchars($search_query) ?>" required placeholder="رقم الاستمارة، رقم الجلوس، أو الرقم الوطني..." class="w-full pl-4 pr-11 py-3.5 rounded-xl sm:rounded-2xl border-0 focus:outline-none focus:ring-2 focus:ring-blue-600 text-slate-900 text-sm font-medium">
                </div>
                <button type="submit" class="px-8 py-3.5 rounded-xl sm:rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-sm shadow-md transition-all flex items-center justify-center gap-2">
                    <span>استعلام الآن</span>
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                </button>
            </form>
        </div>
    </div>
</section>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    
    <!-- عرض النتيجة إذا تمت عملية البحث -->
    <?php if ($searched): ?>
        <?php if ($result): ?>
            <!-- بطاقة النتيجة الرسمية القابلة للطباعة -->
            <div class="max-w-3xl mx-auto result-certificate rounded-3xl p-8 mb-14 shadow-2xl bg-white border border-slate-300">
                
                <!-- رأس النتيجة -->
                <div class="flex flex-col sm:flex-row items-center justify-between pb-6 border-b border-slate-200 gap-4 text-center sm:text-right">
                    <div class="flex items-center gap-3">
                        <div class="w-14 h-14 rounded-2xl bg-blue-700 text-white flex items-center justify-center text-2xl font-black shadow-md">
                            <i class="fa-solid fa-graduation-cap"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-slate-900">كلية الأفق الأكاديمية</h3>
                            <p class="text-xs text-slate-500 font-bold">عمادة القبول وشؤون الطلاب | إشعار نتيجة القبول</p>
                        </div>
                    </div>
                    <div>
                        <span class="text-xs text-slate-400 block font-bold">تاريخ إصدار الإشعار:</span>
                        <span class="text-xs font-black text-slate-700"><?= date('Y-m-d') ?></span>
                    </div>
                </div>

                <!-- شارة الحالة الكبيرة -->
                <div class="my-6 text-center">
                    <?php if (in_array($result['status'], ['مقبول نهائياً', 'مقبول مبدئياً'])): ?>
                        <div class="inline-flex flex-col items-center p-4 rounded-3xl bg-emerald-50 border-2 border-emerald-400 text-emerald-800 shadow-sm w-full">
                            <div class="w-16 h-16 rounded-full bg-emerald-500 text-white flex items-center justify-center text-3xl mb-2 shadow-md">
                                <i class="fa-solid fa-check"></i>
                            </div>
                            <span class="text-2xl font-black">مبارك القبول! 🎉</span>
                            <span class="text-sm font-bold mt-1 text-emerald-700">تم قبولك بنجاح في: قسم <?= htmlspecialchars($result['dept_name']) ?></span>
                        </div>
                    <?php elseif ($result['status'] === 'قيد المراجعة'): ?>
                        <div class="inline-flex flex-col items-center p-4 rounded-3xl bg-amber-50 border-2 border-amber-400 text-amber-800 shadow-sm w-full">
                            <div class="w-16 h-16 rounded-full bg-amber-500 text-white flex items-center justify-center text-3xl mb-2 shadow-md">
                                <i class="fa-solid fa-hourglass-half"></i>
                            </div>
                            <span class="text-2xl font-black">طلبك قيد المراجعة والتدقيق 🟡</span>
                            <span class="text-sm font-bold mt-1 text-amber-700">تجري لجنة القبول مطابقة البيانات ومفاضلة النسب.</span>
                        </div>
                    <?php else: ?>
                        <div class="inline-flex flex-col items-center p-4 rounded-3xl bg-rose-50 border-2 border-rose-400 text-rose-800 shadow-sm w-full">
                            <div class="w-16 h-16 rounded-full bg-rose-500 text-white flex items-center justify-center text-3xl mb-2 shadow-md">
                                <i class="fa-solid fa-circle-exclamation"></i>
                            </div>
                            <span class="text-2xl font-black"><?= htmlspecialchars($result['status']) ?></span>
                            <span class="text-sm font-bold mt-1 text-rose-700">يرجى مراجعة إدارة القبول للنظر في البرامج البديلة.</span>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- جدول بيانات الطالب والقبول -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs sm:text-sm bg-slate-50 p-5 rounded-2xl border border-slate-200 mb-6">
                    <div>
                        <span class="text-slate-400 block text-xs">اسم الطالب الرباعي:</span>
                        <span class="font-extrabold text-slate-900 text-base"><?= htmlspecialchars($result['full_name']) ?></span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-xs">رقم استمارة التقديم:</span>
                        <span class="font-black text-blue-600 text-base tracking-wider"><?= htmlspecialchars($result['app_number']) ?></span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-xs">التخصص والدرجة:</span>
                        <span class="font-extrabold text-slate-800"><?= htmlspecialchars($result['dept_name']) ?> (<?= htmlspecialchars($result['degree']) ?>)</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-xs">مسار ونسبة الثانوية:</span>
                        <span class="font-extrabold text-slate-800">مسار <?= htmlspecialchars($result['high_school_branch']) ?> - <?= $result['percentage'] ?>%</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-xs">رقم الجلوس:</span>
                        <span class="font-bold text-slate-700"><?= htmlspecialchars($result['seat_number']) ?></span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-xs">الرقم الوطني / الهوية:</span>
                        <span class="font-bold text-slate-700"><?= htmlspecialchars($result['national_id']) ?></span>
                    </div>
                    <?php if (!empty($result['interview_date'])): ?>
                    <div class="sm:col-span-2 p-2.5 rounded-xl bg-blue-100 text-blue-900 font-bold text-xs flex items-center gap-2">
                        <i class="fa-regular fa-calendar-check text-blue-600 text-base"></i>
                        <span>موعد المقابلة الشخصية وتثبيت القيد: <?= format_date_ar($result['interview_date']) ?> في قاعة الاستقبال الرئيسية.</span>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- رسالة وتوجيهات لجنة القبول -->
                <div class="p-4 rounded-2xl bg-blue-50 border border-blue-200 text-blue-900 text-xs leading-relaxed mb-6">
                    <h5 class="font-bold text-sm mb-1 flex items-center gap-2">
                        <i class="fa-solid fa-bullhorn text-blue-600"></i> ملاحظات إدارة الكلية:
                    </h5>
                    <p><?= htmlspecialchars($result['admin_reply'] ?: 'يرجى إحضار أصل الوثائق وسداد القسط الأول لتثبيت المقعد والحصول على البطاقة الجامعية.') ?></p>
                </div>

                <!-- أزرار الإجراءات -->
                <div class="flex flex-wrap items-center justify-center gap-3 no-print">
                    <button onclick="window.print()" class="px-6 py-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs flex items-center gap-2 shadow-sm">
                        <i class="fa-solid fa-print"></i> طباعة إشعار القبول الرسمي
                    </button>
                    <a href="calendar.php" class="px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs flex items-center gap-2 shadow-sm">
                        <i class="fa-regular fa-calendar-days"></i> مواعيد بدء الدراسة
                    </a>
                </div>

            </div>
        <?php else: ?>
            <!-- لم يتم العثور على نتيجة -->
            <div class="max-w-md mx-auto text-center py-12 p-8 rounded-3xl bg-white border border-slate-200 shadow-sm mb-12">
                <div class="w-16 h-16 rounded-full bg-rose-100 text-rose-500 flex items-center justify-center text-3xl mx-auto mb-4">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">لم يتم العثور على طلب مطابق!</h3>
                <p class="text-slate-500 text-xs leading-relaxed mb-6">
                    تأكد من كتابة رقم الاستمارة بشكل صحيح (مثال: OFQ-2026-1001) أو رقم الجلوس أو رقم الهوية.
                </p>
                <div class="flex justify-center gap-3">
                    <a href="admission_results.php" class="px-5 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-bold text-xs">إعادة البحث</a>
                    <a href="admissions.php" class="px-5 py-2.5 rounded-xl bg-blue-600 text-white font-bold text-xs">تقديم طلب جديد</a>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- إحصائيات القبول السريعة -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-12">
        <div class="glass-card rounded-3xl p-6 border border-slate-200 text-center">
            <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center text-xl mx-auto mb-3">
                <i class="fa-solid fa-users"></i>
            </div>
            <div class="text-3xl font-black text-slate-900"><?= $stats_total ?></div>
            <div class="text-xs font-bold text-slate-500 mt-1">إجمالي طلبات التقديم</div>
        </div>

        <div class="glass-card rounded-3xl p-6 border border-slate-200 text-center">
            <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl mx-auto mb-3">
                <i class="fa-solid fa-user-check"></i>
            </div>
            <div class="text-3xl font-black text-emerald-600"><?= $stats_accepted ?></div>
            <div class="text-xs font-bold text-slate-500 mt-1">المقبولون مبدئياً ونهائياً</div>
        </div>

        <div class="glass-card rounded-3xl p-6 border border-slate-200 text-center">
            <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center text-xl mx-auto mb-3">
                <i class="fa-solid fa-hourglass-half"></i>
            </div>
            <div class="text-3xl font-black text-amber-600"><?= $stats_review ?></div>
            <div class="text-xs font-bold text-slate-500 mt-1">طلبات قيد التدقيق والمراجعة</div>
        </div>
    </div>

    <!-- جدول شفافية المقبولين حديثاً -->
    <div class="glass-card rounded-3xl p-6 sm:p-8 border border-slate-200">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
            <div>
                <h3 class="text-lg font-black text-slate-900">آخر المقبولين في كلية الأفق للعام الجامعي 2026/2027</h3>
                <p class="text-xs text-slate-500 mt-0.5">قائمة فورية محدثة مباشرة من قاعدة البيانات</p>
            </div>
            <span class="text-xs bg-emerald-100 text-emerald-800 font-bold px-3 py-1 rounded-full">محدث باستمرار</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-bold border-b border-slate-200">
                        <th class="p-3.5 rounded-r-xl">رقم الاستمارة</th>
                        <th class="p-3.5">اسم الطالب</th>
                        <th class="p-3.5">القسم المقبول فيه</th>
                        <th class="p-3.5">النسبة</th>
                        <th class="p-3.5 rounded-l-xl">الحالة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($recent_accepted as $rec): ?>
                        <tr class="hover:bg-blue-50/40 transition-colors">
                            <td class="p-3.5 font-black text-blue-600 font-mono"><?= htmlspecialchars($rec['app_number']) ?></td>
                            <td class="p-3.5 font-bold text-slate-800"><?= htmlspecialchars($rec['full_name']) ?></td>
                            <td class="p-3.5 font-semibold text-slate-600">قسم <?= htmlspecialchars($rec['dept_name']) ?></td>
                            <td class="p-3.5 font-black text-slate-900"><?= $rec['percentage'] ?>%</td>
                            <td class="p-3.5">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold <?= $rec['status'] == 'مقبول نهائياً' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800' ?>">
                                    <?= htmlspecialchars($rec['status']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
