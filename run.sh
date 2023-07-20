
#!/bin/bash

# Path WordPress
WORDPRESS_DIR="./"
cd $WORDPRESS_DIR

php -S 127.0.0.1:8082 -t .
