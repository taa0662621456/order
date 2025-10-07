#!/usr/bin/env bash
set -euo pipefail
MSG="${1:-"CI failure"}"
EMOJI="${CI_FAILURE_EMOJI:-"❌"}"
BOT="${TELEGRAM_BOT_TOKEN:-}"
CHAT="${TELEGRAM_CHAT_ID:-}"
[ -z "$BOT" ] && { echo "TELEGRAM_BOT_TOKEN is not set"; exit 1; }
[ -z "$CHAT" ] && { echo "TELEGRAM_CHAT_ID is not set"; exit 1; }
TAIL=""
if [ -f "$GITHUB_WORKSPACE/job.log" ]; then
  TAIL=$(tail -n 40 "$GITHUB_WORKSPACE/job.log")
fi
TEXT="${EMOJI} ${MSG}%0A${GITHUB_RUN_URL}%0A\`\`\`%0A${TAIL}%0A\`\`\`"
curl -fsSL "https://api.telegram.org/bot${BOT}/sendMessage" \
  -d "chat_id=${CHAT}" \
  -d "text=${TEXT}" \
  -d "parse_mode=MarkdownV2"
