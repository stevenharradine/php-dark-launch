#!/usr/bin/env bash
CURRENT_DIR=$(pwd)
SCRIPT_DIR=$(cd $(dirname ${0}); pwd)

${SCRIPT_DIR}/vendor/bin/apigen generate -s ${SCRIPT_DIR}/src -d ${SCRIPT_DIR}/docs