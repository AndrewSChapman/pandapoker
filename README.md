# Installation Instructions

Your server needs to be running Redis.  If it's not installed, you can probably install it with

apt install redis

You need PHP 7.2+ with the php-redis extension enabled, e.g.

apt install php-redis
service php7.2-fpm restart

cd backend

composer install

mkdir storage
sudo chown pandapoker:www-data storage

cp .env.example .env

edit the .env file and adjust the JWT key to a random key.   Also change the domain
name to be correct for wherever you're going to be serving the website from.

./vendor/bin/phpunit --testsuite Unit

(These should all pass)


cd ../frontend

npm install

npm run build

cd ..

mkdir www

cp -rfp frontend/public/* www/

cp -rfp frontend/dist/* www/


