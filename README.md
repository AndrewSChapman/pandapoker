# Installation Instructions

cd backend

composer install

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


