#!/bin/bash

# What both scripts need, in one place.

# The wall clock in milliseconds.
#
# GNU date knows %N and BSD date does not — it leaves the letter in the output, which used to
# be taken for a number and quietly produced an empty result. The macOS branch called Python 2
# by a name that has not existed on macOS for years, so the answer there was always nothing.
get_milliseconds () {
    local stamp
    stamp=$(date +%s%N 2>/dev/null)

    if [[ "$stamp" != *N* && -n "$stamp" ]]; then
        echo $((stamp / 1000000))
        return
    fi

    if command -v perl >/dev/null 2>&1; then
        perl -MTime::HiRes=time -e 'printf "%d\n", time * 1000'
        return
    fi

    if command -v python3 >/dev/null 2>&1; then
        python3 -c 'import time; print(int(time.time() * 1000))'
        return
    fi

    echo "Unable to read the clock in milliseconds on this system." >&2
    exit 3
}

# A number, or a refusal. An empty measurement is worse than none, because it looks like one.
require_number () {
    if ! [[ "$1" =~ ^[0-9]+$ ]] || [ "$1" -lt 1 ]; then
        echo "Expected a positive whole number, got: $1" >&2
        exit 1
    fi
}
