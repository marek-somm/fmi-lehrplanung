start cmd /k "cd ./*-backend & php artisan serve"
start cmd /k "cd ./*-frontend & npm run serve"

start cmd /k "cd ./*-backend & c & exit"
start cmd /k "cd ./*-frontend & c & exit"

start firefox -new-window "http://localhost:8080