<?php
$page_title = 'الرئيسية - بوابة كلية الأفق الأكاديمية';
require_once __DIR__ . '/includes/header.php';

// جلب الأقسام من قاعدة البيانات
$stmt_depts = $pdo->query("SELECT * FROM departments ORDER BY id ASC");
$departments = $stmt_depts->fetchAll();

// جلب آخر الأنشطة
$stmt_act = $pdo->query("SELECT * FROM activities ORDER BY id DESC LIMIT 4");
$activities = $stmt_act->fetchAll();

// جلب الفعاليات القادمة في التقويم
$stmt_cal = $pdo->query("SELECT * FROM academic_calendar WHERE is_highlight = 1 ORDER BY start_date ASC LIMIT 4");
$calendar_events = $stmt_cal->fetchAll();

// جلب آخر مباريات دوري كرة القدم
$stmt_football = $pdo->query("SELECT * FROM football_matches ORDER BY match_date DESC LIMIT 3");
$football_matches = $stmt_football->fetchAll();
?>

<!-- قسم الترحيب والواجهة البطولية (Hero Section) -->
<section class="relative overflow-hidden hero-gradient py-16 lg:py-24 border-b border-slate-200/80">
    <!-- عناصر زخرفية خلفية -->
    <div class="absolute -top-24 -right-24 w-96 h-96 bg-blue-400/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-emerald-400/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <!-- النصوص والأزرار الترحيبية -->
            <div class="lg:col-span-7 space-y-6 text-center lg:text-right">
                
                <!-- شارة تشجيعية جذابة -->
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full motivational-pill text-blue-900 font-bold text-xs sm:text-sm shadow-sm">
                    <span class="flex h-2.5 w-2.5 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                    </span>
                    <span>باب القبول والتسجيل للعام الجامعي 2026/2027 مفتوح الآن! ✨</span>
                </div>

                <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black text-slate-900 leading-tight">
                    أهلاً بك في <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-l from-blue-700 via-indigo-600 to-cyan-600">كُـلّـيـة الأُفُــق الأكاديمية</span>
                </h1>

                <!-- عبارة تشجيعية بارزة للطلاب -->
                <div class="p-4 rounded-2xl bg-white/80 border border-blue-100 shadow-sm backdrop-blur-sm">
                    <p class="text-base sm:text-lg text-slate-700 font-medium leading-relaxed">
                        <span class="text-2xl text-amber-500 font-black">“</span>
                        طموحك يبدأ من هنا نحو آفاق رحبة من التميز والإبداع! نمنحك تعليماً استثنائياً يجمع بين المعرفة العميقة، المهارات السريرية والتطبيقية، والأنشطة الطلابية الحماسية.
                        <span class="text-2xl text-amber-500 font-black">”</span>
                    </p>
                </div>

                <!-- أزرار الإجراءات السريعة -->
                <div class="flex flex-wrap items-center justify-center lg:justify-start gap-4 pt-2">
                    <a href="admissions.php" class="px-8 py-4 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-extrabold text-base shadow-xl shadow-blue-500/25 hover:scale-105 transition-all flex items-center gap-3">
                        <i class="fa-solid fa-file-pen text-lg"></i>
                        <span>التقديم لبداية العام</span>
                    </a>
                    <a href="admission_results.php" class="px-7 py-4 rounded-2xl bg-white hover:bg-slate-50 text-slate-800 font-extrabold text-base border border-slate-200 shadow-sm hover:border-blue-300 hover:scale-105 transition-all flex items-center gap-2">
                        <i class="fa-solid fa-id-card-clip text-blue-600"></i>
                        <span>استعلام نتائج القبول</span>
                    </a>
                    <a href="exam_results.php" class="px-6 py-4 rounded-2xl bg-amber-50 hover:bg-amber-100 text-amber-900 font-bold text-sm border border-amber-200 transition-all flex items-center gap-2">
                        <i class="fa-solid fa-square-poll-vertical text-amber-600"></i>
                        <span>نتائج الامتحانات</span>
                    </a>
                </div>

                <!-- ميزات سريعة مصغرة -->
                <div class="grid grid-cols-3 gap-3 pt-4 max-w-lg mx-auto lg:mx-0">
                    <div class="text-center p-3 rounded-xl bg-white/70 border border-slate-200/60 shadow-sm">
                        <div class="text-2xl font-black text-blue-600">6</div>
                        <div class="text-xs font-bold text-slate-500">تخصصات رائدة</div>
                    </div>
                    <div class="text-center p-3 rounded-xl bg-white/70 border border-slate-200/60 shadow-sm">
                        <div class="text-2xl font-black text-emerald-600">100%</div>
                        <div class="text-xs font-bold text-slate-500">اعتماد رسمي</div>
                    </div>
                    <div class="text-center p-3 rounded-xl bg-white/70 border border-slate-200/60 shadow-sm">
                        <div class="text-2xl font-black text-amber-600">20+</div>
                        <div class="text-xs font-bold text-slate-500">نشاط ومبادرة</div>
                    </div>
                </div>

            </div>

            <!-- بطاقة الترحيب التفاعلية والرسوم الجذابة -->
            <div class="lg:col-span-5">
                <div class="relative max-w-md mx-auto">
                    <!-- كرت الطالب والنشاط -->
                    <div class="glass-card rounded-3xl p-6 relative z-10 border border-white">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center text-xl font-black">
                                    🎓
                                </div>
                                <div>
                                    <h4 class="font-extrabold text-slate-900 text-base">بوابة الطالب الذكية</h4>
                                    <p class="text-xs text-slate-500">كلية الأفق - ريادة وإلهام</p>
                                </div>
                            </div>
                            <span class="bg-emerald-100 text-emerald-700 text-xs px-2.5 py-1 rounded-full font-bold">نشط 🟢</span>
                        </div>

                        <!-- روابط سريعة مباشرة -->
                        <div class="space-y-3">
                            <a href="departments.php" class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 hover:bg-blue-50/80 border border-slate-100 hover:border-blue-200 transition-all group">
                                <div class="flex items-center gap-3">
                                    <span class="w-9 h-9 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-sm group-hover:scale-110 transition-transform"><i class="fa-solid fa-stethoscope"></i></span>
                                    <div>
                                        <div class="text-sm font-bold text-slate-800">الأقسام العلمية والأدبية</div>
                                        <div class="text-[11px] text-slate-500">تمريض، مختبرات، تقنية، إدارة، إنجليزي، محاسبة</div>
                                    </div>
                                </div>
                                <i class="fa-solid fa-chevron-left text-xs text-slate-400 group-hover:text-blue-600 group-hover:-translate-x-1 transition-all"></i>
                            </a>

                            <a href="calendar.php" class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 hover:bg-emerald-50/80 border border-slate-100 hover:border-emerald-200 transition-all group">
                                <div class="flex items-center gap-3">
                                    <span class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-sm group-hover:scale-110 transition-transform"><i class="fa-regular fa-calendar-check"></i></span>
                                    <div>
                                        <div class="text-sm font-bold text-slate-800">التقويم الأكاديمي 2026/2027</div>
                                        <div class="text-[11px] text-slate-500">مواعيد التقديم وبداية الدراسة والامتحانات</div>
                                    </div>
                                </div>
                                <i class="fa-solid fa-chevron-left text-xs text-slate-400 group-hover:text-emerald-600 group-hover:-translate-x-1 transition-all"></i>
                            </a>

                            <a href="activities.php" class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 hover:bg-amber-50/80 border border-slate-100 hover:border-amber-200 transition-all group">
                                <div class="flex items-center gap-3">
                                    <span class="w-9 h-9 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center text-sm group-hover:scale-110 transition-transform"><i class="fa-solid fa-trophy"></i></span>
                                    <div>
                                        <div class="text-sm font-bold text-slate-800">بطولة كرة القدم والمبادرات</div>
                                        <div class="text-[11px] text-slate-500">كأس الأفق، الأيام الثقافية، والحملات الصحية</div>
                                    </div>
                                </div>
                                <i class="fa-solid fa-chevron-left text-xs text-slate-400 group-hover:text-amber-600 group-hover:-translate-x-1 transition-all"></i>
                            </a>

                            <a href="complaints.php" class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 hover:bg-rose-50/80 border border-slate-100 hover:border-rose-200 transition-all group">
                                <div class="flex items-center gap-3">
                                    <span class="w-9 h-9 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center text-sm group-hover:scale-110 transition-transform"><i class="fa-solid fa-comments"></i></span>
                                    <div>
                                        <div class="text-sm font-bold text-slate-800">الشكاوى والمقترحات</div>
                                        <div class="text-[11px] text-slate-500">صوتك مسموع مباشرة لدى إدارة الكلية</div>
                                    </div>
                                </div>
                                <i class="fa-solid fa-chevron-left text-xs text-slate-400 group-hover:text-rose-600 group-hover:-translate-x-1 transition-all"></i>
                            </a>
                        </div>

                        <!-- إشعار تشجيعي سفلي -->
                        <div class="mt-5 p-3 rounded-xl bg-blue-50 border border-blue-200/60 text-xs text-blue-900 flex items-center gap-2">
                            <i class="fa-solid fa-lightbulb text-amber-500 text-sm"></i>
                            <span><b>نصيحة اليوم:</b> اجعل شغفك هو المحرك الأساسي لكل إنجاز تصنعه!</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- قسم مجالات وأقسام الكلية (الستة تخصصات المحددة) -->
