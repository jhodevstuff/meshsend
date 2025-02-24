#!/bin/bash

if [ "$#" -ne 2 ]; then
  echo "Usage: $0 node message"
  exit 1
fi

node=$1
message=$2

conf="$(dirname "$0")/config.json"
if [ ! -f "$conf" ]; then
  echo "Config file not found"
  exit 1
fi

meshtasic=$(jq -r '.meshtastic_cli' "$conf")
host=$(jq -r '.host // empty' "$conf")

if [ -z "$meshtastic" ]; then
  echo "Meshtastic CLI bin path not known, run 'which meshtasic' and paste it in the config.json!"
  exit 1
fi

if [ "$node" = "__PUBLIC__" ]; then
  if [ -n "$host" ]; then
    "$meshtastic" --host "$host" --ch-index 0 --sendtext "$message" --ack
  else
    "$meshtastic" --ch-index 0 --sendtext "$message" --ack
  fi
else
  if [ -n "$host" ]; then
    "$meshtastic" --host "$host" --dest "$node" --sendtext "$message" --ack
  else
    "$meshtastic" --dest "$node" --sendtext "$message" --ack
  fi
fi

exit $?