# MySQL SSH Tunnel Script
# This script creates an SSH tunnel to the remote MySQL server

$sshUser = "root"
$sshHost = "84.247.167.199"
$localPort = 3307
$remotePort = 3306
$sshPath = "C:\Program Files\Git\usr\bin\ssh.exe"

Write-Host "Starting SSH tunnel to $sshHost..." -ForegroundColor Green
Write-Host "Local port: $localPort -> Remote MySQL port: $remotePort" -ForegroundColor Cyan
Write-Host ""
Write-Host "Keep this window open while using the remote database." -ForegroundColor Yellow
Write-Host "Press Ctrl+C to stop the tunnel." -ForegroundColor Yellow
Write-Host ""

# Create SSH tunnel
& $sshPath -L ${localPort}:localhost:${remotePort} ${sshUser}@${sshHost} -N
