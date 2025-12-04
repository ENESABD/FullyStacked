FROM mysql:8.0

ENV MYSQL_ROOT_HOST=%

RUN echo '[mysqld]' > /etc/mysql/conf.d/laravel.cnf \
    && echo 'default-authentication-plugin=mysql_native_password' >> /etc/mysql/conf.d/laravel.cnf

EXPOSE 3306
