<?php
$page_title = 'الأقسام الأدبية والعلمية - مجالات كلية الأفق';
require_once __DIR__ . '/config/db.php';

$filter = clean_input($_GET['track'] ?? 'all');

if ($filter === 'scientific') {
    $stmt = $pdo->prepare("SELECT * FROM departments WHERE track_type = 'علمي' ORDER BY id ASC");
    $stmt->execute();
} elseif ($filter === 'literary') {
    $stmt = $pdo->prepare("SELECT * FROM departments WHERE track_type IN ('أدبي', 'مشترك') ORDER BY id ASC");
    $stmt->execute();
} else {
    $stmt = $pdo->query("SELECT * FROM departments ORDER BY id ASC");
}

$departments = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<!-- هيدر الصفحة -->
<section class="bg-gradient-to-b from-blue-900 to-indigo-900 text-white py-14 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <span class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-cyan-400 text-blue-950 font-black text-xs shadow-sm mb-3">
            <i class="fa-solid fa-shapes"></i> بيئة تعليمية تلهمك للتفوق
        </span>
        <h1 class="text-3xl sm:text-5xl font-black mb-4">الأقسام العلمية والأدبية في كلية الأفق</h1>
        <p class="text-blue-200 text-sm sm:text-base max-w-2xl mx-auto leading-relaxed">
            استكشف مجالات الكلية الستة المعتمدة، وتعرف على خططها الدراسية، متطلبات القبول، وفرصك المستقبلية الواعدة في سوق العمل الإقليمي والدولي.
        </p>

        <!-- أزرار الفلترة السريعة -->
        <div class="flex justify-center gap-2 mt-8">
            <a href="departments.php?track=all" class="px-5 py-2.5 rounded-xl font-bold text-xs transition-all <?= $filter == 'all' ? 'bg-white text-blue-900 shadow-md scale-105' : 'bg-white/10 text-white hover:bg-white/20' ?>">
                جميع التخصصات (6)
            </a>
            <a href="departments.php?track=scientific" class="px-5 py-2.5 rounded-xl font-bold text-xs transition-all <?= $filter == 'scientific' ? 'bg-white text-blue-900 shadow-md scale-105' : 'bg-white/10 text-white hover:bg-white/20' ?>">
                الأقسام العلمية (تمريض، طب ومختبرات، تقنية)
            </a>
            <a href="departments.php?track=literary" class="px-5 py-2.5 rounded-xl font-bold text-xs transition-all <?= $filter == 'literary' ? 'bg-white text-blue-900 shadow-md scale-105' : 'bg-white/10 text-white hover:bg-white/20' ?>">
                الأقسام المشتركة والأدبية (إدارة، لغات، محاسبة)
            </a>
        </div>
    </div>
