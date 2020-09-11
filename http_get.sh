#!/bin/bash

# MIT License
# Copyright (c) 2020 Radek Ziemniewicz

usage () {
    me=$(basename "$0")

    echo "No arguments provided"
    echo
    echo "Use example:"
    echo "./${me} http://127.0.0.1:8088 10 2"
    echo
    echo "Where:"
    echo "- 10 is a total number of requests"
    echo "- 2 is a number of concurrent requests"
    echo
    exit 1
}

get_milliseconds () {
    if [[ "$OSTYPE" == "linux-gnu"* ]]; then
        date +%s%N | cut -b1-13
    elif [[ "$OSTYPE" == "darwin"* ]]; then
        python -c 'from time import time; print int(round(time() * 1000))'
    fi
}

if [ $# -lt 3 ]; then usage; fi

url=$1
requests=$2
parallel=$3

start_time=$(get_milliseconds)

log_file="output_get_${requests}_${parallel}_${start_time}.log"
echo -n '' > $log_file

seq 1 $requests | xargs -I $ -n1 -P${parallel} curl --request GET \
    --location $url \
    --write-out '%{time_total}\n' \
    --output /dev/null \
    --silent >> $log_file

end_time=$(get_milliseconds)

took=$(($end_time - $start_time))
took_seconds=$(awk "BEGIN {printf \"%.6f\",${took}/1000}")
per_second=$(awk "BEGIN {printf \"%.6f\",${requests}/${took_seconds}}")
total_time_operation=$(sed 's/ //g' $log_file | tr '\n' '+' | sed 's/+$//g')
total_time=$(awk "BEGIN {printf \"%.6f\",$total_time_operation}")
avg_time=$(awk "BEGIN {printf \"%.6f\",${total_time}/${requests}}")

rm $log_file

echo
echo -n "${requests} requests, "
echo "${parallel} concurrently"

echo "URL ${url}"

echo '--------------------------------------------------------------------'
echo -n "Took ${took_seconds} s, "
echo -n "${per_second} requests per second, "
echo -n "${avg_time} avg req time"
echo
echo
