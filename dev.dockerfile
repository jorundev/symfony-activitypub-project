FROM alpine:latest AS symfony

# Install dependencies
RUN apk update
RUN apk add bash composer curl git redis
RUN apk add php81-tokenizer php81-ctype php81-iconv php81-session php81-xml php81-simplexml \
	php81-dom php81-posix php81-tokenizer php81-intl php81-pdo php81-pdo_pgsql

# Install symfony-cli
RUN curl -sS https://get.symfony.com/cli/installer | bash
RUN mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

WORKDIR /app

ENTRYPOINT ["./docker/entrypoint.sh"]
