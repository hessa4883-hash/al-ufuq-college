<?php
$page_title = 'نظام الاستعلامات الذكي والأسئلة الشائعة';
require_once __DIR__ . '/config/db.php';

$search = clean_input($_GET['q'] ?? '');
$cat = clean_input($_GET['cat'] ?? 'all');

// معالجة إرسال استفسار مباشر
$inquiry_sent = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_direct_inquiry'])) {
    $name = clean_input($_POST['name'] ?? '');
    $phone = clean_input($_POST['phone'] ?? '');
    $email = clean_input($_POST['email'] ?? '');
    $question_text = clean_input($_POST['question'] ?? '');
    $category_select = clean_input($_POST['category'] ?? 'عام');

    if (!empty($question_text) && !empty($phone)) {
        // يمكن تخزينه في جدول الشكاوى والمقترحات أو حفظه
        try {
            $ticket = 'INQ-' . rand(1000, 9999);
            $stmt = $pdo->prepare("INSERT INTO complaints (ticket_number, type, category, title, content, student_name, email, status) VALUES (?, 'شكوى طلابية', 'أخرى', ?, ?, ?, ?, 'قيد المراجعة')");
            $stmt->execute([$ticket, "استفسار: " . mb_substr($question_text, 0, 40), $question_text . " | رقم الهاتف: " . $phone, $name ?: 'زائر', $email]);
            $inquiry_sent = true;
            set_flash('success', "تم إرسال استفسارك بنجاح برقم تذكرة: {$ticket}. سيقوم فريق القبول بالتواصل معك عبر الواتساب.");
        } catch (PDOException $e) {
            set_flash('error', "حدث خطأ أثناء الإرسال: " . $e->getMessage());
        }
    }
}

