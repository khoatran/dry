#!/bin/bash
php app/console gen-script $1
sh run-scripts/$1.sh