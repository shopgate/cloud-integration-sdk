#!/bin/sh

mkdir release/shopgate-cloud-integration-sdk
rm-rf vendor
composer install -vvv --no-dev
rsync -av --exclude-from './release/exclude-filelist.txt' ./ release/shopgate-cloud-integration-sdk
cd release
zip -r ../shopgate-cloud-integration-sdk.zip shopgate-cloud-integration-sdk
