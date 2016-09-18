Codeblock [![Build Status](https://snap-ci.com/davidsoderberg/Codeblock/branch/master/build_image)](https://snap-ci.com/davidsoderberg/Codeblock/branch/master) [![StyleCI](https://styleci.io/repos/32723113/shield)](https://styleci.io/repos/32723113)
===============================================================================================================================================================
Repository for codeblock.se

## Installation instructions
1. Install [composer](https://getcomposer.org/) and run `composer install` in this directory.
2. Create database and database user with all permissions.
3. Create an .env from .env.example and add all your config values in there.
4. Run `php artisan Install`.
5. Run `php artisan websocket` with [supervisor](http://supervisord.org/).

## Config needed for pHpunit tests
If you would like to run the phpunit tests you will need following in your .env file:  
FROM_ADRESS, FROM_NAME, GITHUB_TOKEN

## Creating documentation
1. Run `php artisan make:doc`.
2. You can now view the documentation on routes `/doc` and `/doc/api`.
 
## Coding style
You can check if your contribution passes the styleguide by using phpcs and running following in your project root:    
1. Run `vendor/bin/phpcs --config-set default_standard PSR2`.  
2. Run `vendor/bin/phpcs path/to/files/to/check`. (This command should you run on all php files you are editing.)  
	