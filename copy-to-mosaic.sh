#!/usr/bin/env bash

mkdir -p ../../mosaic/mosaic/connect/react/node_modules/rxante-oauth
rm ../../mosaic/mosaic/connect/react/node_modules/rxante-oauth/package.json
rm -rf ../../mosaic/mosaic/connect/react/node_modules/rxante-oauth/javascript
cp package.json ../../mosaic/mosaic/connect/react/node_modules/rxante-oauth/package.json
cp -r javascript ../../mosaic/mosaic/connect/react/node_modules/rxante-oauth/javascript
