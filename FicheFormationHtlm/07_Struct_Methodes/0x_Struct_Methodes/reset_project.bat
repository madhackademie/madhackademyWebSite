@echo off
set "ROOT=%~dp0"
rmdir /s /q "%ROOT%src"
mkdir "%ROOT%src"
xcopy "%ROOT%Source\*" "%ROOT%src\" /E /H /C /I
