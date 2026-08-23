#!/bin/bash

# MIT License
# Copyright (c) 2020 Radek Ziemniewicz

set -euo pipefail

# shellcheck source=lib/common.sh
. "$(dirname "$0")/lib/common.sh"

usage () {
    me=$(basename "$0")

    echo "No arguments provided"
    echo
    echo "Use example:"
    echo "./${me} 'php ../test.php' 10 2"
    echo
    echo "Where:"
    echo "- 10 is a total number of calls"
    echo "- 2 is number of concurrent requests"
    echo
    exit 1
}

if [ $# -lt 3 ]; then usage; fi

command=$1
requests=$2
parallel=$3

require_number "$requests"
require_number "$parallel"

result=$(eval "$command" 2>&1 || true)

if [[ ! $result =~ ^[0-9]+\.?[0-9]*$ ]]; then
    echo "Script not found or the result format is not float with a new line at the end (e.g. 1.2321)"

    exit 2
fi

start_time=$(get_milliseconds)

log_file=$(mktemp "${TMPDIR:-/tmp}/benchmark_command_XXXXXX")
trap 'rm -f "${log_file}"' EXIT

seq 1 "$requests" | xargs -I '{}' -n1 "-P${parallel}" sh -c "$command" >> "$log_file"

end_time=$(get_milliseconds)

took=$((end_time - start_time))
took_seconds=$(awk "BEGIN {printf \"%.6f\", ${took}/1000}")
per_second=$(awk "BEGIN {printf \"%.6f\", ${requests}/${took_seconds}}")
total_time=$(awk '{ total += $1 } END { printf "%.6f", total }' "$log_file")
avg_time=$(awk "BEGIN {printf \"%.6f\", ${total_time}/${requests}}")

echo
echo -n "${requests} calls, "
echo "${parallel} concurrently"

echo "Command \`${command}\`"

echo '-------------------------------------------------------------------'
echo -n "Took ${took_seconds} s, "
echo -n "${per_second} calls per second, "
echo -n "${avg_time} avg call time"
echo
echo
