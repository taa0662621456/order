#!/usr/bin/env bash
set -euo pipefail
MSG="${1:-"CI failure"}"
EMOJI="${CI_FAILURE_EMOJI:-":x:"}"
URL="${SLACK_WEBHOOK_URL:-}"
[ -z "$URL" ] && { echo "SLACK_WEBHOOK_URL is not set"; exit 1; }
TAIL=""; 
if [ -f "$GITHUB_WORKSPACE/job.log" ]; then
  TAIL=$(tail -n 40 "$GITHUB_WORKSPACE/job.log" | sed 's/"/\\"/g')
fi
PAYLOAD=$(cat <<JSON
{
  "text": "${EMOJI} ${MSG}\n<${GITHUB_RUN_URL}|Open run>\n\`\`\`\n${TAIL}\n\`\`\`"
}
JSON
)
curl -fsSL -X POST -H 'Content-type: application/json' --data "$PAYLOAD" "$URL"
