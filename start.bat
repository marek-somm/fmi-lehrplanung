start cmd /k "cd ./*-backend & php artisan serve"
start cmd /k "cd ./*-frontend & npm run serve"

start cmd /k "cd ./*-backend & code & exit"
start cmd /k "cd ./*-frontend & code & exit"

start firefox -new-window "http://localhost:8080