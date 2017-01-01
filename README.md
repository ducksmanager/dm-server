### Instructions

Clone the repo, `cd` into it.

Build the image :
``docker build -t dm-server .``

Run a container :
``docker run -d --restart=always -v `pwd`/app/config:/var/www/html/dm-server/app/config -p 8001:80 --name dm-server-box --net dm_network dm-server``

Then run the following command to fix the Apache logfile structure :
``docker exec -it dm-server-box /bin/bash -c 'cd /var/log/apache2 && for logfile in /var/log/apache2/*.log; do rm $logfile && touch $logfile; done && /etc/init.d/apache2 reload'``