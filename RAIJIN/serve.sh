#!/bin/bash

# Get the absolute path of the project directory
PROJECT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

# Set OPENSSL_CONF environment variable to the custom config file
export OPENSSL_CONF="$PROJECT_DIR/openssl_custom.cnf"

echo "Starting Laravel Server with Custom OpenSSL Config..."
echo "Config Path: $OPENSSL_CONF"

# Run the artisan serve command
php artisan serve "$@"
