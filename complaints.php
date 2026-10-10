<?php
$page_title = 'الشكاوى الطلابية والمقترحات التطويرية';
require_once __DIR__ . '/config/db.php';

$track_ticket = clean_input($_GET['ticket'] ?? '');
$tracked_complaint = null;

// تتبع تذكرة سابقة
if (!empty($track_ticket)) {
    $stmt_tr = $pdo->prepare("SELECT * FROM complaints WHERE ticket_number = ? LIMIT 1");
    $stmt_tr->execute([$track_ticket]);
    $tracked_complaint = $stmt_tr->fetch();
}

// إرسال شكوى أو مقترح جديد
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_complaint'])) {
    $type = clean_input($_POST['type'] ?? 'مقترح تطويري');
    $category = clean_input($_POST['category'] ?? 'أكاديمي وتدريسي');
    $title = clean_input($_POST['title'] ?? '');
    $content = clean_input($_POST['content'] ?? '');
    $student_name = clean_input($_POST['student_name'] ?? '');
    $student_id = clean_input($_POST['student_id'] ?? '');
    $email = clean_input($_POST['email'] ?? '');
    $is_anonymous = isset($_POST['is_anonymous']) ? 1 : 0;

    if ($is_anonymous) {
        $student_name = 'طالب جامعي (مجهول)';
        $student_id = null;
    }

    if (!empty($title) && !empty($content)) {
        $ticket_number = 'TKT-2026-' . rand(100, 999);
        try {
            $stmt_ins = $pdo->prepare("INSERT INTO complaints (ticket_number, type, category, title, content, student_name, student_id, email, is_anonymous, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'قيد المراجعة')");
            $stmt_ins->execute([$ticket_number, $type, $category, $title, $content, $student_name, $student_id, $email, $is_anonymous]);

            set_flash('success', "تم إرسال تذكرتك بنجاح برقم: {$ticket_number}. يرجى الاحتفاظ برقم التذكرة لمتابعة رد إدارة الكلية.");
            header("Location: complaints.php?ticket={$ticket_number}#track");
            exit;
        } catch (PDOException $e) {
            set_flash('error', "حدث خطأ أثناء حفظ التذكرة: " . $e->getMessage());
        }
    } else {
        set_flash('error', "يرجى كتابة عنوان وتفاصيل الشكوى أو المقترح.");
    }
}

// جلب آخر المقترحات والشكاوى التي تم حلها والرد عليها (لعرض التفاعل والشفافية)
$resolved_complaints = $pdo->query("SELECT * FROM complaints WHERE status = 'تم الحل والرد' ORDER BY response_date DESC LIMIT 4")->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<!-- هيدر الصفحة والتحفيز -->
<section class="bg-gradient-to-b from-blue-900 to-indigo-900 text-white py-14 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <span class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-amber-400 text-blue-950 font-black text-xs shadow-sm mb-3">
            <i class="fa-solid fa-heart"></i> نافذة الشفافية والشراكة الطلابية
        </span>
        <h1 class="text-3xl sm:text-5xl font-black mb-4">الشكاوى الطلابية والمقترحات التطويرية</h1>
        <p class="text-blue-200 text-sm sm:text-base max-w-2xl mx-auto leading-relaxed mb-6">
            رأيك يصنع الفارق! نرحب بملاحظاتك البناءة ومقترحاتك لتطوير الكلية والخدمات الجامعية. تضمن عمادة الكلية معالجة كل تذكرة ومتابعتها مباشرة مع الإدارة.
        </p>

        <div class="flex justify-center gap-3">
            <a href="#new-ticket" class="px-6 py-3 rounded-2xl bg-amber-400 hover:bg-amber-300 text-slate-950 font-black text-xs shadow-md transition-all">
                <i class="fa-solid fa-pen-to-square ml-1"></i> تقديم شكوى أو مقترح
            </a>
            <a href="#track" class="px-6 py-3 rounded-2xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs border border-white/20 transition-all">
                <i class="fa-solid fa-magnifying-glass ml-1"></i> متابعة تذكرة سابقة
            </a>
        </div>
    </div>
