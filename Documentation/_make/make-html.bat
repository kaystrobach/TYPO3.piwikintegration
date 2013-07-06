@echo off
echo.
echo.

echo Build single file HTML version in build/singlehtml ...
call make.bat singlehtml
echo.
echo.

echo Build HTML version in build/html ...
call make.bat html
echo.
echo.

echo Start build/html/Index.html in browser ...
start build\html\Index.html
echo.
echo.

echo Finished.
pause