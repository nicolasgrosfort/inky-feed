#!/bin/bash

cd /home/pi-inky-feed/inky-feed || exit 1

echo "$(date '+%Y-%m-%d %H:%M:%S'): git pull en cours..."
git pull --quiet
echo "$(date '+%Y-%m-%d %H:%M:%S'): git pull terminé."