### Setup

The fastest way to start the project is to use the docker-compose template. In that case, Docker Compose 1.11.0-rc1+ is required.

#### Web server setup

Copy `config/roles.base.ini` and rename the copy to `config/roles.ini`. Edit `config/roles.ini` to set the application role passwords:
* The `ducksmanager` and `whattheduck` roles are only authorized to use the services prefixed with `/collection/`
* `rawsql` is only authorized to use the services prefixed with `/rawsql`

#### Database setup
Copy `config/config.db.base.ini` and rename the copy to `config/config.db.ini`.

In order to customize the names of the containers, the port bindings or the database credentials, edit `docker-compose.yml` and `config/config.db.ini`. 
The `container_name` values in `docker-compose.yml` and the `host` values in `config/config.db.ini` must match.

### Run !

#### Start the project

```bash
docker-compose up --build -d && watch -n 1 'docker ps | grep " second"'
```

Creating the containers should take less than a minute. 

#### Create database schemas

Once the containers are started, create the schemas in the databases using the following command:
```bash
docker exec -it web /bin/bash -c /var/www/html/dm-server/scripts/create-schemas.sh
```
(considering `web` is the name of the running Web container)


### Maintain

#### Updating the code in the container

Browse to the path of the source on the host, then run: 
```bash
scripts/deploy-server.sh dm-server-box
```