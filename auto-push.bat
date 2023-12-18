set message=%1
git add .
git commit -m"%message%"
echo "Pushing data to remote server"
git push -u origin master
pause