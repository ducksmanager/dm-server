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


### Maintain

#### Updating the code in the container

Browse to the path of the source on the host, then run: 
```bash
./scripts/deploy/deploy-app.sh web
```

### Related projects

* [DucksManager](https://github.com/bperel/DucksManager) is a free and open-source website enabling comic book collectors to manage their Disney collection.
* [WhatTheDuck](https://github.com/bperel/WhatTheDuck) is the mobile app of DucksManager, allowing users to check the contents of their collection on a mobile and add issues to the collection by photographing comic book covers.
* [EdgeCreator](https://github.com/bperel/EdgeCreator) is a project allowing users to upload photos of edges and create models out of them in order to generate edge pictures.
* [Duck cover ID](https://github.com/bperel/duck-cover-id) is a collection of shell scripts launched by a daily cronjob, allowing to retrieve comic book covers from the Inducks website and add the features of these pictures to a Pastec index. This index is searched whn taking a picture of a cover in the WhatTheDuck app.
* [COA updater](https://github.com/bperel/coa-updater) is a shell script launched by a daily cronjob, allowing to retrieve the structure and the contents of the Inducks database and to create a copy of this database locally.
* [DucksManager-stats](https://github.com/bperel/DucksManager-stats) contains a list of scripts launched by a daily cronjob, allowing to calculate statistics about issues that are recommended to users on DucksManager, depending on the authors that they prefer.

![DucksManager architecture](https://raw.githubusercontent.com/bperel/DucksManager/master/server_architecture.png)


