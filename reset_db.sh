#!/usr/bin/env bash

rm writable/db.sqlite && touch writable/db.sqlite
php spark migrate
php spark db:seed Admin
#php spark db:seed Student
php spark db:seed DeviceRfid
php spark db:seed Session