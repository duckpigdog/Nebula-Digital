FROM alpine:3.19
RUN apk add --no-cache php php-cli php-session php-exif
WORKDIR /var/www/html
COPY . /var/www/html
RUN mkdir -p /var/www/html/uploads
EXPOSE 80
CMD ["php", "-S", "0.0.0.0:80", "-t", "/var/www/html"]
