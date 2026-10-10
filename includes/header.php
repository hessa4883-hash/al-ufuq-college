<?php
require_once __DIR__ . '/../config/db.php';
$current_page = basename($_SERVER['PHP_SELF']);
$user = current_user();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title . ' - كلية الأفق' : 'كلية الأفق | Al-Ufuq College - بوابة التميز الأكاديمي' ?></title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        },
                        accent: {
                            gold: '#f59e0b',
                            emerald: '#10b981',
                            teal: '#0d9488',
                            cyan: '#06b6d4'
                        }
                    }
                }
            }
        }
    </script>
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-slate-50 text-slate-800 flex flex-col min-h-screen">

    <!-- شريط العبارات التشجيعية العلوي (Encouraging Bar) -->
    <div class="bg-gradient-to-r from-blue-900 via-indigo-900 to-blue-800 text-white text-xs md:text-sm py-2 px-4 shadow-sm border-b border-blue-700/50">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-2">
            <div class="flex items-center gap-2 font-medium">
                <span class="inline-flex items-center justify-center bg-amber-400 text-blue-950 px-2 py-0.5 rounded-full text-xs font-black shadow-sm animate-pulse">
                    <i class="fa-solid fa-sparkles ml-1"></i> إلهام اليوم
                </span>
                <span id="rotating-quote" class="transition-opacity duration-300 font-bold text-amber-200">
                    طموحك يبدأ من هنا نحو آفاق لا حدود لها 🚀
                </span>
            </div>
            <div class="flex items-center gap-4 text-xs text-blue-200">
                <span class="hidden md:inline-flex items-center gap-1">
                    <i class="fa-solid fa-graduation-cap text-amber-400"></i> القبول والتسجيل للعام الجامعي 2026/2027 متاح الآن
                </span>
                <span class="flex items-center gap-1">
                    <i class="fa-solid fa-phone-volume text-emerald-400"></i> الاستعلامات: 777000111
                </span>
            </div>
        </div>
    </div>

    <!-- رأس الصفحة والشريط الرئيسي (Navbar) -->
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200 shadow-sm transition-all duration-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                
                <!-- الشعار (Logo) -->
                <a href="index.php" class="flex items-center gap-3 group">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-blue-700 to-indigo-600 flex items-center justify-center text-white text-2xl shadow-md shadow-blue-500/20 group-hover:scale-105 transition-transform duration-200">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <div>
                        <div class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-1">
                            <span>كُـلّـيـة</span>
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">الأُفُــق</span>
                        </div>
                        <div class="text-[11px] text-slate-400 font-semibold tracking-wider uppercase">Al-Ufuq College</div>
                    </div>
                </a>

                <!-- روابط التنقل (Desktop Menu) -->
                <nav class="hidden xl:flex items-center gap-1">
                    <a href="index.php" class="px-3 py-2 rounded-xl text-sm font-bold transition-all <?= $current_page == 'index.php' ? 'text-blue-600 bg-blue-50 font-extrabold' : 'text-slate-700 hover:text-blue-600 hover:bg-slate-50' ?>">
                        <i class="fa-solid fa-house-chimney ml-1 text-xs opacity-70"></i> الرئيسية
                    </a>
                    <a href="admissions.php" class="px-3 py-2 rounded-xl text-sm font-bold transition-all <?= $current_page == 'admissions.php' ? 'text-blue-600 bg-blue-50 font-extrabold' : 'text-slate-700 hover:text-blue-600 hover:bg-slate-50' ?>">
                        <i class="fa-solid fa-user-plus ml-1 text-xs opacity-70"></i> التقديمات
                    </a>
                    <a href="admission_results.php" class="px-3 py-2 rounded-xl text-sm font-bold transition-all <?= $current_page == 'admission_results.php' ? 'text-blue-600 bg-blue-50 font-extrabold' : 'text-slate-700 hover:text-blue-600 hover:bg-slate-50' ?>">
                        <i class="fa-solid fa-id-card-clip ml-1 text-xs opacity-70"></i> نتائج القبول
                    </a>
                    <a href="departments.php" class="px-3 py-2 rounded-xl text-sm font-bold transition-all <?= $current_page == 'departments.php' ? 'text-blue-600 bg-blue-50 font-extrabold' : 'text-slate-700 hover:text-blue-600 hover:bg-slate-50' ?>">
                        <i class="fa-solid fa-layer-group ml-1 text-xs opacity-70"></i> الأقسام
                    </a>
                    <a href="calendar.php" class="px-3 py-2 rounded-xl text-sm font-bold transition-all <?= $current_page == 'calendar.php' ? 'text-blue-600 bg-blue-50 font-extrabold' : 'text-slate-700 hover:text-blue-600 hover:bg-slate-50' ?>">
                        <i class="fa-regular fa-calendar-days ml-1 text-xs opacity-70"></i> التقويم الدراسي
                    </a>
                    <a href="exam_results.php" class="px-3 py-2 rounded-xl text-sm font-bold transition-all <?= $current_page == 'exam_results.php' ? 'text-blue-600 bg-blue-50 font-extrabold' : 'text-slate-700 hover:text-blue-600 hover:bg-slate-50' ?>">
                        <i class="fa-solid fa-award ml-1 text-xs opacity-70"></i> نتائج الامتحانات
                    </a>
                    <a href="activities.php" class="px-3 py-2 rounded-xl text-sm font-bold transition-all <?= $current_page == 'activities.php' ? 'text-blue-600 bg-blue-50 font-extrabold' : 'text-slate-700 hover:text-blue-600 hover:bg-slate-50' ?>">
                        <i class="fa-solid fa-futbol ml-1 text-xs opacity-70"></i> النشاط الطلابي
                    </a>
                    <a href="inquiries.php" class="px-3 py-2 rounded-xl text-sm font-bold transition-all <?= $current_page == 'inquiries.php' ? 'text-blue-600 bg-blue-50 font-extrabold' : 'text-slate-700 hover:text-blue-600 hover:bg-slate-50' ?>">
                        <i class="fa-solid fa-circle-question ml-1 text-xs opacity-70"></i> الاستعلامات
                    </a>
                    <a href="complaints.php" class="px-3 py-2 rounded-xl text-sm font-bold transition-all <?= $current_page == 'complaints.php' ? 'text-blue-600 bg-blue-50 font-extrabold' : 'text-slate-700 hover:text-blue-600 hover:bg-slate-50' ?>">
                        <i class="fa-solid fa-envelope-open-text ml-1 text-xs opacity-70"></i> الشكاوى والمقترحات
                    </a>
                </nav>

                <!-- أزرار الدخول والإجراءات (Action Buttons) -->
                <div class="hidden sm:flex items-center gap-3">
                    <?php if (is_logged_in()): ?>
                        <?php if (is_admin()): ?>
                            <a href="admin_dashboard.php" class="inline-flex items-center gap-2 bg-gradient-to-r from-purple-600 to-indigo-700 text-white px-4 py-2.5 rounded-xl font-bold text-sm shadow-md hover:shadow-lg transition-all">
                                <i class="fa-solid fa-shield-halved"></i> لوحة الإدارة
                            </a>
                        <?php else: ?>
                            <a href="student_dashboard.php" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-4 py-2.5 rounded-xl font-bold text-sm shadow-md hover:shadow-lg transition-all">
                                <i class="fa-solid fa-user-graduate"></i> بوابة الطالب
                            </a>
                        <?php endif; ?>
                        <a href="logout.php" title="تسجيل الخروج" class="p-2.5 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-all">
                            <i class="fa-solid fa-arrow-right-from-bracket text-lg"></i>
                        </a>
                    <?php else: ?>
                        <a href="login.php" class="inline-flex items-center gap-2 text-slate-700 hover:text-blue-600 font-bold text-sm px-3 py-2 rounded-xl hover:bg-slate-100 transition-all">
                            <i class="fa-solid fa-right-to-bracket text-blue-600"></i> تسجيل الدخول
                        </a>
                        <a href="admissions.php" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-4 py-2.5 rounded-xl font-bold text-sm shadow-md hover:shadow-blue-500/30 hover:scale-[1.02] transition-all">
                            <i class="fa-solid fa-paper-plane"></i> قدّم الآن
                        </a>
                    <?php endif; ?>
                </div>

                <!-- زر القائمة للأجهزة الذكية (Mobile Button) -->
                <div class="flex xl:hidden items-center gap-2">
                    <button id="mobile-menu-btn" class="p-2 rounded-xl text-slate-700 hover:bg-slate-100 focus:outline-none">
                        <i class="fa-solid fa-bars-staggered text-2xl"></i>
                    </button>
                </div>

            </div>
        </div>

        <!-- القائمة المنسدلة للشاشات الصغيرة (Mobile Menu) -->
        <div id="mobile-menu" class="hidden xl:hidden border-t border-slate-200 bg-white px-4 pt-3 pb-6 space-y-1 shadow-lg">
            <a href="index.php" class="block px-3 py-2.5 rounded-xl text-base font-bold <?= $current_page == 'index.php' ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:bg-slate-50' ?>">الرئيسية</a>
            <a href="admissions.php" class="block px-3 py-2.5 rounded-xl text-base font-bold <?= $current_page == 'admissions.php' ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:bg-slate-50' ?>">التقديمات لبداية العام</a>
            <a href="admission_results.php" class="block px-3 py-2.5 rounded-xl text-base font-bold <?= $current_page == 'admission_results.php' ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:bg-slate-50' ?>">عرض نتائج القبول</a>
            <a href="departments.php" class="block px-3 py-2.5 rounded-xl text-base font-bold <?= $current_page == 'departments.php' ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:bg-slate-50' ?>">الأقسام الأدبية والعلمية</a>
            <a href="calendar.php" class="block px-3 py-2.5 rounded-xl text-base font-bold <?= $current_page == 'calendar.php' ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:bg-slate-50' ?>">التقويم الدراسي</a>
            <a href="exam_results.php" class="block px-3 py-2.5 rounded-xl text-base font-bold <?= $current_page == 'exam_results.php' ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:bg-slate-50' ?>">نتائج الامتحانات</a>
            <a href="activities.php" class="block px-3 py-2.5 rounded-xl text-base font-bold <?= $current_page == 'activities.php' ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:bg-slate-50' ?>">النشاط الطلابي والبطولات</a>
            <a href="inquiries.php" class="block px-3 py-2.5 rounded-xl text-base font-bold <?= $current_page == 'inquiries.php' ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:bg-slate-50' ?>">نظام استعلامات الكلية</a>
            <a href="complaints.php" class="block px-3 py-2.5 rounded-xl text-base font-bold <?= $current_page == 'complaints.php' ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:bg-slate-50' ?>">الشكاوى الطلابية والمقترحات</a>
            <div class="pt-4 border-t border-slate-100 flex flex-col gap-2">
                <?php if (is_logged_in()): ?>
                    <?php if (is_admin()): ?>
                        <a href="admin_dashboard.php" class="text-center bg-purple-600 text-white py-2.5 rounded-xl font-bold">لوحة تحكم الإدارة</a>
                    <?php else: ?>
                        <a href="student_dashboard.php" class="text-center bg-blue-600 text-white py-2.5 rounded-xl font-bold">لوحة الطالب</a>
                    <?php endif; ?>
                    <a href="logout.php" class="text-center bg-rose-50 text-rose-600 py-2.5 rounded-xl font-bold">تسجيل الخروج</a>
                <?php else: ?>
                    <a href="login.php" class="text-center bg-slate-100 text-slate-800 py-2.5 rounded-xl font-bold">تسجيل الدخول</a>
                    <a href="admissions.php" class="text-center bg-blue-600 text-white py-2.5 rounded-xl font-bold">تقديم طلب جديد</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- عرض رسائل التنبيه والنجاح (Flash Messages) -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full mt-4">
        <?php if ($msg = get_flash('success')): ?>
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between shadow-sm animate-bounce">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-xl"></i>
                    <span class="font-bold"><?= $msg ?></span>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($msg = get_flash('error')): ?>
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-triangle-exclamation text-rose-500 text-xl"></i>
                    <span class="font-bold"><?= $msg ?></span>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- محتوى الصفحة الرئيسي يبدأ هنا -->
    <main class="flex-grow">
