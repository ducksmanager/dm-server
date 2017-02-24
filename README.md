### Setup

Clone the repo, `cd` into it.

Build the image :
``docker build -t dm-server .``

Build the network that will also contain the DBs :
``docker network create -d bridge --subnet 172.25.0.0/16 dm_network``

Run a container :
``docker run -d --restart=always -v `pwd`/app/config:/var/www/html/dm-server/app/config  -p 9000:9000 -p 8001:80 --name dm-server-box --net dm_network dm-server``

Then run the following command to fix the Apache logfile structure :
``docker exec -it dm-server-box /bin/bash -c 'cd /var/log/apache2 && for logfile in /var/log/apache2/*.log; do rm $logfile && touch $logfile; done && /etc/init.d/apache2 reload'``


### Configuration

Copy `config/config.db.base.ini` into `config/config.db.ini` and change the DB settings.


### Updating the code in the container

Browse to the path of the source on the host, then run: 
```bash
scripts/deploy-app.sh dm-server-box
```


### Tasks

#### Reset the demo user

```bash
docker exec -i dm-server-box /bin/bash dm-server/scripts/call-service.sh admin /user/resetDemo
```