#!/usr/bin/env bash

mkdir -p ../../../mosaic/mosaic/connect/react/node_modules/rxante-oauth
rm ../../../mosaic/mosaic/connect/react/node_modules/rxante-oauth/package.json
rm -rf ../../../mosaic/mosaic/connect/react/node_modules/rxante-oauth/*
cp package.json ../../../mosaic/mosaic/connect/react/node_modules/rxante-oauth/package.json
cp pnpm-lock.yaml ../../../mosaic/mosaic/connect/react/node_modules/rxante-oauth/pnpm-lock.yaml
cp -r dist ../../../mosaic/mosaic/connect/react/node_modules/rxante-oauth/dist
cp -r src ../../../mosaic/mosaic/connect/react/node_modules/rxante-oauth/src
