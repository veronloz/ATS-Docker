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
|   ├── Dockerfile
│   ├── index.php
│   ├── init.sql
│   └── nginx.conf
├── resources/
|   ├── img/
|   |    └── docker_architecture.png
│   └── docs/
|        └── ArcTecSw_2025_DevOps_Practica.pdf
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

## 📝 P2 - Run

## 👽 Creators  
- [Verónica Lozada Pérez](https://github.com/veronloz)
- [Xinyu Yu](https://github.com/itsYu04)