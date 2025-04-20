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
**Docker run command**
```shell
docker run --name Database \
--hostname Database -p 15432:5432 \
-e POSTGRES_USER=<user> \
-e POSTGRES_PASSWORD=<password> \
-e POSTGRES_DB=AppDB \
-v pgdata:/var/lib/postgresql/data \
-v "$(pwd)/init.sql:/docker-entrypoint-initdb.d/init.sql" \
-d postgres
```
Remember to replace the `<user>`and the `<password>` to to real values.

> [!IMPORTANT]
> You have to create a network to connect the Database container and the DatabaseManager container, you can do it running the following command.
>
> ``` docker network create ManagementNet```
>
> We did it late, so we manually added both containers to the network by executing this command line:
>
> ``` network connect ManagementNet Database ```
>
> ``` network connect ManagementNet DataBaseManager ```
>
> But you can actually connect both of them when you run your docker run command adding this tag: ``` --network ManagementNet ```

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
docker run --name DataBaseManager \
--hostname DataBaseManager \
-p 20001:8000 \
-e PGADMIN_DEFAULT_EMAIL=<email> \
-e PGADMIN_DEFAULT_PASSWORD=<password> \
-e PGADMIN_LISTEN_PORT=8000 \
-v pgadmin:/var/lib/pgadmin \
-d dpage/pgadmin4
```
Remember to replace the `<email>` and the `<password>` to real values.
### 3. Mount the WebServers [1-5]

> [!IMPORTANT]
> You have to create a network called FrontendNet to connect nginx and the WebServers, you can do it executing the following line:
> 
> ``` docker network create FrontendNet ```

In the same directory as your  [Dockerfile](/P1/Dockerfile) execute this command line:
```
docker build -t webServers . 
```
This command will build up an Image from your Dockerfile.

Now you can build up your WebServer Containers. You have to execute the same command 5 times, one for each WebServer, just remember to change the number (`Webserver<number>`).
```
docker run --name WebServer1 --hostname Webserver1 --network FrontendNet -d webservers
```
> [!IMPORTANT]
> You have to create a network called BackendNet also to connect Database and the WebServers, you can do it executing the following line:
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
 docker run --name LoadBalancer \
 --hostname LoadBalancer \
 --network FrontendNet \
 -p 20000:8000 \
 -v "$(pwd)/nginx.conf:/etc/nginx/nginx.conf:ro" \
 -d nginx
```

## 📝 P2 - Run

## 👽 Creators  
- [Verónica Lozada Pérez](https://github.com/veronloz)
- [Xinyu Yu](https://github.com/itsYu04)