/**
 * Al-Ufuq College - Main Interactive JavaScript
 * السكربت الرئيسي لتفاعلات موقع كلية الأفق والمساعد الذكي
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. القائمة المتنقلة للشاشات الصغيرة
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    }

    // 2. تدوير العبارات التشجيعية تلقائياً
    const quotes = [
        "طموحك يبدأ من هنا نحو آفاق لا حدود لها 🚀",
        "اصنع مستقبلك بشغف وتميّز 🌟",
        "لكل مجتهد نصيب.. وأفقك يتسع لكل أحلامك 🎓",
        "التعليم رحلة ملهمة لصناعة القادة ✨",
        "كن فخوراً بخطواتك.. أنت تصنع الفرق 💡"
    ];
    const quoteElement = document.getElementById('rotating-quote');
    if (quoteElement) {
        let quoteIndex = 0;
        setInterval(() => {
            quoteIndex = (quoteIndex + 1) % quotes.length;
            quoteElement.style.opacity = '0';
            setTimeout(() => {
                quoteElement.innerText = quotes[quoteIndex];
                quoteElement.style.opacity = '1';
            }, 300);
        }, 5000);
    }

    // 3. المساعد الذكي للاستعلامات الفورية (Chatbot)
    const chatToggle = document.getElementById('chat-toggle-btn');
    const chatBox = document.getElementById('chat-box-popup');
    const chatClose = document.getElementById('chat-close-btn');
    const chatInput = document.getElementById('chat-input-field');
    const chatSend = document.getElementById('chat-send-btn');
    const chatMessages = document.getElementById('chat-messages-container');

    if (chatToggle && chatBox) {
        chatToggle.addEventListener('click', () => {
            if (chatBox.style.display === 'flex') {
                chatBox.style.display = 'none';
            } else {
                chatBox.style.display = 'flex';
                if (chatInput) chatInput.focus();
            }
        });
    }

    if (chatClose && chatBox) {
        chatClose.addEventListener('click', () => {
            chatBox.style.display = 'none';
        });
    }

    if (chatSend && chatInput && chatMessages) {
        const handleSend = () => {
            const query = chatInput.value.trim();
            if (!query) return;

            // رسالة المستخدم
            appendChatMessage('user', query);
            chatInput.value = '';

            // مؤشر الكتابة
            const typingIndicator = document.createElement('div');
            typingIndicator.className = 'text-xs text-gray-500 italic p-2';
            typingIndicator.innerText = 'مساعد الأفق الذكي يكتب الآن...';
            chatMessages.appendChild(typingIndicator);
            chatMessages.scrollTop = chatMessages.scrollHeight;

            // إرسال للـ API أو استجابة ذكية فورية
            setTimeout(() => {
                chatMessages.removeChild(typingIndicator);
                const botReply = generateSmartReply(query);
                appendChatMessage('bot', botReply);
            }, 700);
        };

        chatSend.addEventListener('click', handleSend);
        chatInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') handleSend();
        });
    }

    function appendChatMessage(sender, text) {
        const msgDiv = document.createElement('div');
        msgDiv.className = `flex mb-3 ${sender === 'user' ? 'justify-start' : 'justify-end'}`;
        
        const bubble = document.createElement('div');
        if (sender === 'user') {
            bubble.className = 'bg-blue-600 text-white p-3 rounded-2xl rounded-tr-sm text-sm max-w-[85%] shadow-sm';
        } else {
            bubble.className = 'bg-gray-100 text-gray-800 p-3 rounded-2xl rounded-tl-sm text-sm max-w-[85%] shadow-sm border border-gray-200';
        }
        bubble.innerHTML = text;
        msgDiv.appendChild(bubble);
        chatMessages.appendChild(msgDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function generateSmartReply(input) {
        const q = input.toLowerCase();
        if (q.includes('تمريض') || q.includes('التمريض')) {
            return '💉 <b>قسم التمريض</b> يقبل خريجي الثانوية (علمي) بنسبة 75% فما فوق. مدة الدراسة 4 سنوات وتدريب سريري ممتاز في أفضل المستشفيات.';
        }
        if (q.includes('مختبر') || q.includes('طب ومختبرات')) {
            return '🔬 <b>قسم الطب والمختبرات</b> يقبل العلمي بنسبة 78% فما فوق، ويحتوي على أحدث أجهزة التحليل والتشخيص الطبي والمناعي.';
        }
        if (q.includes('تقنية') || q.includes('it') || q.includes('حاسوب') || q.includes('برمج')) {
            return '💻 <b>قسم تقنية المعلومات</b> يقبل العلمي بنسبة 70%، ويشمل مجالات البرمجة، والذكاء الاصطناعي، والأمن السيبراني، وتطوير التطبيقات.';
        }
        if (q.includes('إدارة') || q.includes('ادارة') || q.includes('اعمال')) {
            return '📊 <b>قسم إدارة الأعمال</b> يقبل العلمي والأدبي بنسبة 60% فما فوق، ويؤهل لقيادة المنظمات والتسويق الرقمي وريادة المشاريع.';
        }
        if (q.includes('انجليزي') || q.includes('لغات') || q.includes('ترجمة')) {
            return '🌐 <b>قسم لغات وترجمة - إنجليزي</b> يقبل العلمي والأدبي بنسبة 62%، ويتميز بالتركيز على الترجمة الفورية والطلاقة اللغوية.';
        }
        if (q.includes('محاسب') || q.includes('نظم معلومات محاسبية')) {
            return '📈 <b>قسم نظم معلومات محاسبية</b> يقبل العلمي والأدبي بنسبة 65%، ويجمع بين المحاسبة والأنظمة السحابية والتحليل المالي.';
        }
        if (q.includes('رسوم') || q.includes('تكلفة') || q.includes('منح') || q.includes('تخفيض')) {
            return '💰 توفر كلية الأفق رسوماً ميسرة مع أقساط مريحة، بالإضافة لمنحة 30% لأوائل المحافظات و20% للمعدلات 90% فأعلى و15% للإخوة.';
        }
        if (q.includes('شروط') || q.includes('قبول') || q.includes('تسجيل') || q.includes('وثائق')) {
            return '📄 <b>المستندات المطلوبة:</b> أصل استمارة الثانوية + صور طبق الأصل، صورة الهوية، 6 صور شمسية 4x6، وسند سداد رسوم التسجيل. يمكنك التقديم فوراً من صفحة <a href="admissions.php" class="text-blue-600 underline font-bold">التقديمات للكلية</a>.';
        }
        if (q.includes('امتحان') || q.includes('نتيجة') || q.includes('درجات')) {
            return '🎓 يمكنك مراجعة نتائج الامتحانات فوراً من خلال صفحة <a href="exam_results.php" class="text-blue-600 underline font-bold">نتائج الامتحانات</a> بإدخال رقم القيد الجامعي.';
        }
        if (q.includes('شكوى') || q.includes('مقترح')) {
            return '✉️ نرحب بملاحظاتك دائماً! يمكنك كتابة شكوى أو مقترح عبر صفحة <a href="complaints.php" class="text-blue-600 underline font-bold">الشكاوى والمقترحات</a> وسيرد عليك عميد الكلية فوراً.';
        }
        if (q.includes('كرة قدم') || q.includes('رياضة') || q.includes('دورة') || q.includes('نشاط')) {
            return '⚽ بطولة <b>كأس الأفق لخماسيات كرة القدم</b> جارية الآن، بالإضافة للأيام الثقافية والحملات الصحية في صفحة <a href="activities.php" class="text-blue-600 underline font-bold">النشاط الطلابي</a>.';
        }
        return 'مرحباً بك في كلية الأفق! 🌟 يسعدني مساعدتك في شروط القبول، التخصصات، الرسوم، نتائج الامتحانات، أو الأنشطة الطلابية. يمكنك طرح سؤالك بصيغة أخرى أو تصفح القائمة الرئيسية.';
    }
});

// دالة الطباعة لكشف الدرجات أو إشعار القبول
function printCertificate() {
    window.print();
}
