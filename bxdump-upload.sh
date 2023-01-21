#!/bin/bash
tar -czf ~/bxdump/bxdump-upload.tar.gz -X ~/bxdump/.ignore.upload --no-recursion --files-from <(find ./upload -size -10M -o -type d)
