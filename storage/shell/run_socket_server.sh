#!/bin/bash
procesCount=0
for pid in `ps -ef | grep 'chat_server' | awk '{print $2}'` ;
do
    procesCount=$((procesCount+1))
done

#if chat socket procces was not founded - run socket server
if [[ "$procesCount" -ne 2 ]]
then
    php  /home/jobgrou2/public_html/application/artisan chat_server:serve
fi