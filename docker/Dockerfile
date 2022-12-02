FROM alpine:3.17

# Requirements
RUN apk update \
	&& apk add php php-json \
	&& rm -rf /var/cache/apk/*
	
COPY startup /usr/local/bin
COPY pepper.php /app/

# Set runtime commands
ENTRYPOINT ["startup"]
