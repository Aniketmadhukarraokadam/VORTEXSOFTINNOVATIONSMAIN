$txt = (Invoke-WebRequest -Uri "https://www.vortexsoftinnovations.com/llms.txt" -UseBasicParsing).Content
Write-Host "llms.txt contains 'Stipend':" ($txt.Contains("Stipend"))
Write-Host "llms.txt contains 'Salary':" ($txt.Contains("Salary"))
Write-Host "llms.txt contains '₹':" ($txt.Contains("₹"))

$full = (Invoke-WebRequest -Uri "https://www.vortexsoftinnovations.com/llms-full.txt" -UseBasicParsing).Content
Write-Host "llms-full.txt contains 'Stipend':" ($full.Contains("Stipend"))
Write-Host "llms-full.txt contains 'Salary':" ($full.Contains("Salary"))
Write-Host "llms-full.txt contains '₹':" ($full.Contains("₹"))
