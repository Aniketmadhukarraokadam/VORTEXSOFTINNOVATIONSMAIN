$urls = @(
    "https://www.vortexsoftinnovations.com/",
    "https://www.vortexsoftinnovations.com/careers.php",
    "https://www.vortexsoftinnovations.com/assets/usability-audit-fixes.css?v=20261027c"
)

foreach ($u in $urls) {
    $sw = [System.Diagnostics.Stopwatch]::StartNew()
    $res = Invoke-WebRequest -Uri $u -UseBasicParsing
    $sw.Stop()
    Write-Host "[$($res.StatusCode)] $($sw.ElapsedMilliseconds)ms - $u"
}

# Verify CSS has responsive system rules
$css = (Invoke-WebRequest -Uri "https://www.vortexsoftinnovations.com/assets/usability-audit-fixes.css?v=20261027c" -UseBasicParsing).Content
Write-Host "`nCSS verification:"
Write-Host "Contains 'ENTERPRISE MOBILE & IPAD/TABLET RESPONSIVE SYSTEM':" ($css.Contains("ENTERPRISE MOBILE & IPAD/TABLET RESPONSIVE SYSTEM"))
Write-Host "Contains 'iOS Safari requires min 16px':" ($css.Contains("iOS Safari requires min 16px"))
Write-Host "Contains 'Tablets / iPads (768px - 1024px)':" ($css.Contains("Tablets / iPads (768px - 1024px)"))
