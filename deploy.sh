#!/bin/bash

user=$1
host=$2

ssh $user@$host -- "
  set -e
  git pull origin master
"