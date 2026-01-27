# Script to update kategori names in view files
# barang_sewa -> aset_sewa
# habis_pakai -> material_umum

Write-Host "Starting kategori update in view files..." -ForegroundColor Green

$viewsPath = "c:\Users\ASUS\Documents\PKL PLN\InventariSIS\resources\views"
$files = Get-ChildItem -Path $viewsPath -Filter "*.blade.php" -Recurse

$totalFiles = $files.Count
$filesChanged = 0
$totalReplacements = 0

foreach ($file in $files) {
    $content = Get-Content -Path $file.FullName -Raw
    $originalContent = $content
    
    # Replace 'barang_sewa' with 'aset_sewa'
    $content = $content -replace "'barang_sewa'", "'aset_sewa'"
    $content = $content -replace """barang_sewa""", """aset_sewa"""
    $content = $content -replace "barang_sewa", "aset_sewa"
    
    # Replace 'habis_pakai' with 'material_umum'
    $content = $content -replace "'habis_pakai'", "'material_umum'"
    $content = $content -replace """habis_pakai""", """material_umum"""
    $content = $content -replace "habis_pakai", "material_umum"
    
    # Only write if content changed
    if ($content -ne $originalContent) {
        Set-Content -Path $file.FullName -Value $content -NoNewline
        $filesChanged++
        $replacementCount = ([regex]::Matches($originalContent, "barang_sewa|habis_pakai")).Count
        $totalReplacements += $replacementCount
        Write-Host "  Updated: $($file.Name) ($replacementCount replacements)" -ForegroundColor Yellow
    }
}

Write-Host "`nUpdate complete!" -ForegroundColor Green
Write-Host "Total files scanned: $totalFiles" -ForegroundColor Cyan
Write-Host "Files changed: $filesChanged" -ForegroundColor Cyan
Write-Host "Total replacements made: ~$totalReplacements" -ForegroundColor Cyan
Write-Host "`nNote: Please review changes before committing." -ForegroundColor Magenta
