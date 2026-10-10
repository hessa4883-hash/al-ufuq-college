<?php
$page_title = 'نتائج الامتحانات وكشوف الدرجات الأكاديمية';
require_once __DIR__ . '/config/db.php';

$student_query = clean_input($_GET['student_id'] ?? '');
$semester_query = clean_input($_GET['semester'] ?? 'الفصل الثاني');
$results = [];
$student_info = null;
$searched = false;

if (!empty($student_query)) {
    $searched = true;
    
    // البحث عن درجات الطالب
    $stmt = $pdo->prepare("SELECT er.*, d.name_ar AS dept_name, d.name_en AS dept_en 
        FROM exam_results er 
        JOIN departments d ON er.department_id = d.id 
        WHERE (er.student_id = ? OR er.seat_number = ?)
        AND (er.semester = ? OR ? = 'الكل')
        ORDER BY er.course_code ASC");
    $stmt->execute([$student_query, $student_query, $semester_query, $semester_query]);
    $results = $stmt->fetchAll();

    if (!empty($results)) {
        $student_info = [
            'student_id' => $results[0]['student_id'],
            'student_name' => $results[0]['student_name'],
            'seat_number' => $results[0]['seat_number'],
            'academic_year' => $results[0]['academic_year'],
            'semester' => $results[0]['semester'],
            'dept_name' => $results[0]['dept_name'],
            'dept_en' => $results[0]['dept_en']
        ];

        // حساب المعدل الفصلي GPA
        $total_credits = 0;
        $total_points = 0;
        $total_marks = 0;
        foreach ($results as $r) {
            $total_credits += $r['credit_hours'];
            $total_points += ($r['gpa_points'] * $r['credit_hours']);
            $total_marks += $r['total_grade'];
        }
        $gpa = $total_credits > 0 ? round($total_points / $total_credits, 2) : 0;
        $avg_percentage = count($results) > 0 ? round($total_marks / count($results), 2) : 0;
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<!-- هيدر الصفحة والبحث -->
<section class="bg-gradient-to-b from-blue-900 to-indigo-900 text-white py-14 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <span class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-amber-400 text-blue-950 font-black text-xs shadow-sm mb-3">
            <i class="fa-solid fa-award"></i> بوابة النتائج الرسمية
        </span>
        <h1 class="text-3xl sm:text-5xl font-black mb-4">الاستعلام عن نتائج الامتحانات</h1>
        <p class="text-blue-200 text-sm sm:text-base max-w-2xl mx-auto leading-relaxed mb-8">
            أدخل رقم القيد الجامعي (مثال: STD-2026-101) أو رقم الجلوس لعرض كشف درجاتك المعتمد والمعدل التراكمي وطباعته رسمياً.
        </p>

        <!-- صندوق البحث -->
        <div class="max-w-2xl mx-auto bg-white rounded-2xl sm:rounded-3xl p-3 shadow-2xl">
            <form method="GET" action="exam_results.php" class="flex flex-col sm:flex-row gap-2">
                <div class="relative flex-1">
                    <i class="fa-solid fa-id-card absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input type="text" name="student_id" value="<?= htmlspecialchars($student_query) ?>" required placeholder="رقم القيد (STD-2026-101) أو رقم الجلوس (88124)..." class="w-full pl-4 pr-11 py-3 rounded-xl sm:rounded-2xl border-0 focus:outline-none focus:ring-2 focus:ring-blue-600 text-slate-900 text-sm font-medium">
                </div>
                <select name="semester" class="px-4 py-3 rounded-xl sm:rounded-2xl border border-slate-200 text-slate-700 text-xs font-bold focus:outline-none focus:border-blue-600 bg-slate-50">
                    <option value="الفصل الثاني" <?= $semester_query == 'الفصل الثاني' ? 'selected' : '' ?>>الفصل الثاني</option>
                    <option value="الفصل الأول" <?= $semester_query == 'الفصل الأول' ? 'selected' : '' ?>>الفصل الأول</option>
                    <option value="الكل" <?= $semester_query == 'الكل' ? 'selected' : '' ?>>جميع الفصول</option>
                </select>
                <button type="submit" class="px-7 py-3 rounded-xl sm:rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs shadow-md transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <span>عرض الكشف</span>
                </button>
            </form>
        </div>

        <!-- أرقام تجريبية سريعة للمستخدم للاختبار -->
        <div class="mt-4 text-xs text-blue-200 flex flex-wrap justify-center items-center gap-2">
            <span>أرقام تجريبية جاهزة للاستعلام:</span>
            <a href="exam_results.php?student_id=STD-2026-101&semester=الفصل+الثاني" class="bg-white/20 hover:bg-white/30 text-white px-2.5 py-1 rounded-lg underline">STD-2026-101 (تقنية معلومات)</a>
            <a href="exam_results.php?student_id=STD-2026-102&semester=الفصل+الثاني" class="bg-white/20 hover:bg-white/30 text-white px-2.5 py-1 rounded-lg underline">STD-2026-102 (تمريض)</a>
        </div>
    </div>
</section>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-14">

    <?php if ($searched): ?>
        <?php if (!empty($results) && $student_info): ?>
            
            <!-- بطاقة كشف الدرجات الرسمية المعتمدة (قابلة للطباعة) -->
            <div class="result-certificate rounded-3xl p-6 sm:p-10 mb-10 shadow-2xl bg-white border border-slate-300">
                
                <!-- رأس الكشف الجامعي الرسمي -->
                <div class="flex flex-col sm:flex-row items-center justify-between pb-6 border-b-2 border-slate-900 gap-4 text-center sm:text-right">
                    <div class="flex items-center gap-3">
                        <div class="w-16 h-16 rounded-2xl bg-blue-900 text-white flex items-center justify-center text-3xl font-black">
                            <i class="fa-solid fa-graduation-cap"></i>
                        </div>
                        <div>
                            <h2 class="text-xl sm:text-2xl font-black text-slate-900">كلية الأفق الأكاديمية</h2>
                            <p class="text-xs font-bold text-slate-500">نيابة شؤون الطلاب - الإدارة العامة للقبول والامتحانات</p>
                            <p class="text-[10px] text-slate-400 font-mono">Al-Ufuq College - Student Academic Records</p>
                        </div>
                    </div>
                    <div class="text-left font-mono text-xs text-slate-600">
                        <div class="font-bold text-slate-900">كشف درجات رسمي</div>
                        <div>العام الجامعي: <?= htmlspecialchars($student_info['academic_year']) ?></div>
                        <div>الفصل: <?= htmlspecialchars($student_info['semester']) ?></div>
                    </div>
                </div>

                <!-- بيانات الطالب الأكاديمية -->
                <div class="my-6 grid grid-cols-2 sm:grid-cols-4 gap-4 bg-slate-50 p-4 rounded-2xl border border-slate-200 text-xs">
                    <div>
                        <span class="text-slate-400 block text-[11px]">اسم الطالب:</span>
                        <span class="font-black text-slate-900 text-sm"><?= htmlspecialchars($student_info['student_name']) ?></span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[11px]">رقم القيد الجامعي:</span>
                        <span class="font-black text-blue-600 text-sm font-mono"><?= htmlspecialchars($student_info['student_id']) ?></span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[11px]">التخصص / القسم:</span>
                        <span class="font-extrabold text-slate-800 text-sm">قسم <?= htmlspecialchars($student_info['dept_name']) ?></span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[11px]">رقم الجلوس:</span>
                        <span class="font-bold text-slate-700 text-sm font-mono"><?= htmlspecialchars($student_info['seat_number']) ?></span>
                    </div>
                </div>

                <!-- جدول المواد والدرجات -->
                <div class="overflow-x-auto mb-6">
                    <table class="w-full text-center text-xs border border-slate-200">
                        <thead>
                            <tr class="bg-slate-800 text-white font-bold">
                                <th class="p-3 border border-slate-700">رمز المقرر</th>
                                <th class="p-3 border border-slate-700 text-right">اسم المقرر الأكاديمي</th>
                                <th class="p-3 border border-slate-700">الساعات</th>
                                <th class="p-3 border border-slate-700">أعمال الفصل (40)</th>
                                <th class="p-3 border border-slate-700">الامتحان النهائي (60)</th>
                                <th class="p-3 border border-slate-700">المجموع (100)</th>
                                <th class="p-3 border border-slate-700">التقدير</th>
                                <th class="p-3 border border-slate-700">النقاط (GPA)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            <?php foreach ($results as $res): ?>
                                <tr class="hover:bg-blue-50/30 transition-colors">
                                    <td class="p-3 font-mono font-bold text-slate-700 border border-slate-200"><?= htmlspecialchars($res['course_code']) ?></td>
                                    <td class="p-3 font-extrabold text-slate-900 text-right border border-slate-200"><?= htmlspecialchars($res['course_name']) ?></td>
                                    <td class="p-3 font-bold border border-slate-200"><?= $res['credit_hours'] ?></td>
                                    <td class="p-3 text-slate-700 border border-slate-200"><?= number_format($res['coursework_grade'], 1) ?></td>
                                    <td class="p-3 text-slate-700 border border-slate-200"><?= number_format($res['final_grade'], 1) ?></td>
                                    <td class="p-3 font-black text-blue-700 text-sm border border-slate-200"><?= number_format($res['total_grade'], 1) ?>%</td>
                                    <td class="p-3 font-black border border-slate-200">
                                        <span class="px-2 py-0.5 rounded text-[11px] <?= in_array($res['letter_grade'], ['A+', 'A']) ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800' ?>">
                                            <?= htmlspecialchars($res['letter_grade']) ?>
                                        </span>
                                    </td>
                                    <td class="p-3 font-mono font-bold border border-slate-200"><?= number_format($res['gpa_points'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="bg-slate-100 font-extrabold text-slate-900">
                                <td colspan="2" class="p-3 text-right">إجمالي الساعات المكتسبة: <?= $total_credits ?> ساعة</td>
                                <td colspan="2" class="p-3">متوسط الدرجات: <?= $avg_percentage ?>%</td>
                                <td colspan="4" class="p-3 text-left font-black text-blue-700 text-sm">
                                    المعدل التراكمي (GPA): <?= $gpa ?> / 4.00
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- شريط الحالة والتقدير العام -->
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 flex flex-col sm:flex-row items-center justify-between gap-4 mb-8">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-emerald-500 text-white flex items-center justify-center text-lg">
                            <i class="fa-solid fa-star"></i>
                        </div>
                        <div>
                            <h4 class="font-black text-sm">التقدير العام: <?= $gpa >= 3.7 ? 'ممتاز مع مرتبة الشرف 🌟' : ($gpa >= 3.0 ? 'جيد جداً مرتفع 👏' : 'ناجح بمستوى متميز') ?></h4>
                            <p class="text-xs text-emerald-700">تهانينا! أداء أكاديمي مشرف يعكس شغفك واجتهادك المستمر.</p>
                        </div>
                    </div>
                    <span class="bg-emerald-600 text-white px-3 py-1 rounded-xl font-black text-xs">الحالة: ناجح ومؤهل للفصل التالي</span>
                </div>

                <!-- التوقيعات والختم الرسمي -->
                <div class="grid grid-cols-3 gap-6 pt-6 border-t border-slate-200 text-center text-xs text-slate-600">
                    <div>
                        <div class="font-bold mb-8">مسجل الكلية والكنترول</div>
                        <div class="border-t border-dashed border-slate-400 pt-1 text-[11px]">أ. سالم باحارثة</div>
                    </div>
                    <div>
                        <div class="font-bold mb-8">عميد كلية الأفق</div>
                        <div class="border-t border-dashed border-slate-400 pt-1 text-[11px]">د. منصور الأحمدي</div>
                    </div>
                    <div class="flex flex-col items-center justify-center">
                        <div class="w-20 h-20 rounded-full border-2 border-dashed border-blue-600/60 flex items-center justify-center text-[10px] text-blue-600 font-bold rotate-12">
                            ختم الكنترول الرسمي
                        </div>
                    </div>
                </div>

                <!-- زر الطباعة -->
                <div class="mt-8 text-center no-print">
                    <button onclick="window.print()" class="px-8 py-3.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-black text-xs shadow-lg transition-all inline-flex items-center gap-2">
                        <i class="fa-solid fa-print"></i>
                        <span>طباعة كشف الدرجات الرسمي</span>
                    </button>
                </div>

            </div>

        <?php else: ?>
            <!-- لا توجد نتائج -->
            <div class="max-w-md mx-auto text-center py-12 p-8 rounded-3xl bg-white border border-slate-200 shadow-sm mb-12">
                <div class="w-16 h-16 rounded-full bg-amber-100 text-amber-500 flex items-center justify-center text-3xl mx-auto mb-4">
                    <i class="fa-solid fa-magnifying-glass-chart"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">لم يتم العثور على نتائج امتحانات!</h3>
                <p class="text-slate-500 text-xs leading-relaxed mb-6">
                    يرجى التأكد من إدخال رقم القيد الجامعي الصحيح (مثال: STD-2026-101) أو رقم الجلوس، أو مراجعة شؤون الطلاب والكنترول.
                </p>
                <a href="exam_results.php" class="px-6 py-2.5 rounded-xl bg-blue-600 text-white font-bold text-xs inline-block">إعادة المحاولة</a>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- ميزات نظام الامتحانات والتقييم في الكلية -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="glass-card rounded-3xl p-6 border border-slate-200 text-center">
            <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center text-xl mx-auto mb-3">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <h4 class="font-extrabold text-slate-900 text-base mb-1">دقة وموثوقية الكنترول</h4>
            <p class="text-slate-500 text-xs leading-relaxed">رصد إلكتروني مشفر لجميع الدرجات والاختبارات لضمان أعلى مستويات العدالة والشفافية.</p>
        </div>

        <div class="glass-card rounded-3xl p-6 border border-slate-200 text-center">
            <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl mx-auto mb-3">
                <i class="fa-solid fa-gauge-high"></i>
            </div>
            <h4 class="font-extrabold text-slate-900 text-base mb-1">نظام الساعات المعتمدة (GPA)</h4>
            <p class="text-slate-500 text-xs leading-relaxed">احتساب آلي للمعدل الفصلي والتراكمي بنظام النقاط المعتمد دولياً في كبرى الجامعات.</p>
        </div>

        <div class="glass-card rounded-3xl p-6 border border-slate-200 text-center">
            <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center text-xl mx-auto mb-3">
                <i class="fa-solid fa-medal"></i>
            </div>
            <h4 class="font-extrabold text-slate-900 text-base mb-1">لوحة شرف الأوائل</h4>
            <p class="text-slate-500 text-xs leading-relaxed">تكريم سنوي وحوافز مالية ومنح تفوق للمراتب الثلاث الأولى في كل تخصص وقسم.</p>
        </div>
    </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
