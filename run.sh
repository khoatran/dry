#!/bin/bash
rm -rf run-scripts
mkdir run-scripts
php app/console gen-script $1
sh run-scripts/$1.sh