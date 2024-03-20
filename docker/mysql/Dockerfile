FROM mysql:latest

COPY generate_entrypoint.sh scripts/
COPY .env .
COPY src dbstruct

RUN chmod a+x /scripts/generate_entrypoint.sh

RUN touch /docker-entrypoint-initdb.d/init.sql \
    && chmod a+w /docker-entrypoint-initdb.d/init.sql \
    && chmod a+r /dbstruct/

RUN /bin/bash /scripts/generate_entrypoint.sh
