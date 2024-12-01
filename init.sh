composer install

php artisan key:gen
php artisan migrate:fresh
php artisan make:filament-user
php artisan shield:generate --all
php artisan shield:super-admin

npm install
npm run build
