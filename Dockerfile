# debian base image
FROM debian:bullseye-slim
# install dependencies (php, nginx, python, etc) and then delete the cache and unneeded programs
RUN apt-get update && apt-get install -y sudo php php-common php-cli php-fpm php-json php-pdo php-zip php-gd php-mbstring php-curl nginx python3 python3-pip git composer && apt-get clean autoclean && apt-get autoremove --yes
# create the 'docker' user and add it to the sudo group
RUN adduser --disabled-password --gecos '' docker && adduser docker sudo && echo '%sudo ALL=(ALL) NOPASSWD:ALL' >> /etc/sudoers
# create code directory and set permissions on code directory and www directory.
RUN mkdir /home/docker/code && chown -R docker:docker /home/docker/code/ && chown -R docker:docker /var/www/
# switch to the 'docker' user
USER docker
# nginx config
RUN sudo rm /etc/nginx/sites-enabled/default && sudo rm /etc/nginx/nginx.conf && echo "user docker docker; worker_processes auto; pid /run/nginx.pid; include /etc/nginx/modules-enabled/*.conf; events {\nworker_connections 768;}\n http {\nsendfile on;\ntcp_nopush on;\ntypes_hash_max_size 2048;\nserver_tokens off;\ninclude /etc/nginx/mime.types;\ndefault_type application/octet-stream;\nssl_protocols TLSv1 TLSv1.1 TLSv1.2 TLSv1.3; # Dropping SSLv3, ref: POODLE\nssl_prefer_server_ciphers on;\naccess_log /var/log/nginx/access.log;\nerror_log /var/log/nginx/error.log;\ngzip on;   	server {     		listen 80 default_server;     		server_name _;  		server_name_in_redirect off;     		root  /var/www/html/; 		index index.php;  location ~* .(js|css)$ { expires 1d; }		location ~ \.php$ {\n\n			include snippets/fastcgi-php.conf;\n\n			fastcgi_pass unix:/home/docker/code/php7.4-fpm.sock;}}}\ninclude /etc/nginx/conf.d/*.conf;\ninclude /etc/nginx/sites-enabled/*;" | sudo tee -a /etc/nginx/nginx.conf > /dev/null
# php config
RUN sudo rm /etc/php/7.4/fpm/pool.d/www.conf && echo "[www]\nuser = docker\ngroup = docker\nlisten = /home/docker/code/php7.4-fpm.sock\nlisten.owner = docker\nlisten.group = docker\npm = dynamic\npm.max_children = 5\npm.start_servers = 2\npm.min_spare_servers = 1\npm.max_spare_servers = 3" | sudo tee -a /etc/php/7.4/fpm/pool.d/www.conf > /dev/null
# environment variables
ENV PYTHONUNBUFFERED='true'
# add requirements to home directory
ADD requirements.txt /
# install requirements from requirements.txt
RUN pip install --no-cache-dir -r /requirements.txt
# startup command
CMD cd /home/docker/code && sudo /etc/init.d/php7.4-fpm start && echo "running" && sudo nginx -g 'daemon off;'
# copy web folder to www directory
ADD --chown=docker:docker web /var/www/html/
# copy code folder to code directory
ADD --chown=docker:docker python /home/docker/code/