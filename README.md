### Instructions

Clone the repo, `cd` into it.

If you intend to put the databases in the same container as the Web server run : 

``docker run -d --restart=always -v `pwd`/app/config:/var/www/html/dm-server/app/config -p 8001:80 --name dm-server-box dm-server``

If the databases are located in different containers, link them in the command :
``docker run -d --restart=always -v `pwd`/app/config:/var/www/html/dm-server/app/config -p 8001:80 --name dm-server-box --link coa-db:coa-db --link dm-db:dm-db --link coverinfo-db:coverinfo-db mariadb-server-box:coa-db dm-server``

The run the following command to fix the Apache logfile structure :
``docker exec -it dm-server-box /bin/bash -c "cd /var/log/apache2 && for logfile in /var/log/apache2/*.log; do rm $logfile && touch $logfile; done && /etc/init.d/apache2 reload"``