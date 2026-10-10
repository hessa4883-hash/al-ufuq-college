@echo off
chcp 65001 >nul
title تشغيل موقع كلية الأفق - Al-Ufuq College
echo ========================================================
echo        مرحباً بك في نظام كلية الأفق الأكاديمية
echo       Al-Ufuq College Management & Portal System
echo ========================================================
echo.
echo [1/3] جاري التحقق من تشغيل قاعدة بيانات MySQL في XAMPP...
echo.

if exist "C:\xampp\mysql\bin\mysqld.exe" (
    tasklist /FI "IMAGENAME eq mysqld.exe" 2>NUL | find /I /N "mysqld.exe">NUL
    if "%ERRORLEVEL%"=="0" (
        echo [✓] خادم MySQL يعمل بنجاح!
    ) else (
        echo [!] تشغيل خادم MySQL...
        start "" /B "C:\xampp\mysql\bin\mysqld.exe" --defaults-file="C:\xampp\mysql\bin\my.ini" --standalone
        timeout /t 3 /nobreak >nul
    )
)

echo.
echo [2/3] جاري فتح الموقع في المتصفح...
start http://localhost/alufuq-college/

echo.
echo [3/3] إذا كان Apache يعمل في XAMPP يمكنك استخدام الرابط أعلاه.
echo أو تشغيل خادم PHP المدمج على http://localhost:8080/ بالضغط على أي مفتاح...
echo.
pause

"C:\xampp\php\php.exe" -S localhost:8080 -t "%~dp0"
pause
