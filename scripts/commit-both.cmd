@echo off
powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0commit-both.ps1" %*
