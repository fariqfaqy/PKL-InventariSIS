# Database Migration Consolidation Script
Write-Host "================================" -ForegroundColor Cyan
Write-Host "DATABASE CONSOLIDATION SCRIPT" -ForegroundColor Cyan
Write-Host "================================" -ForegroundColor Cyan
Write-Host ""

if (-not (Test-Path ".\artisan")) {
    Write-Host "ERROR: Script harus dijalankan dari root folder project!" -ForegroundColor Red
    exit 1
}

Write-Host "Script ini akan:" -ForegroundColor Yellow
Write-Host "1. Backup folder migrations yang ada" -ForegroundColor White
Write-Host "2. Mengkonsolidasikan migration files" -ForegroundColor White
Write-Host ""
Write-Host "WARNING: Pastikan database Anda sudah di-backup!" -ForegroundColor Red
Write-Host ""

$confirm = Read-Host "Lanjutkan? (yes/no)"
if ($confirm -ne "yes") {
    Write-Host "Dibatalkan." -ForegroundColor Yellow
    exit 0
}

Write-Host ""
Write-Host "Memulai proses..." -ForegroundColor Green
Write-Host ""

$timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
$backupFolder = "database\migrations_backup_$timestamp"

Write-Host "[1/6] Backup migrations..." -ForegroundColor Cyan
if (Test-Path "database\migrations") {
    Copy-Item -Path "database\migrations" -Destination $backupFolder -Recurse
    Write-Host "OK Backup ke: $backupFolder" -ForegroundColor Green
} else {
    Write-Host "ERROR migrations tidak ditemukan!" -ForegroundColor Red
    exit 1
}

Write-Host "[2/6] Buat folder baru..." -ForegroundColor Cyan
$tempMigrations = "database\migrations_new"
if (Test-Path $tempMigrations) {
    Remove-Item -Path $tempMigrations -Recurse -Force
}
New-Item -Path $tempMigrations -ItemType Directory | Out-Null
Write-Host "OK" -ForegroundColor Green

Write-Host "[3/6] Copy files yang tidak diubah..." -ForegroundColor Cyan
$filesToKeep = @(
    "0001_01_01_000000_create_users_table.php",
    "0001_01_01_000001_create_cache_table.php",
    "0001_01_01_000002_create_jobs_table.php",
    "2026_01_06_000001_add_role_to_users_table.php",
    "2026_01_06_000003_create_incoming_transactions_table.php",
    "2026_01_06_000005_create_activity_logs_table.php",
    "2026_01_08_000002_create_rack_assignments_table.php"
)

foreach ($file in $filesToKeep) {
    $sourcePath = "database\migrations\$file"
    if (Test-Path $sourcePath) {
        Copy-Item -Path $sourcePath -Destination "$tempMigrations\$file"
        Write-Host "  OK $file" -ForegroundColor Gray
    }
}
Write-Host "OK" -ForegroundColor Green

Write-Host "[4/6] Copy files consolidated..." -ForegroundColor Cyan
$consolidatedFiles = @(
    "2026_01_06_000002_create_stock_table_consolidated.php",
    "2026_01_06_000004_create_outgoing_transactions_table_consolidated.php",
    "2026_01_13_000003_create_item_requests_table_consolidated.php",
    "2026_01_20_130000_create_divisions_table_consolidated.php"
)

foreach ($file in $consolidatedFiles) {
    $sourcePath = "database\migrations_consolidated\$file"
    if (Test-Path $sourcePath) {
        Copy-Item -Path $sourcePath -Destination "$tempMigrations\$file"
        Write-Host "  OK $file" -ForegroundColor Gray
    } else {
        Write-Host "  SKIP $file (not found)" -ForegroundColor Yellow
    }
}
Write-Host "OK" -ForegroundColor Green

Write-Host "[5/6] Replace folder migrations..." -ForegroundColor Cyan
Remove-Item -Path "database\migrations" -Recurse -Force
Rename-Item -Path $tempMigrations -NewName "migrations"
Write-Host "OK" -ForegroundColor Green

Write-Host "[6/6] Verifikasi..." -ForegroundColor Cyan
$fileCount = (Get-ChildItem -Path "database\migrations" -Filter "*.php").Count
Write-Host "Total files: $fileCount" -ForegroundColor Green

Write-Host ""
Write-Host "================================" -ForegroundColor Cyan
Write-Host "SELESAI!" -ForegroundColor Green
Write-Host "================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Backup ada di: $backupFolder" -ForegroundColor Gray
Write-Host ""
