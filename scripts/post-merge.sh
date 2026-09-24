#!/usr/bin/env bash
set -euo pipefail

# The project uses committed, prebuilt application sources. Keep post-merge
# setup intentionally non-destructive so merges do not alter application data
# or rebuild either preview behind the user's back.
test -f .replit