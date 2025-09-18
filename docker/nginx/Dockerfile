FROM nginx:alpine

COPY nginx.conf /etc/nginx/nginx.conf
COPY sites/ /etc/nginx/conf.d/

CMD ["nginx", "-g", "daemon off;"]