</section>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 space-y-12">

    <!-- قائمة الأقسام المفصلة -->
    <?php foreach ($departments as $dept): ?>
        <div id="dept-<?= $dept['id'] ?>" class="glass-card rounded-3xl p-8 border border-slate-200 scroll-mt-28 relative overflow-hidden">
            
            <!-- شريط جانبي ملون لكل تخصص -->
            <div class="absolute top-0 right-0 w-3 h-full" style="background-color: <?= $dept['color_accent'] ?>;"></div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- 8 أعمدة: العنوان والتفاصيل والمهن والخطة -->
                <div class="lg:col-span-8 space-y-6">
                    
                    <!-- الهيدر -->
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-3xl text-white shadow-lg" style="background-color: <?= $dept['color_accent'] ?>;">
                            <i class="fa-solid <?= $dept['icon'] ?>"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="text-2xl sm:text-3xl font-black text-slate-900">
                                    قسم <?= htmlspecialchars($dept['name_ar']) ?>
                                </h2>
                                <span class="text-xs font-black px-3 py-1 rounded-full <?= $dept['track_type'] == 'علمي' ? 'bg-sky-100 text-sky-800' : 'bg-amber-100 text-amber-800' ?>">
                                    المسار: <?= $dept['track_type'] ?>
                                </span>
                            </div>
                            <div class="text-sm font-bold text-slate-400 font-mono mt-0.5">
                                <?= htmlspecialchars($dept['name_en']) ?> (<?= htmlspecialchars($dept['code']) ?>)
                            </div>
                        </div>
                    </div>

                    <!-- النبذة الشاملة -->
                    <div>
                        <h4 class="text-xs font-extrabold text-slate-400 uppercase tracking-widest mb-2">رؤية القسم ورسالته</h4>
                        <p class="text-slate-700 text-sm sm:text-base leading-relaxed">
                            <?= htmlspecialchars($dept['full_desc']) ?>
                        </p>
                    </div>

                    <!-- مجالات العمل بعد التخرج -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80">
                        <h4 class="text-xs font-extrabold text-blue-700 flex items-center gap-2 mb-2">
                            <i class="fa-solid fa-briefcase"></i> الآفاق والفرص الوظيفية للخريجين:
                        </h4>
                        <p class="text-slate-600 text-xs sm:text-sm leading-relaxed">
                            <?= htmlspecialchars($dept['career_opportunities']) ?>
                        </p>
                    </div>

                    <!-- نظرة على الخطة الدراسية -->
                    <div>
                        <h4 class="text-xs font-extrabold text-slate-400 uppercase tracking-widest mb-2">محاور الخطة الدراسية</h4>
                        <div class="p-4 rounded-2xl bg-blue-50/50 border border-blue-100 text-xs sm:text-sm text-slate-700 leading-relaxed">
                            <i class="fa-solid fa-book-open text-blue-600 ml-1"></i>
                            <?= htmlspecialchars($dept['study_plan']) ?>
                        </div>
                    </div>

                </div>

                <!-- 4 أعمدة: كرت الشروط، الرسوم، وزر التقديم -->
                <div class="lg:col-span-4 bg-slate-50 rounded-3xl p-6 border border-slate-200 space-y-4">
                    <h4 class="font-black text-slate-900 text-sm pb-2 border-b border-slate-200">
                        معلومات وشروط الالتحاق
                    </h4>

                    <div class="space-y-3 text-xs">
                        <div class="flex justify-between items-center py-1.5 border-b border-slate-200/60">
                            <span class="text-slate-500 font-medium"><i class="fa-solid fa-medal ml-1 text-amber-500"></i> الدرجة الأكاديمية:</span>
                            <span class="font-bold text-slate-800"><?= htmlspecialchars($dept['degree']) ?></span>
                        </div>

                        <div class="flex justify-between items-center py-1.5 border-b border-slate-200/60">
                            <span class="text-slate-500 font-medium"><i class="fa-solid fa-chart-line ml-1 text-blue-500"></i> الحد الأدنى للقبول:</span>
                            <span class="font-black text-blue-700 text-sm"><?= $dept['min_percentage'] ?>% ثانوية</span>
                        </div>

                        <div class="flex justify-between items-center py-1.5 border-b border-slate-200/60">
                            <span class="text-slate-500 font-medium"><i class="fa-solid fa-clock ml-1 text-emerald-500"></i> الرسوم والدعم:</span>
                            <span class="font-bold text-emerald-700"><?= htmlspecialchars($dept['annual_fee']) ?></span>
                        </div>

                        <div class="flex justify-between items-center py-1.5 border-b border-slate-200/60">
                            <span class="text-slate-500 font-medium"><i class="fa-solid fa-graduation-cap ml-1 text-purple-500"></i> المسار المقبول:</span>
                            <span class="font-bold text-slate-800"><?= $dept['track_type'] == 'علمي' ? 'علمي فقط' : 'علمي وأدبي' ?></span>
                        </div>
                    </div>

                    <div class="pt-2 space-y-2">
                        <a href="admissions.php?dept=<?= $dept['id'] ?>" class="block w-full py-3.5 rounded-xl text-center text-white font-extrabold text-sm shadow-md hover:scale-[1.02] transition-all" style="background-color: <?= $dept['color_accent'] ?>;">
                            <i class="fa-solid fa-file-signature ml-1"></i> قدّم في هذا التخصص
                        </a>
                        <a href="inquiries.php?q=<?= urlencode($dept['name_ar']) ?>" class="block w-full py-2.5 rounded-xl text-center bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 font-bold text-xs transition-all">
                            استفسر عن تخصص <?= htmlspecialchars($dept['name_ar']) ?>
                        </a>
                    </div>
                </div>

            </div>

        </div>
    <?php endforeach; ?>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
