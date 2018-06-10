### Setup

The fastest way to start the project is to use the docker-compose template. In that case, Docker Compose 1.11.0-rc1+ is required.

#### Web server setup

Edit `config/roles.base.ini` and `config/settings.base.ini` and edit the passwords if necessary:
* The `ducksmanager` and `whattheduck` roles are only authorized to use the services prefixed with `/collection/`
* `rawsql` is only authorized to use the services prefixed with `/rawsql`
* `edgecreator` is only authorized to use the services prefixed with `/edgecreator`

#### Database setup

If you wish to customize the names of the containers, the port bindings or the database credentials, edit `docker-compose.yml` and `.env`.

### Run !

#### Start the project

```bash
docker-compose up --build -d && watch -n 1 'docker-compose ps'
```

Creating the containers should take less than a minute. 

#### Generate the DB config files

Once the containers are started, run the following command to generate the DB config files from `docker-compose.yml` :
```bash
docker-compose run php php app/config/generate-config.php docker-compose.yml .env
```
(supposing that `web` is the name of the running Web container)


### Maintain

#### Updating the code in the container

Browse to the path of the source on the host, then run: 
```bash
./scripts/deploy/deploy-app.sh web
```


### Tasks

#### Reset the demo user

```bash
docker-compose run web /bin/bash scripts/call-service.sh admin /ducksmanager/resetDemo
```
