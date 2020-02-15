# PandaPoker Backend Installation

composer install

## Setup the environment
cp .env.example .env

In the .env file, change the URL to be correct for your domain setup.

APP_URL=http://localhost:8081


# Running the web socket server
websocketd --port=8082 php eventStreamSubscriber.php

## Run the tests
./vendor/bin/phpunit --testsuite Unit

./vendor/bin/phpunit --testsuite Functional

Note that for the functional tests to work, your webserver will need to be up and running.