<section class="py-20 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-3xl mx-auto mb-14 space-y-3">
            <span class="text-blue-600 font-extrabold text-xs uppercase tracking-widest bg-blue-100/70 px-3.5 py-1.5 rounded-full">تخصصات المستقبل</span>
            <h2 class="text-3xl sm:text-4xl font-black text-slate-900">الأقسام العلمية والأدبية في كلية الأفق</h2>
            <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                برامج أكاديمية معتمدة صُممت بعناية لتزويدك بأحدث المعارف العلمية والمهارات التطبيقية المطلوبة في سوق العمل الحديث.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($departments as $dept): ?>
                <div class="glass-card rounded-3xl p-7 border border-slate-200/80 relative flex flex-col justify-between group">
                    <div>
                        <!-- الرأس والأيقونة -->
                        <div class="flex items-center justify-between mb-5">
                            <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl text-white shadow-lg transition-transform group-hover:scale-110" style="background-color: <?= $dept['color_accent'] ?>;">
                                <i class="fa-solid <?= $dept['icon'] ?>"></i>
                            </div>
                            <span class="text-xs font-black px-3 py-1 rounded-full <?= $dept['track_type'] == 'علمي' ? 'bg-sky-100 text-sky-800' : ($dept['track_type'] == 'أدبي' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800') ?>">
                                <?= $dept['track_type'] ?>
                            </span>
                        </div>

                        <!-- الاسم والوصف -->
                        <h3 class="text-xl font-black text-slate-900 mb-1 group-hover:text-blue-600 transition-colors">
                            قسم <?= htmlspecialchars($dept['name_ar']) ?>
                        </h3>
                        <div class="text-xs text-slate-400 font-bold mb-3"><?= htmlspecialchars($dept['name_en']) ?></div>
                        <p class="text-slate-600 text-sm leading-relaxed mb-5">
                            <?= htmlspecialchars($dept['short_desc']) ?>
                        </p>

                        <!-- تفاصيل سريعة -->
                        <div class="space-y-2 py-3 border-y border-slate-100 text-xs text-slate-600 mb-5">
                            <div class="flex justify-between">
                                <span class="text-slate-400"><i class="fa-solid fa-graduation-cap ml-1"></i> الدرجة العلمية:</span>
                                <span class="font-bold text-slate-800"><?= htmlspecialchars($dept['degree']) ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400"><i class="fa-solid fa-chart-line ml-1"></i> الحد الأدنى للثانوية:</span>
                                <span class="font-extrabold text-blue-600"><?= $dept['min_percentage'] ?>%</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <a href="departments.php#dept-<?= $dept['id'] ?>" class="flex-1 text-center py-2.5 rounded-xl bg-slate-100 hover:bg-blue-600 hover:text-white text-slate-700 font-bold text-xs transition-all">
                            تفاصيل التخصص
                        </a>
                        <a href="admissions.php?dept=<?= $dept['id'] ?>" class="flex-1 text-center py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-sm transition-all">
                            قدّم الآن
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    </div>
</section>

