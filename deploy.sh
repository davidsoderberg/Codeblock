#!/bin/bash

sudo traceroute -T $HOST
sudo traceroute -T -p 22 $HOST

ssh $USER@$HOST -- "
 set -e
 git pull origin master
"