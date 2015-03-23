#!/bin/bash

ssh -vvv $USER@$HOST -- "
 set -e
 git pull origin master
"