<!-- قسم النشاط الطلابي والفعاليات الحماسية (كرة القدم والمبادرات والأيام الثقافية) -->
<section class="py-20 bg-white border-t border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col md:flex-row justify-between items-center mb-12 gap-4">
            <div>
                <span class="text-emerald-600 font-extrabold text-xs uppercase tracking-widest bg-emerald-100/70 px-3 py-1 rounded-full">حياة جامعية متكاملة</span>
                <h2 class="text-3xl sm:text-4xl font-black text-slate-900 mt-2">النشاط الطلابي والمبادرات الحية</h2>
                <p class="text-slate-500 text-sm mt-1">دورات كرة القدم، المبادرات الإنسانية، الأيام الثقافية، والحملات الصحية التوعوية.</p>
            </div>
            <a href="activities.php" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-emerald-600 hover:text-white text-slate-800 font-bold text-sm transition-all flex items-center gap-2">
                <span>تصفح جميع الأنشطة</span>
                <i class="fa-solid fa-arrow-left"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- بطاقات الأنشطة والمبادرات (8 أعمدة) -->
            <div class="lg:col-span-8 grid grid-cols-1 sm:grid-cols-2 gap-6">
                <?php foreach ($activities as $act): ?>
                    <div class="glass-card rounded-3xl p-6 border border-slate-200/80 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-black px-3 py-1 rounded-full bg-blue-100 text-blue-800">
                                    <?= htmlspecialchars($act['category']) ?>
                                </span>
                                <span class="text-xs text-slate-400 font-bold flex items-center gap-1">
                                    <i class="fa-regular fa-calendar ml-1 text-slate-400"></i>
                                    <?= format_date_ar($act['event_date']) ?>
                                </span>
                            </div>

                            <h4 class="text-base font-black text-slate-900 mb-2 leading-snug">
                                <?= htmlspecialchars($act['title']) ?>
                            </h4>
                            <p class="text-slate-600 text-xs leading-relaxed line-clamp-3 mb-4">
                                <?= htmlspecialchars($act['description']) ?>
                            </p>
                        </div>

                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                            <span class="text-slate-500 flex items-center gap-1">
                                <i class="fa-solid fa-location-dot text-rose-500"></i>
                                <?= htmlspecialchars($act['location']) ?>
                            </span>
                            <a href="activities.php#act-<?= $act['id'] ?>" class="text-blue-600 font-bold hover:underline">
                                المزيد والتسجيل ←
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- دوري كرة القدم - كأس الأفق (4 أعمدة) -->
            <div class="lg:col-span-4 bg-gradient-to-b from-slate-900 to-indigo-950 rounded-3xl p-6 text-white shadow-xl relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 text-slate-800/20 text-9xl pointer-events-none">
                    <i class="fa-solid fa-futbol"></i>
                </div>
                
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <span class="w-10 h-10 rounded-xl bg-amber-400 text-slate-950 flex items-center justify-center text-lg font-black">
                            <i class="fa-solid fa-trophy"></i>
                        </span>
                        <div>
                            <h4 class="font-extrabold text-base">بطولة كأس الأفق 2026</h4>
                            <p class="text-xs text-amber-200">دوري خماسيات كرة القدم</p>
                        </div>
                    </div>
                    <span class="bg-red-500/80 text-white text-[11px] px-2.5 py-0.5 rounded-full font-bold animate-pulse">مباشر ⚽</span>
                </div>

                <div class="space-y-4">
                    <?php foreach ($football_matches as $match): ?>
                        <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/10">
                            <div class="flex justify-between items-center text-[11px] text-slate-300 mb-2">
                                <span><?= htmlspecialchars($match['stage']) ?></span>
                                <span class="text-amber-300 font-bold"><?= htmlspecialchars($match['status']) ?></span>
                            </div>
                            <div class="flex items-center justify-between font-bold text-sm">
                                <span class="truncate max-w-[40%]"><?= htmlspecialchars($match['team_a']) ?></span>
                                <div class="px-3 py-1 rounded-lg bg-black/40 text-amber-300 font-black tracking-widest text-xs">
                                    <?= $match['score_a'] !== null ? $match['score_a'] . ' - ' . $match['score_b'] : 'VS' ?>
                                </div>
                                <span class="truncate max-w-[40%] text-left"><?= htmlspecialchars($match['team_b']) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="mt-6 pt-4 border-t border-white/10 text-center">
                    <a href="activities.php#football" class="block w-full py-2.5 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-950 font-black text-xs transition-all">
                        جدول البطولة وترتيب الفرق
                    </a>
                </div>
            </div>

        </div>

    </div>
