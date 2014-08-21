#!/bin/bash
rm -rf scripts/$1/run-box
mkdir scripts/$1/run-box
mkdir run-scripts
php app/console gen-script $1
sh scripts/$1/run-box/$1.sh