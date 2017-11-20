#!/bin/bash


while(true)
do
   datetime=`date +%F+%T`;
   echo "== $datetime =="
   echo "-- queues --"
   sudo rabbitmqctl list_queues name messages_ready messages_unacknowledged arguments

   echo "-- exchanges --"
   sudo rabbitmqctl list_exchanges name type arguments
   sleep 5;
done
