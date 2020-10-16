#!/bin/sh

if [ "$1" = "travis" ]; then
    psql -U postgres -c "CREATE DATABASE ucomment_test;"
    psql -U postgres -c "CREATE USER ucomment PASSWORD 'ucomment' SUPERUSER;"
else
    sudo -u postgres dropdb --if-exists ucomment
    sudo -u postgres dropdb --if-exists ucomment_test
    sudo -u postgres dropuser --if-exists ucomment
    sudo -u postgres psql -c "CREATE USER ucomment PASSWORD 'ucomment' SUPERUSER;"
    sudo -u postgres createdb -O ucomment ucomment
    sudo -u postgres psql -d ucomment -c "CREATE EXTENSION pgcrypto;" 2>/dev/null
    sudo -u postgres createdb -O ucomment ucomment_test
    sudo -u postgres psql -d ucomment_test -c "CREATE EXTENSION pgcrypto;" 2>/dev/null
    LINE="localhost:5432:*:ucomment:ucomment"
    FILE=~/.pgpass
    if [ ! -f $FILE ]; then
        touch $FILE
        chmod 600 $FILE
    fi
    if ! grep -qsF "$LINE" $FILE; then
        echo "$LINE" >> $FILE
    fi
fi
