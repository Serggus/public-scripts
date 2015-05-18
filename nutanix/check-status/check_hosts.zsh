#!/bin/bash
# Program name: check_hosts.zsh

echo
date
echo

cat ./host_ips.txt |  while read output
do
    ping -c 1 -i 0.1 "$output" > /dev/null
    if [ $? -eq 0 ]; then
    echo "node $output is up" 
    else
    echo "node $output is down"
    fi
done

echo
