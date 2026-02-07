#!/bin/sh
if [ ! -d "../../public/assets/base/node_modules" ]; then
  echo "Installing SSP assets base..."
  npm install --prefix ../../public/assets/base
fi
sass --quiet-deps -q --source-map public/assets/css/main.scss public/assets/css/main.css
