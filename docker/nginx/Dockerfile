FROM nginx:alpine
COPY ./config/default.conf ./config/loadbalancer.conf /etc/nginx/conf.d/
COPY ./config/nginx.conf /etc/nginx/nginx.conf
EXPOSE 80