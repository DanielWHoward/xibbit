#!/bin/sh
rm -rf .cache
rm -rf dist
npm run build
ls dist
