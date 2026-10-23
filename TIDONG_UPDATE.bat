@echo off
setlocal EnableExtensions DisableDelayedExpansion
title TIDONG - Safe One Click Update V2
cd /d "%~dp0"
echo.
echo ============================================================
echo   TIDONG DIGITAL - SAFE UPDATE V2 (GitHub to Render)
echo ============================================================
echo.

rem Only run in the real local Laravel project.
if not exist "artisan" goto :wrongfolder
if not exist "Dockerfile" goto :wrongfolder
if not exist "phpunit.xml" goto :wrongfolder
where git >nul 2>&1
if errorlevel 1 goto :missinggit
where php >nul 2>&1
if errorlevel 1 goto :missingphp
where npm >nul 2>&1
if errorlevel 1 goto :missingnpm

set "REMOTE="
for /f "delims=" %%G in ('git remote get-url origin 2^>nul') do set "REMOTE=%%G"
echo %REMOTE% | findstr /I /C:"santoshkaankar/tidong-digital-card" >nul
if errorlevel 1 goto :wrongremote

set "BRANCH="
for /f "delims=" %%B in ('git branch --show-current 2^>nul') do set "BRANCH=%%B"
if /I not "%BRANCH%"=="main" goto :wrongbranch

rem Test config must point at separate digital_test_db; never run on live or production.
findstr /I /C:"digital_test_db" "phpunit.xml" >nul
if errorlevel 1 goto :testdbguard
if not exist "vendor\bin\phpunit.bat" goto :missingdeps

echo [1/6] Checking Laravel routes...
php artisan route:list --except-vendor >nul
if errorlevel 1 goto :failed

echo [2/6] Running PHPUnit against isolated digital_test_db...
call "vendor\bin\phpunit.bat" --configuration phpunit.xml --no-progress
if errorlevel 1 goto :failed

echo [3/6] Building CSS and JavaScript...
call npm run build
if errorlevel 1 goto :failed
if not exist "public\build\manifest.json" goto :failed

echo [4/6] Staging changed project source safely...
rem Plain git add -A respects .gitignore. Never pass ignored paths as positive pathspecs.
git add -A
if errorlevel 1 goto :failed
rem Non-ignored .env.example/.env.testing and archives must also stay local.
rem Reset only the INDEX. This does not delete or overwrite any local file.
git reset -q HEAD -- ":(glob)**/.env*" ":(glob)**/*.zip" ":(glob)**/*.7z" ":(glob)**/*.rar" ":(glob)**/*.sql" ":(glob)**/*.bak" ":(glob)**/*.pem" ":(glob)**/*.key" ":(glob)**/node_modules/**" ":(glob)**/vendor/**" ":(glob)**/public/build/**" ":(glob)**/storage/**"
if errorlevel 1 goto :failed

rem Last safety check for credentials/archives accidentally left staged.
git -c core.quotepath=false diff --cached --name-only | findstr /I /R /C:"\.env" /C:"\.zip$" /C:"\.7z$" /C:"\.rar$" /C:"\.sql$" /C:"\.pem$" /C:"\.key$" >nul
if not errorlevel 1 goto :secretguard

git diff --cached --check
if errorlevel 1 goto :failed

echo.
echo --- Files prepared for GitHub ---
git status --short
echo.
git diff --cached --quiet
if not errorlevel 1 goto :nochanges

choice /C YN /N /M "Push the staged code to GitHub main? (Y/N): "
if errorlevel 2 goto :cancelled

echo [5/6] Committing checked source changes...
git commit -m "Update TIDONG digital from validated local code"
if errorlevel 1 goto :failed

echo [6/6] Pushing to GitHub main (Render Auto Deploy)...
git push origin main
if errorlevel 1 goto :failed

echo.
echo SUCCESS: GitHub main updated.
echo Render Auto Deploy will run because this service is configured for auto deploy.
echo NOTE: Deploy completion and feature ON/OFF settings need separate verification.
echo NOTE: This never copies local MySQL data into live Supabase.
echo URL: https://tidong.in/
goto :end

:nochanges
echo No new tracked source changes. Nothing to push.
goto :end
:cancelled
echo Cancelled. No commit or push performed.
goto :end
:wrongfolder
echo ERROR: Keep this BAT in C:\xampp\htdocs\digital next to artisan.
goto :end
:missinggit
echo ERROR: Git not found in PATH.
goto :end
:missingphp
echo ERROR: PHP not found in PATH.
goto :end
:missingnpm
echo ERROR: npm not found in PATH.
goto :end
:wrongremote
echo ERROR: Git origin is not the intended TIDONG repository.
goto :end
:wrongbranch
echo ERROR: Branch is not main.
goto :end
:testdbguard
echo ERROR: PHPUnit config does not mention digital_test_db; tests blocked.
goto :end
:missingdeps
echo ERROR: PHPUnit is not installed locally.
goto :end
:secretguard
echo ERROR: A secret or archive is staged. Refusing commit/push.
goto :end
:failed
echo.
echo ERROR: Step failed. NO PUSH was performed by this run.
echo Read the message above for details.
goto :end
:end
echo.
pause
endlocal
