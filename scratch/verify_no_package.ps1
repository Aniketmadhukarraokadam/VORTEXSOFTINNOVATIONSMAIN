$url = "https://www.vortexsoftinnovations.com/careers.php"
$res = Invoke-WebRequest -Uri $url -UseBasicParsing
$html = $res.Content

Write-Host "HTTP Status:" $res.StatusCode
Write-Host "Page size:" $html.Length "bytes"

# Check for salary/stipend/package mentions
$hasRupee = $html.Contains("₹")
$hasSalaryPill = $html.Contains("salary-pill")
$has15k = $html.Contains("15k")
$has25k = $html.Contains("25,000")
$has36L = $html.Contains("3.6L")
$has18L = $html.Contains("18.0L")

Write-Host "Contains Rupee symbol (₹):" $hasRupee
Write-Host "Contains 'salary-pill':" $hasSalaryPill
Write-Host "Contains '15k':" $has15k
Write-Host "Contains '25,000':" $has25k
Write-Host "Contains '3.6L':" $has36L
Write-Host "Contains '18.0L':" $has18L

# Check FAQ 5 content
if ($html -match 'id="faq5"[\s\S]*?id="collapse5"[\s\S]*?<div class="accordion-body[^"]*">([\s\S]*?)<\/div>') {
    Write-Host "`nFAQ 5 Body:"
    Write-Host $matches[1].Trim()
}
