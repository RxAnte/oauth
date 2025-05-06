#!/usr/bin/env bash

mkdir -p ../../rxeffect/web/node_modules/rxante-oauth
rm ../../rxeffect/web/node_modules/rxante-oauth/package.json
rm -rf ../../rxeffect/web/node_modules/rxante-oauth/*
cp package.json ../../rxeffect/web/node_modules/rxante-oauth/package.json
cp pnpm-lock.yaml ../../rxeffect/web/node_modules/rxante-oauth/pnpm-lock.yaml
cp -r dist ../../rxeffect/web/node_modules/rxante-oauth/dist
cp -r src ../../rxeffect/web/node_modules/rxante-oauth/src
