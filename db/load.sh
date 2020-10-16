#!/bin/sh

BASE_DIR=$(dirname "$(readlink -f "$0")")
if [ "$1" != "test" ]; then
    psql -h localhost -U ucomment -d ucomment < $BASE_DIR/ucomment.sql
fi
psql -h localhost -U ucomment -d ucomment_test < $BASE_DIR/ucomment.sql