// جلب الأسئلة الشائعة من قاعدة البيانات
$sql = "SELECT * FROM inquiries_faq WHERE 1=1";
$params = [];
if (!empty($search)) {
    $sql .= " AND (question LIKE ? OR answer LIKE ? OR keywords LIKE ?)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}
if ($cat !== 'all') {
    $sql .= " AND category = ?";
    $params[] = $cat;
}
$sql .= " ORDER BY views DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$faqs = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<!-- هيدر الصفحة والبحث الفوري -->
<section class="bg-gradient-to-b from-blue-900 to-indigo-900 text-white py-14 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <span class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-cyan-400 text-blue-950 font-black text-xs shadow-sm mb-3">
            <i class="fa-solid fa-headset"></i> مركز خدمة واستعلامات الطلبة
        </span>
        <h1 class="text-3xl sm:text-5xl font-black mb-4">نظام استعلامات كلية الأفق</h1>
        <p class="text-blue-200 text-sm sm:text-base max-w-2xl mx-auto leading-relaxed mb-8">
            إجابات فورية ودقيقة على جميع تساؤلاتك حول شروط القبول، الرسوم الدراسية، الأقسام، معادلة المواد، ومرافق الحرم الجامعي.
        </p>

        <!-- مربع البحث الفوري -->
        <div class="max-w-2xl mx-auto bg-white rounded-2xl sm:rounded-3xl p-3 shadow-2xl">
            <form method="GET" action="inquiries.php" class="flex flex-col sm:flex-row gap-2">
                <div class="relative flex-1">
                    <i class="fa-solid fa-magnifying-glass absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="ابحث عن سؤالك هنا (شروط القبول، الرسوم، التمريض...)" class="w-full pl-4 pr-11 py-3 rounded-xl sm:rounded-2xl border-0 focus:outline-none focus:ring-2 focus:ring-blue-600 text-slate-900 text-sm font-medium">
                </div>
                <button type="submit" class="px-8 py-3 rounded-xl sm:rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs shadow-md transition-all">
                    بحث فوري
                </button>
            </form>
        </div>

        <!-- أزرار الفئات السريعة -->
        <div class="flex flex-wrap justify-center gap-2 mt-6">
            <a href="inquiries.php?cat=all" class="px-3.5 py-1.5 rounded-xl font-bold text-xs transition-all <?= $cat == 'all' ? 'bg-white text-blue-950 font-black' : 'bg-white/10 text-white hover:bg-white/20' ?>">الكل</a>
            <a href="inquiries.php?cat=<?= urlencode('شروط القبول') ?>" class="px-3.5 py-1.5 rounded-xl font-bold text-xs transition-all <?= $cat == 'شروط القبول' ? 'bg-white text-blue-950 font-black' : 'bg-white/10 text-white hover:bg-white/20' ?>">شروط القبول</a>
            <a href="inquiries.php?cat=<?= urlencode('الرسوم والمنح') ?>" class="px-3.5 py-1.5 rounded-xl font-bold text-xs transition-all <?= $cat == 'الرسوم والمنح' ? 'bg-white text-blue-950 font-black' : 'bg-white/10 text-white hover:bg-white/20' ?>">الرسوم والمنح</a>
            <a href="inquiries.php?cat=<?= urlencode('الأقسام والتخصصات') ?>" class="px-3.5 py-1.5 rounded-xl font-bold text-xs transition-all <?= $cat == 'الأقسام والتخصصات' ? 'bg-white text-blue-950 font-black' : 'bg-white/10 text-white hover:bg-white/20' ?>">الأقسام والتخصصات</a>
            <a href="inquiries.php?cat=<?= urlencode('الوثائق المطلوبة') ?>" class="px-3.5 py-1.5 rounded-xl font-bold text-xs transition-all <?= $cat == 'الوثائق المطلوبة' ? 'bg-white text-blue-950 font-black' : 'bg-white/10 text-white hover:bg-white/20' ?>">الوثائق المطلوبة</a>
            <a href="inquiries.php?cat=<?= urlencode('الأنشطة والخدمات') ?>" class="px-3.5 py-1.5 rounded-xl font-bold text-xs transition-all <?= $cat == 'الأنشطة والخدمات' ? 'bg-white text-blue-950 font-black' : 'bg-white/10 text-white hover:bg-white/20' ?>">الأنشطة والخدمات</a>
        </div>
    </div>
</section>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-14 space-y-14">

    <!-- قائمة الأسئلة التفاعلية (Accordion) -->
    <div class="space-y-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-black text-slate-900">الأسئلة الشائعة والاستفسارات الأكثر تكراراً</h3>
            <span class="text-xs font-bold text-slate-400"><?= count($faqs) ?> استفسار مسجل</span>
        </div>

        <?php if (!empty($faqs)): ?>
            <?php foreach ($faqs as $f): ?>
                <details class="glass-card rounded-2xl p-5 border border-slate-200/80 group [&_summary::-webkit-details-marker]:hidden">
                    <summary class="flex items-center justify-between cursor-pointer font-bold text-sm sm:text-base text-slate-900 group-hover:text-blue-600 transition-colors">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xs font-black flex-shrink-0">
                                <i class="fa-solid fa-question"></i>
                            </span>
                            <span><?= htmlspecialchars($f['question']) ?></span>
                        </div>
                        <span class="w-7 h-7 rounded-full bg-slate-100 group-hover:bg-blue-100 flex items-center justify-center text-xs transition-transform group-open:rotate-180 flex-shrink-0">
                            <i class="fa-solid fa-chevron-down"></i>
                        </span>
                    </summary>
                    <div class="mt-4 pt-4 border-t border-slate-100 text-xs sm:text-sm text-slate-600 leading-relaxed pr-11">
                        <?= nl2br(htmlspecialchars($f['answer'])) ?>
                        <div class="mt-3 flex items-center gap-3 text-[11px] text-slate-400">
                            <span class="bg-slate-100 px-2 py-0.5 rounded font-bold text-slate-600"><?= htmlspecialchars($f['category']) ?></span>
                            <span><i class="fa-regular fa-eye ml-1"></i> تمت المشاهدة <?= $f['views'] ?> مرة</span>
                        </div>
                    </div>
                </details>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="text-center py-10 p-6 bg-white rounded-2xl border border-slate-200">
                <p class="text-slate-500 text-sm">لم يتم العثور على سؤال يطابق بحثك. يمكنك استخدام نموذج الاستفسار المباشر أدناه وسنرد عليك فوراً!</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- نموذج إرسال استفسار مباشر للإدارة -->
    <div class="glass-card rounded-3xl p-8 border border-slate-200 bg-gradient-to-br from-white to-blue-50/40">
        <div class="max-w-2xl mx-auto">
            <div class="text-center mb-6">
                <span class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-600 inline-flex items-center justify-center text-2xl mb-2">
                    <i class="fa-solid fa-envelope-open-text"></i>
                </span>
                <h3 class="text-2xl font-black text-slate-900">لم تجد إجابة لسؤالك؟ اسأل إدارة الكلية مباشرة!</h3>
                <p class="text-slate-500 text-xs sm:text-sm mt-1">أرسل استفسارك وسيقوم أحد مسؤولي القبول والتسجيل بالرد عليك وتزويدك بالتفاصيل الكاملة.</p>
            </div>

            <form method="POST" action="inquiries.php" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">الاسم الكريم *</label>
                        <input type="text" name="name" required placeholder="اسمك الكريم" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-blue-600">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">رقم الواتساب للتواصل والرد *</label>
                        <input type="tel" name="phone" required placeholder="مثال: 771234567" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-blue-600">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">البريد الإلكتروني (اختياري)</label>
                        <input type="email" name="email" placeholder="email@example.com" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-blue-600">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">موضوع الاستفسار</label>
                        <select name="category" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-blue-600 bg-white">
                            <option value="شروط القبول">شروط القبول والتسجيل</option>
                            <option value="الرسوم والمنح">الرسوم الدراسية والمنح</option>
                            <option value="معادلة المواد">معادلة المواد والانتقال من كلية أخرى</option>
                            <option value="أخرى">استفسار عام</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">نص استفسارك بالتفصيل *</label>
                    <textarea name="question" rows="4" required placeholder="اكتب سؤالك واستفسارك هنا بكل وضوح..." class="w-full px-4 py-3 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-blue-600"></textarea>
                </div>

                <div class="text-center pt-2">
                    <button type="submit" name="submit_direct_inquiry" class="px-10 py-3.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs shadow-md transition-all">
                        <i class="fa-solid fa-paper-plane ml-1"></i> إرسال الاستفسار الآن
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
