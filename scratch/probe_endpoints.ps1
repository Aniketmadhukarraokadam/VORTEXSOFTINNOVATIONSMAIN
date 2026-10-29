$urls = @(
    'https://www.vortexsoftinnovations.com/',
    'https://www.vortexsoftinnovations.com/about.php',
    'https://www.vortexsoftinnovations.com/careers.php',
    'https://www.vortexsoftinnovations.com/contact.php',
    'https://www.vortexsoftinnovations.com/service.php',
    'https://www.vortexsoftinnovations.com/blog.php',
    'https://www.vortexsoftinnovations.com/faq.php',
    'https://www.vortexsoftinnovations.com/privacy.php',
    'https://www.vortexsoftinnovations.com/terms.php',
    'https://www.vortexsoftinnovations.com/admin/login.php',
    'https://www.vortexsoftinnovations.com/admin/setup_db.php',
    'https://www.vortexsoftinnovations.com/admin/webhook_deploy.php',
    'https://www.vortexsoftinnovations.com/api/apply.php',
    'https://www.vortexsoftinnovations.com/api/contact.php',
    'https://www.vortexsoftinnovations.com/robots.txt',
    'https://www.vortexsoftinnovations.com/sitemap.xml',
    'https://www.vortexsoftinnovations.com/llms.txt'
)

foreach ($u in $urls) {
    try {
        $sw = [System.Diagnostics.Stopwatch]::StartNew()
        $resp = Invoke-WebRequest -Uri $u -Method Get -UseBasicParsing -TimeoutSec 10
        $sw.Stop()
        Write-Host "$($resp.StatusCode) [$($sw.ElapsedMilliseconds)ms] - $u" -ForegroundColor Green
    } catch {
        Write-Host "ERR [$($_.Exception.Message)] - $u" -ForegroundColor Red
    }
}
