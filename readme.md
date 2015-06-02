Codeblock [![Build Status](https://snap-ci.com/davidsoderberg/codeblock/branch/master/build_image)](https://snap-ci.com/davidsoderberg/codeblock/branch/master)
=========
Repository for codeblock.se

## Installation instructions
1. Install [composer](https://getcomposer.org/) and run `composer install` in this directory.
2. Create database and database user with all permissions.
3. Create an .env from .env.example and add all your config values in there.
4. Run `php artisan Install`.
5. Run `php artisan websocket` with [supervisor](http://supervisord.org/).

##Config needed for pphunit tests
If you would like to run the phpunit tests you will need following in your .env file:  
MAIL_HOST, MAIL_PORT, FROM_ADRESS, FROM_NAME, MAIL_USERNAME, MAIL_PASSWORD, MAIL_PRETEND, SOCKET_PORT, SOCKET_ADRESS, GITHUB_TOKEN
