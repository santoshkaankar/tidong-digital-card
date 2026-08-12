@echo off
echo ==========================================
echo Tidong Auto Git Push Script
echo ==========================================

git add .
set /p msg="Enter update message (e.g. fix bug): "
if "%msg%"=="" set msg="Quick update"

git commit -m "%msg%"
git push origin main

echo ==========================================
echo Done! Code successfully pushed to GitHub.
echo ==========================================
pause