</section>

<!-- قسم التقويم الدراسي السريع والأحداث القادمة -->
<section class="py-20 bg-slate-50 border-t border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-2xl mx-auto mb-12 space-y-2">
            <span class="text-indigo-600 font-extrabold text-xs uppercase tracking-widest bg-indigo-100 px-3 py-1 rounded-full">محطات العام الدراسي</span>
            <h2 class="text-3xl font-black text-slate-900">التقويم الأكاديمي 2026 / 2027</h2>
            <p class="text-slate-500 text-sm">أهم المواعيد الرسمية للتسجيل وبداية الدراسة والامتحانات والأنشطة.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($calendar_events as $idx => $event): ?>
                <div class="glass-card rounded-3xl p-6 border border-slate-200 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-black px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700">
                                <?= htmlspecialchars($event['category']) ?>
                            </span>
                            <span class="text-xs font-bold text-slate-400">
                                <?= date('d M', strtotime($event['start_date'])) ?>
                            </span>
                        </div>
                        <h4 class="font-extrabold text-slate-900 text-base mb-2">
                            <?= htmlspecialchars($event['title']) ?>
                        </h4>
                        <p class="text-slate-600 text-xs leading-relaxed">
                            <?= htmlspecialchars($event['description']) ?>
                        </p>
                    </div>
                    <div class="mt-5 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400">
                        <span><i class="fa-regular fa-clock ml-1"></i> <?= htmlspecialchars($event['semester']) ?></span>
                        <a href="calendar.php" class="text-blue-600 font-bold hover:underline">التقويم الكامل ←</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    </div>
