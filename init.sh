composer install

php artisan migrate:fresh
php artisan make:filament-user
php artisan shield:install --fresh --minimal -n

npm install
npm run build