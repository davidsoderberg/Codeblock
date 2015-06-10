#!/bin/bash
# composer.phar update
# composer.phar dump-autoload -o
# php artisan InsertPermissions onlyInsert

ssh $USER@$HOST -- "
 set -e
 git pull origin master
 php artisan migrate
"