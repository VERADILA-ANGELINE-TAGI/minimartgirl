@echo off
setlocal

set "PROJECT_DIR=%~dp0"
pushd "%PROJECT_DIR%" || (
    echo Could not open the MINIMARTGIRL project folder. 1>&2
    exit /b 1
)

set "KASIR_URL=%~1"
if not defined KASIR_URL set "KASIR_URL=http://127.0.0.1:8000/transaction"

set "EDGE_PATH=%ProgramFiles(x86)%\Microsoft\Edge\Application\msedge.exe"
if not exist "%EDGE_PATH%" set "EDGE_PATH=%ProgramFiles%\Microsoft\Edge\Application\msedge.exe"

set "CHROME_PATH=%ProgramFiles%\Google\Chrome\Application\chrome.exe"
if not exist "%CHROME_PATH%" set "CHROME_PATH=%ProgramFiles(x86)%\Google\Chrome\Application\chrome.exe"
if not exist "%CHROME_PATH%" set "CHROME_PATH=%LOCALAPPDATA%\Google\Chrome\Application\chrome.exe"

if not exist "%EDGE_PATH%" if not exist "%CHROME_PATH%" (
    echo Microsoft Edge or Google Chrome was not found. 1>&2
    popd
    exit /b 1
)

where php >nul 2>&1
if errorlevel 1 (
    echo PHP was not found. Start Laragon or add PHP to PATH, then try again. 1>&2
    popd
    exit /b 1
)

curl.exe --silent --show-error --fail "http://127.0.0.1:8000/login" --output nul >nul 2>&1
if errorlevel 1 (
    echo Starting the MINIMARTGIRL Laravel server...
    start "MINIMARTGIRL Laravel Server" /min /D "%PROJECT_DIR%" php artisan serve --host=127.0.0.1 --port=8000
)

for /L %%i in (1,1,30) do (
    curl.exe --silent --show-error --fail "http://127.0.0.1:8000/login" --output nul >nul 2>&1
    if not errorlevel 1 goto server_ready
    timeout /t 1 /nobreak >nul
)

echo The Laravel server did not respond at http://127.0.0.1:8000/login. 1>&2
echo Check that port 8000 is available and PHP dependencies are installed. 1>&2
popd
exit /b 1

:server_ready
echo MINIMARTGIRL server is ready.

if exist "%EDGE_PATH%" (
    start "" "%EDGE_PATH%" --user-data-dir="%LOCALAPPDATA%\MINIMARTGIRL-Kiosk" --kiosk "%KASIR_URL%" --edge-kiosk-type=fullscreen --kiosk-printing
    popd
    exit /b 0
)

start "" "%CHROME_PATH%" --user-data-dir="%LOCALAPPDATA%\MINIMARTGIRL-Kiosk" --kiosk "%KASIR_URL%" --kiosk-printing
popd
exit /b 0
