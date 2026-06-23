@echo off
powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0sync-both.ps1" %*
