#!/bin/bash

##############
# migrate.sh #
##############
#
# Description:
# Updates URL in database so the site can run on localhost
#

# Ensure environment variables are set
if [ -z "$WORDPRESS_DB_USER" ] || [ -z "$WORDPRESS_DB_PASSWORD" ] || [ -z "$WORDPRESS_DB_NAME" ] || [ -z "$WORDPRESS_URL" ]; then
  echo "One or more environment variables are missing. Please set WORDPRESS_DB_USER, WORDPRESS_DB_PASSWORD, WORDPRESS_DB_NAME, and WORDPRESS_URL."
  exit 1
fi

# Set environment variables
export db_host=db
export db_user=$WORDPRESS_DB_USER
export db_password=$WORDPRESS_DB_PASSWORD
export db_name=$WORDPRESS_DB_NAME
export dev_url=http://localhost
export prod_url=$WORDPRESS_URL

# Execute SQL commands
mariadb --host=$db_host --user=$db_user --password=$db_password --database=$db_name --execute="
UPDATE wp_options SET option_value = REPLACE(option_value, '${prod_url}', '${dev_url}') WHERE option_name = 'home' OR option_name = 'siteurl';
UPDATE wp_posts SET guid = REPLACE(guid, '${prod_url}', '${dev_url}');
UPDATE wp_posts SET post_content = REPLACE(post_content, '${prod_url}', '${dev_url}');
UPDATE wp_posts SET post_content = REPLACE(post_content, 'src=\"${prod_url}\"', 'src=\"${dev_url}\"');
UPDATE wp_posts SET guid = REPLACE(guid, '${prod_url}', '${dev_url}') WHERE post_type = 'attachment';
UPDATE wp_postmeta SET meta_value = REPLACE(meta_value, '${prod_url}', '${dev_url}');"
