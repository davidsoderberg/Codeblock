#!/bin/bash
# composer.phar update
# composer.phar dump-autoload -o
# php artisan insertPermissions

ssh $USER@$HOST -- "
 set -e
 git pull origin master
 php artisan migrate
"