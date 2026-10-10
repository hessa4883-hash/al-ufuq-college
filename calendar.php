<?php
$page_title = 'التقويم الدراسي الأكاديمي 2026/2027';
require_once __DIR__ . '/config/db.php';

$category_filter = clean_input($_GET['category'] ?? 'all');
$semester_filter = clean_input($_GET['semester'] ?? 'all');

$sql = "SELECT * FROM academic_calendar WHERE 1=1";
$params = [];

if ($category_filter !== 'all') {
    $sql .= " AND category = ?";
    $params[] = $category_filter;
}
if ($semester_filter !== 'all') {
    $sql .= " AND semester = ?";
    $params[] = $semester_filter;
}

$sql .= " ORDER BY start_date ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$events = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<!-- هيدر الصفحة -->
<section class="bg-gradient-to-b from-blue-900 to-indigo-900 text-white py-14 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <span class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-amber-400 text-blue-950 font-black text-xs shadow-sm mb-3">
            <i class="fa-regular fa-calendar-check"></i> الخطة الزمنية الأكاديمية الرسمية
        </span>
        <h1 class="text-3xl sm:text-5xl font-black mb-4">التقويم الدراسي للعام الجامعي 2026 / 2027</h1>
        <p class="text-blue-200 text-sm sm:text-base max-w-2xl mx-auto leading-relaxed">
            دليلك الزمني الشامل لمواعيد التقديم، بداية المحاضرات، فترات الحذف والإضافة، الامتحانات النصفية والنهائية، والأنشطة والبطولات الرياضية.
        </p>

        <!-- أدوات التصفية والطباعة -->
        <div class="flex flex-wrap justify-center items-center gap-2 mt-8 no-print">
            <a href="calendar.php?category=all" class="px-4 py-2 rounded-xl text-xs font-bold transition-all <?= $category_filter == 'all' ? 'bg-white text-blue-900 shadow-md font-black' : 'bg-white/10 text-white hover:bg-white/20' ?>">
                جميع الأحداث
            </a>
            <a href="calendar.php?category=<?= urlencode('تسجيل وقبول') ?>" class="px-4 py-2 rounded-xl text-xs font-bold transition-all <?= $category_filter == 'تسجيل وقبول' ? 'bg-white text-blue-900 shadow-md font-black' : 'bg-white/10 text-white hover:bg-white/20' ?>">
                التسجيل والقبول
            </a>
            <a href="calendar.php?category=<?= urlencode('امتحانات') ?>" class="px-4 py-2 rounded-xl text-xs font-bold transition-all <?= $category_filter == 'امتحانات' ? 'bg-white text-blue-900 shadow-md font-black' : 'bg-white/10 text-white hover:bg-white/20' ?>">
                مواعيد الامتحانات
            </a>
            <a href="calendar.php?category=<?= urlencode('أنشطة وفعاليات') ?>" class="px-4 py-2 rounded-xl text-xs font-bold transition-all <?= $category_filter == 'أنشطة وفعاليات' ? 'bg-white text-blue-900 shadow-md font-black' : 'bg-white/10 text-white hover:bg-white/20' ?>">
                الأنشطة والبطولات
            </a>
            <button onclick="window.print()" class="px-4 py-2 rounded-xl text-xs font-black bg-amber-400 hover:bg-amber-300 text-slate-950 flex items-center gap-1 shadow-sm transition-all">
                <i class="fa-solid fa-print"></i> طباعة التقويم
            </button>
        </div>
    </div>
</section>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
    
    <!-- خط زمني تفاعلي (Interactive Timeline) -->
    <div class="relative border-r-2 border-blue-200 mr-4 sm:mr-8 pr-6 sm:pr-8 space-y-10">
        
        <?php foreach ($events as $idx => $ev): 
            $today = date('Y-m-d');
            $start = $ev['start_date'];
            $end = $ev['end_date'] ?: $start;
            
            $status_label = 'قادم قريباً';
            $status_class = 'bg-blue-100 text-blue-800';
            $dot_class = 'bg-blue-500 ring-blue-200';

            if ($today >= $start && $today <= $end) {
                $status_label = 'جاري الآن 🟢';
                $status_class = 'bg-emerald-100 text-emerald-800 animate-pulse';
                $dot_class = 'bg-emerald-500 ring-emerald-200';
            } elseif ($today > $end) {
                $status_label = 'مكتمل';
                $status_class = 'bg-slate-100 text-slate-600';
                $dot_class = 'bg-slate-400 ring-slate-200';
            }
        ?>
            <div class="relative group">
                <!-- الدائرة على الخط الزمني -->
                <div class="absolute -right-[35px] sm:-right-[43px] top-1.5 w-5 h-5 rounded-full <?= $dot_class ?> ring-4 border-2 border-white transition-all group-hover:scale-125"></div>

                <!-- بطاقة الحدث -->
                <div class="glass-card rounded-3xl p-6 sm:p-7 border border-slate-200/80 <?= $ev['is_highlight'] ? 'ring-2 ring-blue-500/30' : '' ?>">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-slate-100 gap-2 mb-3">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-black px-3 py-1 rounded-full <?= $status_class ?>">
                                <?= $status_label ?>
                            </span>
                            <span class="text-xs font-bold px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700">
                                <?= htmlspecialchars($ev['category']) ?>
                            </span>
                            <span class="text-xs font-bold text-slate-400">
                                <?= htmlspecialchars($ev['semester']) ?>
                            </span>
                        </div>
                        <div class="text-xs sm:text-sm font-extrabold text-blue-700 flex items-center gap-1.5">
                            <i class="fa-regular fa-calendar-days"></i>
                            <span><?= format_date_ar($ev['start_date']) ?></span>
                            <?php if ($ev['end_date'] && $ev['end_date'] !== $ev['start_date']): ?>
                                <span class="text-slate-400">إلى</span>
                                <span><?= format_date_ar($ev['end_date']) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <h3 class="text-lg sm:text-xl font-black text-slate-900 mb-2">
                        <?= htmlspecialchars($ev['title']) ?>
                    </h3>
                    <p class="text-slate-600 text-xs sm:text-sm leading-relaxed">
                        <?= htmlspecialchars($ev['description']) ?>
                    </p>
                </div>
            </div>
        <?php endforeach; ?>

    </div>

    <!-- بطاقة نصيحة وإرشاد في أسفل التقويم -->
    <div class="mt-14 p-6 rounded-3xl bg-blue-50 border border-blue-200 text-blue-950 flex flex-col sm:flex-row items-center gap-4 text-center sm:text-right">
        <div class="w-14 h-14 rounded-2xl bg-blue-600 text-white flex items-center justify-center text-2xl flex-shrink-0">
            <i class="fa-solid fa-bell"></i>
        </div>
        <div class="flex-1 text-xs sm:text-sm">
            <h4 class="font-extrabold text-base mb-1">تنبيه هام للطلبة الأعزاء:</h4>
            <p class="leading-relaxed text-blue-800">
                يُرجى الالتزام بكافة المواعيد المحددة في التقويم الدراسي، وخاصة فترات الحذف والإضافة وتسليم المشاريع لتجنب أي تأخير أكاديمي. مواعيد الامتحانات تخضع لإشراف الكنترول المركزي.
            </p>
        </div>
        <a href="admissions.php" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-sm transition-all flex-shrink-0">
            بوابة التقديمات
        </a>
    </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
