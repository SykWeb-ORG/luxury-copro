ARG WORDPRESS_IMAGE=wordpress:latest
ARG WORDPRESS_CLI_IMAGE=wordpress:cli

FROM ${WORDPRESS_IMAGE} AS wordpress

ARG WORDPRESS_UID=1000
ARG WORDPRESS_GID=1000

COPY docker/wordpress/set-www-data-id.sh /usr/local/bin/set-www-data-id
RUN chmod +x /usr/local/bin/set-www-data-id \
    && set-www-data-id

COPY docker/wordpress/permissions-entrypoint.sh /usr/local/bin/permissions-entrypoint
RUN chmod +x /usr/local/bin/permissions-entrypoint

ENTRYPOINT ["permissions-entrypoint"]
CMD ["apache2-foreground"]

FROM ${WORDPRESS_CLI_IMAGE} AS cli

USER root

ARG WORDPRESS_UID=1000
ARG WORDPRESS_GID=1000

COPY docker/wordpress/set-www-data-id.sh /usr/local/bin/set-www-data-id
RUN chmod +x /usr/local/bin/set-www-data-id \
    && set-www-data-id

USER www-data
