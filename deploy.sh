#!/bin/bash

ssh $USER@$HOST -- "
 set -e
 git pull origin master
 php artisan migrate
"