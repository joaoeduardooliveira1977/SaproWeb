@echo off
"C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe" "C:\projetos\saproweb-base\artisan" schedule:run >> "C:\projetos\saproweb-base\storage\logs\scheduler.log" 2>&1