</section>

<!-- قسم الشكاوى والمقترحات وصوت الطالب -->
<section class="py-16 bg-gradient-to-r from-blue-900 to-indigo-900 text-white relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            
            <div class="lg:col-span-8 space-y-4">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-400/20 text-amber-300 font-bold text-xs border border-amber-400/30">
                    <i class="fa-solid fa-heart ml-1"></i> صوتك مسموع ورأيك يبني كليتنا
                </span>
                <h3 class="text-2xl sm:text-4xl font-black">نظام الشكاوى الطلابية والمقترحات التطويرية</h3>
                <p class="text-blue-100 text-sm leading-relaxed max-w-2xl">
                    في كلية الأفق نؤمن بأن الطالب هو شريك النجاح الأول. نتيح لك منصة آمنة لتقديم أي شكوى أو مقترح أكاديمي أو خدمي، مع إمكانية التقديم السري ومتابعة رد عمادة الكلية وإدارتها بشفافية تامة.
                </p>
            </div>

            <div class="lg:col-span-4 flex flex-col sm:flex-row lg:flex-col gap-3">
                <a href="complaints.php" class="px-6 py-3.5 rounded-2xl bg-amber-400 hover:bg-amber-300 text-slate-950 font-black text-center shadow-lg transition-all text-sm flex items-center justify-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span>إرسال شكوى أو مقترح الآن</span>
                </a>
                <a href="complaints.php#track" class="px-6 py-3.5 rounded-2xl bg-white/10 hover:bg-white/20 text-white font-bold text-center border border-white/20 transition-all text-sm flex items-center justify-center gap-2">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <span>متابعة حالة تذكرة سابقة</span>
                </a>
            </div>

        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