</section>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 space-y-16">

    <!-- قسم متابعة التذكرة (إذا تم البحث أو إرسال جديد) -->
    <div id="track" class="max-w-3xl mx-auto scroll-mt-28">
        <div class="glass-card rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-md">
            <h3 class="text-lg font-black text-slate-900 mb-2 flex items-center gap-2">
                <i class="fa-solid fa-ticket text-blue-600"></i> متابعة تذكرة شكوى أو مقترح
            </h3>
            <p class="text-slate-500 text-xs mb-4">أدخل رقم التذكرة (مثل: TKT-2026-101) للاطلاع على حالة المعالجة ورد عمادة الكلية.</p>

            <form method="GET" action="complaints.php" class="flex gap-2 mb-6">
                <input type="text" name="ticket" value="<?= htmlspecialchars($track_ticket) ?>" required placeholder="رقم التذكرة (TKT-2026-101)..." class="flex-1 px-4 py-3 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-blue-600 font-mono">
                <button type="submit" class="px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs">
                    متابعة
                </button>
            </form>

            <?php if (!empty($track_ticket)): ?>
                <?php if ($tracked_complaint): ?>
                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                            <div>
                                <span class="text-xs font-mono font-bold text-blue-600"><?= htmlspecialchars($tracked_complaint['ticket_number']) ?></span>
                                <h4 class="font-black text-slate-900 text-base mt-0.5"><?= htmlspecialchars($tracked_complaint['title']) ?></h4>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-black <?= $tracked_complaint['status'] == 'تم الحل والرد' ? 'bg-emerald-100 text-emerald-800' : ($tracked_complaint['status'] == 'قيد المعالجة' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') ?>">
                                <?= htmlspecialchars($tracked_complaint['status']) ?>
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-xs text-slate-500">
                            <div>النوع: <b class="text-slate-800"><?= htmlspecialchars($tracked_complaint['type']) ?></b></div>
                            <div>التصنيف: <b class="text-slate-800"><?= htmlspecialchars($tracked_complaint['category']) ?></b></div>
                            <div>مقدم الطلب: <b class="text-slate-800"><?= htmlspecialchars($tracked_complaint['student_name']) ?></b></div>
                            <div>تاريخ التقديم: <b class="text-slate-800"><?= $tracked_complaint['created_at'] ?></b></div>
                        </div>

                        <div class="p-3.5 rounded-xl bg-white border border-slate-200 text-xs text-slate-700 leading-relaxed">
                            <span class="font-bold text-slate-900 block mb-1">تفاصيل الملاحظة أو المقترح:</span>
                            <?= nl2br(htmlspecialchars($tracked_complaint['content'])) ?>
                        </div>

                        <?php if (!empty($tracked_complaint['admin_response'])): ?>
                            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-900 leading-relaxed">
                                <div class="flex items-center gap-2 font-black text-emerald-800 mb-1">
                                    <i class="fa-solid fa-reply-all"></i>
                                    <span>رد عمادة الكلية وإدارتها (بتاريخ: <?= $tracked_complaint['response_date'] ?>):</span>
                                </div>
                                <p><?= nl2br(htmlspecialchars($tracked_complaint['admin_response'])) ?></p>
                            </div>
                        <?php else: ?>
                            <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-800">
                                <i class="fa-solid fa-hourglass-half ml-1"></i> تذكرتك قيد المراجعة حالياً من قبل الإدارة المختصة، وسيتم إرفاق الرد هنا قريباً.
                            </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold text-center">
                        لم يتم العثور على تذكرة مطابقة لرقم: <?= htmlspecialchars($track_ticket) ?>. تأكد من الرقم وأعد المحاولة.
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- نموذج تقديم شكوى أو مقترح جديد -->
    <div id="new-ticket" class="max-w-3xl mx-auto scroll-mt-28">
        <div class="glass-card rounded-3xl p-6 sm:p-8 border border-slate-200">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
                <div>
                    <h3 class="text-xl font-black text-slate-900">تقديم شكوى أو مقترح تطويري جديد</h3>
                    <p class="text-xs text-slate-500 mt-0.5">شاركنا أفكارك لتحسين البيئة الجامعية أو أبلغنا عن أي صعوبة تواجهك</p>
                </div>
                <span class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-feather-pointed"></i>
                </span>
            </div>

            <form method="POST" action="complaints.php" class="space-y-4">
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">نوع التذكرة *</label>
                        <select name="type" required class="w-full px-4 py-3 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-blue-600 bg-white">
                            <option value="مقترح تطويري">💡 مقترح تطويري وبنّاء</option>
                            <option value="شكوى طلابية">⚠️ شكوى طلابية</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">التصنيف والمجال *</label>
                        <select name="category" required class="w-full px-4 py-3 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-blue-600 bg-white">
                            <option value="أكاديمي وتدريسي">أكاديمي وهيئة التدريس</option>
                            <option value="مرافق وخدمات">مرافق وقاعات ومعامل وخدمات</option>
                            <option value="جداول وامتحانات">جداول دراسية وامتحانات</option>
                            <option value="أنشطة طلابية">أنشطة وبطولات طلابية</option>
                            <option value="شؤون مالية">شؤون مالية ورسوم</option>
                            <option value="أخرى">أخرى</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">عنوان الموضوع بإيجاز *</label>
                    <input type="text" name="title" required placeholder="مثال: مقترح لزيادة ساعات المعمل، أو ملاحظة حول جدول المحاضرات..." class="w-full px-4 py-3 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-blue-600">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">التفاصيل والشرح الوافي *</label>
                    <textarea name="content" rows="4" required placeholder="اكتب كل التفاصيل بوضوح واقترح الحلول المناسبة إن أمكن..." class="w-full px-4 py-3 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-blue-600"></textarea>
                </div>

                <!-- خيار إخفاء الهوية -->
                <div class="p-3.5 rounded-xl bg-blue-50/70 border border-blue-200 flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer text-xs font-bold text-blue-950">
                        <input type="checkbox" name="is_anonymous" id="anon-checkbox" onchange="toggleAnon(this)" class="h-4 w-4 text-blue-600 rounded">
                        <span>إرسال كمقترح/شكوى سرية (مجهول الهوية بدون إظهار اسمي) 🔒</span>
                    </label>
                </div>

                <!-- حقول اسم الطالب إذا لم يكن مجهولاً -->
                <div id="student-ident-fields" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">اسمك الكريم (اختياري)</label>
                        <input type="text" name="student_name" placeholder="أحمد علي" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-blue-600">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">رقم القيد الجامعي (إن وجد)</label>
                        <input type="text" name="student_id" placeholder="STD-2026-101" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-blue-600">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">البريد الإلكتروني للرد</label>
                        <input type="email" name="email" placeholder="email@domain.com" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-blue-600">
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 text-center">
                    <button type="submit" name="submit_complaint" class="w-full sm:w-auto px-10 py-3.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs shadow-md transition-all flex items-center justify-center gap-2 mx-auto">
                        <i class="fa-solid fa-paper-plane"></i>
                        <span>إرسال التذكرة والحصول على رقم المتابعة</span>
                    </button>
                </div>

            </form>
        </div>
    </div>

    <!-- نافذة الشفافية: نماذج من مقترحات تم الاستجابة لها وتطبيقها -->
    <div class="glass-card rounded-3xl p-6 sm:p-8 border border-slate-200">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
            <div>
                <h3 class="text-lg font-black text-slate-900">لوحة الشفافية: مقترحات طلابية تم تبنيها وتطبيقها بنجاح</h3>
                <p class="text-xs text-slate-500 mt-0.5">كيف تُحدث مقترحات الطلاب فارقاً حقيقياً داخل كليتنا</p>
            </div>
            <span class="text-xs bg-emerald-100 text-emerald-800 font-bold px-3 py-1 rounded-full">استجابة فورية 🟢</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php foreach ($resolved_complaints as $res): ?>
                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[11px] font-bold px-2 py-0.5 rounded bg-blue-100 text-blue-800"><?= htmlspecialchars($res['category']) ?></span>
                            <span class="text-[11px] text-slate-400 font-mono"><?= htmlspecialchars($res['ticket_number']) ?></span>
                        </div>
                        <h4 class="font-extrabold text-slate-900 text-sm mb-2"><?= htmlspecialchars($res['title']) ?></h4>
                        <p class="text-xs text-slate-600 leading-relaxed mb-3"><?= htmlspecialchars($res['content']) ?></p>
                    </div>

                    <div class="pt-3 border-t border-slate-200/80 bg-emerald-50/60 p-3 rounded-xl text-xs text-emerald-900">
                        <span class="font-bold block mb-1 text-emerald-800"><i class="fa-solid fa-circle-check text-emerald-600 ml-1"></i> الإجراء المتخذ من العمادة:</span>
                        <?= htmlspecialchars($res['admin_response']) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

</div>

<script>
function toggleAnon(checkbox) {
    const fields = document.getElementById('student-ident-fields');
    if (checkbox.checked) {
        fields.style.display = 'none';
    } else {
        fields.style.display = 'grid';
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
