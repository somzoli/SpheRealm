composer install

php artisan migrate
php artisan make:filament-user
php artisan shield:install --fresh -n

npm install
npm run build