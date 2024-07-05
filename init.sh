composer install

php artisan migrate
php artisan shield:install --fresh -n
php artisan make:filament-user

npm install
npm run build