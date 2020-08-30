#!/bin/bash

usage () {
    me=`basename "$0"`

    echo "No arguments provided"
    echo
    echo "Use example:"
    echo "./${me} http://127.0.0.1:8088 10 2"
    echo
    echo "Where:"
    echo "- 100 is total requests number"
    echo "- 5 is number of concurrent requests"
    echo
    exit 1
}

get_milliseconds () {
    if [[ "$OSTYPE" == "linux-gnu"* ]]; then
        echo $(date +%s%N | cut -b1-13)
    elif [[ "$OSTYPE" == "darwin"* ]]; then
        echo $(python -c 'from time import time; print int(round(time() * 1000))')
    fi
}

if [ $# -lt 3 ]; then usage; fi

url=$1
requests=$2
parallel=$3

start_time=`get_milliseconds`

log_file="output_${requests}_${parallel}.log"
echo -n '' > $log_file

seq 1 $requests | xargs -I $ -n1 -P${parallel} curl --request GET \
    --location $url \
    --write-out '%{time_total} \n' \
    --output /dev/null \
    --silent >> $log_file

end_time=`get_milliseconds`

took=$(expr $end_time - $start_time)
took_seconds=$(awk "BEGIN {printf \"%.6f\",${took}/1000}")
per_second=$(awk "BEGIN {printf \"%.6f\",${requests}/${took_seconds}}")
total_time_operation=$(cat $log_file | sed 's/ //g' | tr '\n' '+' | sed 's/+$//g')
total_time=$(awk "BEGIN {printf \"%.6f\",$total_time_operation}")
avg_time=$(awk "BEGIN {printf \"%.6f\",${total_time}/${requests}}")

rm $log_file

echo -n "${requests} requests, "
echo "${parallel} concurrently:"

echo -n "URL ${url}, "
echo -n "took ${took_seconds} s, "
echo -n "${per_second} requests per second, "
echo -n "${avg_time} avg req time"
echo
