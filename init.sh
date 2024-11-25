composer install

php artisan key:gen
php artisan migrate
php artisan make:filament-user
php artisan shield:install --fresh --minimal -n

npm install
npm run build