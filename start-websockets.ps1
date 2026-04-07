$ErrorActionPreference = "Stop"

$root = Split-Path -Parent $MyInvocation.MyCommand.Path
Set-Location $root

Write-Host "Starting Laravel WebSockets server..." -ForegroundColor Cyan
Write-Host "Close this window to stop the server." -ForegroundColor Yellow

$phpBinary = Join-Path (Split-Path -Parent $root) "php\php.exe"

if (-not (Test-Path $phpBinary)) {
    $phpBinary = "php"
}

& $phpBinary artisan websockets:serve
