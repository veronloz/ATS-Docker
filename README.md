# ATS-Docker
## 🧩​ Table of Contents
- [ATS-Docker](#ats-docker)
  - [🧩​ Table of Contents](#-table-of-contents)
  - [🌟 Introduction](#-introduction)
  - [​👀​ Project structure](#-project-structure)
  - [🚀​ Requirements](#-requirements)
  - [📝 P1 - Run](#-p1---run)
    - [1. Mount postgreSQL docker](#1-mount-postgresql-docker)
    - [2. Mount PGAdmin docker](#2-mount-pgadmin-docker)
    - [3. Mount the WebServers \[1-5\]](#3-mount-the-webservers-1-5)
    - [4. Mount nginx](#4-mount-nginx)
  - [📝 P2 - Run](#-p2---run)
    - [compose.yaml](#composeyaml)
  - [👽 Creators](#-creators)

## 🌟 Introduction
![Docker Architecture](/resources/img/docker_architecture.png)
**P1**



**P2**

Mounting the same microservice application using Docker compose file.
## ​👀​ Project structure
```shell
ATS-Docker/
├── P1/
│   ├── Dockerfile
│   ├── index.php
│   ├── init.sql
│   ├── nginx.conf
│   └── output.txt
├── resources/
│   ├── img/
│   |    └── docker_architecture.png
│   └── docs/
│        └── ArcTecSw_2025_DevOps_Practica.pdf
├── P2/
└── README.md
```

## 🚀​ Requirements
You need to have docker installed.
- [Docker desktop](https://www.docker.com/products/docker-desktop/)

## 📝 P1 - Run
### 1. Mount postgreSQL docker
**Pull images from docker hub**
```shell
docker pull postgres
```
**Create volume for pgSQL**
```shell
docker volume create pgdata
```

> [!IMPORTANT]
> You have to create a network to connect the Database container and the DatabaseManager container, you can do it running the following command.
>
> ``` docker network create ManagementNet```

**Docker run command**
```shell
docker run --name Database --hostname Database -p 15432:5432 -e POSTGRES_USER=useradmin -e POSTGRES_PASSWORD=secure1234 -e POSTGRES_DB=AppDB -v pgdata:/var/lib/postgresql/data -v "$(pwd)/init.sql:/docker-entrypoint-initdb.d/init.sql" --net ManagementNet -d postgres
```
Remember to replace the `<user>` and the `<password>` to real values.


### 2. Mount PGAdmin docker
**Pull images from docker hub**
```shell
docker pull dpage/pgadmin4
```
**Create volume for pgAdmin**
```shell
docker volume create pgadmin
```
**Docker run command**
```shell
docker run --name DataBaseManager --hostname DataBaseManager -p 20001:8000 -e PGADMIN_DEFAULT_EMAIL=admin@admin.com -e PGADMIN_DEFAULT_PASSWORD=secretsecret -e PGADMIN_LISTEN_PORT=8000 -v pgadmin:/var/lib/pgadmin --net ManagementNet -d dpage/pgadmin4
```
Remember to replace the `<email>` and the `<password>` to real values.


### 3. Mount the WebServers [1-5]

> [!IMPORTANT]
> You have to create a network called FrontendNet to connect nginx and the WebServers, you can do it executing the following line:
> 
> ``` docker network create FrontendNet ```

In the same directory as your  [Dockerfile](/P1/Dockerfile) execute this command line:
```
docker build -t webservers . 
```
This command will build up an Image from your Dockerfile.

Now you can build up your WebServer Containers. You have to execute the same command 5 times, one for each WebServer, just remember to change the number (`Webserver<number>`).
```
docker run --name WebServer1 --hostname Webserver1 --network FrontendNet -d webservers
```
> [!IMPORTANT]
> You have to create a network called BackendNet also to connect the Database and the WebServers, you can do it executing the following line:
> 
> ``` docker network create BackendNet ```
>
> We did this step once we encountered an error, but you can actually do this before, once you create the PostgreSQL container. But it won't be late to do it now!
> You can add each WebServer and the Database to this network by the following command. Remember to repeat the same command line for each WebServer[1-5]
> 
> ``` docker network connect BackendNet WebServer1 ```
> 
> ``` docker network connect BackendNet Database ```

### 4. Mount nginx
With the nginx.conf file in the actual directory you can execute this command to mount the docker
```shell
docker run --name LoadBalancer --hostname LoadBalancer --network FrontendNet -p 20000:8000 -v "$(pwd)/nginx.conf:/etc/nginx/nginx.conf" -d nginx
```

## 📝 P2 - Run
Explanation of the Docker compose file.
### compose.yaml
```yaml
services:
  db:
    container_name: Database
    hostname: Database
    image: postgres:latest
    ports: 
      - "15432:5432"
    environment:
      POSTGRES_USER: "useradmin"
      POSTGRES_PASSWORD: "secure1234"
      POSTGRES_DB: AppDB
    volumes:
      - "pgdata:/var/lib/postgresql/data"
      - "./init.sql:/docker-entrypoint-initdb.d/init.sql"
    networks:
      - ManagementNet
      - BackendNet
```
This creates the `postgreSQL` db container, defining the ports, user, volumes and networks in the process. It also defines a hostname to be later used in the internal networking of the rest of the containers. The image used is the latest.

The mounting of the pgdata volume allows data to persist and the `init.sql` files pre-populates the db.

The `ManagementNet` network is the one used to connect the Database container and the DatabaseManager container. 

The `BackendNet` network is used to to connect the Database and the WebServers.

```yaml
  db_admin:
    container_name: DatabaseManager
    hostname: DatabaseManager
    image: dpage/pgadmin4
    ports:
     - "20001:8000"
    environment:
      PGADMIN_DEFAULT_EMAIL: admin@admin.com
      PGADMIN_DEFAULT_PASSWORD: secretsecret
      PGADMIN_LISTEN_PORT: 8000
    volumes:
      - "pgadmin:/var/lib/pgadmin"
    networks:
      - ManagementNet
    depends_on:
      - db
```

This part creates the `pgadmin` container. The configuration is similar to the previous one. Since this is the DatabaseManager, it uses of the `ManagementNet` network to communicate with the Database container.

The use of the `depends_on` option ensures that the `db` container is started before the `db_admin` container.

```yaml
  webserver1: &webserver1
    container_name: WebServer1
    hostname: WebServer1
    build: .
    networks:
      - FrontendNet
      - BackendNet
    depends_on:
      - db_admin
  webserver2:
    <<: *webserver1
    container_name: WebServer2
    hostname: WebServer2
  webserver3:
    <<: *webserver1
    container_name: WebServer3
    hostname: WebServer3
  webserver4:
    <<: *webserver1
    container_name: WebServer4
    hostname: WebServer4
  webserver5:
    <<: *webserver1
    container_name: WebServer5
    hostname: WebServer5
```

This part defines the creation of the `WebServers`. At first, `webserver1` is created. The build specifies that the container should be built from the current directory. It stablish the use of the `FrontendNet` and the `BackendNet` that will allow  the connection to ngix and the database respectively. The `depens_on` option is one again use to ensure that the `db_admin` service is running.

Creating an anchor (`&webserver1`) and then using `<<: * webserver1`, allows to copy the configuration settings of `webserver1` into the rest of the webservers.

```yaml
  nginx:
    image: nginx:latest
    container_name: LoadBalancer
    hostname: LoadBalancer
    ports:
      - "20000:8000"
    volumes:
      - "./nginx.conf:/etc/nginx/nginx.conf"
    networks:
      - FrontendNet
    depends_on:
      - webserver1
      - webserver2
      - webserver3
      - webserver4
      - webserver5
```
In here, `ngix` is being mounted. It's defined with the hostname `LoadBalancer`. The volume used the local file `ngix.conf` and the `FrontendNet` network to communicate with the webservers.
Once more, the `depends_on` option ensures that the web servers are started before the Nginx container.

```yaml
networks:
  ManagementNet:
    driver: bridge
  BackendNet:
    driver: bridge
  FrontendNet:
    driver: bridge
volumes:
  pgdata:
    external: true
  pgadmin:
    external: true
```
Lastly, this final portion of the file defines the networks and volumes referenced throughout the compose file.

The `driver: bridge` in the networks specifies that the networks use the bridge driver, which allows containers connected to this network to communicate with each other.

The `external: true` in the volumes indicates that this volume is managed externally and must already exist before running the `docker-compose` file.

Alternatively, you could do:
```yaml
volumes:
  pgdata:
    driver: local
  pgadmin:
    driver: local 
```

This way the volumes will use Docker's default local driver to store data on the host machine. They will be created automatically when you run the `docker-compose`.

Finally, to initialize and deploy all defined services in the `docker-compose` file, you can execute the following command:
``` bash
docker compose up -d
```

By running it in the detached mode (`-d`) it is guaranteed that the services will be started respecting the dependencies and the network configurations.

## 👽 Creators  
- [Verónica Lozada Pérez](https://github.com/veronloz)
- [Xinyu Yu](https://github.com/itsYu04)