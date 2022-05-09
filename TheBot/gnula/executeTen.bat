@echo off
color 0B
echo Loading Gnula Bot...
php boot.php
:start
echo Re-Loading Gnula Bot...
php boot.php
goto :start
pause > nul