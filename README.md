# Kafka + ksqlDB + Keycloak POC

## 🚀 Overview

This project is a Proof of Concept (POC) demonstrating the integration of Kafka, ksqlDB, and Keycloak using PHP. It showcases how to build an event-driven architecture where messages are produced, transformed, and consumed securely via OAuth2 authentication.

## 📌 Features

- **Kafka Producer & Consumer** (PHP-based)
- **Kafka Streams** (via ksqlDB & PHP transformation logic)
- **Real-time data processing** using ksqlDB
- **OAuth2 authentication** with Keycloak
- **Dockerized environment** for easy setup

## 🏗 Architecture

### 1. Kafka Event Processing Pipeline

- **php-producer**: Publishes messages to Kafka topics.
- **php-consumer**: Reads & processes messages.
- **php-transformer**: Applies data transformation logic.
- **ksqlDB**: Performs SQL-based stream processing in Kafka.

### 2. Authentication & Security

- Users authenticate via **Keycloak OAuth2**.
- PHP app integrates OAuth2 using **league/oauth2-client**.
- Role-based access can be managed within Keycloak.

## 🛠 Tech Stack

| Technology    | Purpose                             |
|--------------|-------------------------------------|
| Apache Kafka | Message streaming                  |
| ksqlDB       | SQL-based stream processing       |
| PHP          | Application logic (producer/consumer) |
| Keycloak     | Authentication (OAuth2)           |
| Docker       | Containerized setup               |
| Nginx        | Reverse proxy                     |

## 📂 Project Structure

```
├── nginx/
│   ├── default.conf
├── php/
│   ├── Dockerfile
├── vendor/ (Composer dependencies)
├── composer.json
├── docker-compose.yml
├── consumer.php
├── producer.php
├── producer-poller.php
├── transformer.php
├── login.php
├── callback.php
```

## 🚀 Getting Started

### 1️⃣ Clone the Repository

```sh
git clone https://github.com/GarethBoomayn/Kafka-KsqlDB-Keycloak-POC.git
cd Kafka-KsqlDB-Keycloak-POC
```

### 2️⃣ Start the Docker Environment

```sh
docker-compose up -d
```

### 3️⃣ Verify Services

Check if all containers are running:

```sh
docker ps
```

### 4️⃣ Test Kafka Producer & Consumer

Open a PHP producer and send messages:

```sh
docker exec -it php-producer php producer.php
```

Run a Kafka consumer to process messages:

```sh
docker exec -it php-consumer php consumer.php
```

### 5️⃣ Test ksqlDB Queries

Open ksqlDB CLI:

```sh
docker exec -it ksqldb-cli ksql http://ksqldb-server:8088
```

Show existing topics:

```sql
SHOW STREAMS;
```

Create a new transformed stream:

```sql
CREATE STREAM transformed_stream AS SELECT * FROM favoris_stream WHERE id > 50;
```

### 6️⃣ Test Keycloak Authentication

#### Access Keycloak Admin Panel:

- Open: [http://localhost:8081](http://localhost:8081)
- Username: **admin**
- Password: **admin**

#### Login via PHP App:

- Open: [http://localhost:8000/login.php](http://localhost:8000/login.php)
- Authenticate via Keycloak.
- Redirects to: [http://localhost:8000/callback.php](http://localhost:8000/callback.php)

## 🔥 Use Cases

- **Event-driven microservices** (real-time data processing with Kafka)
- **Stream processing without Java** (using ksqlDB + PHP transformations)
- **Secure authentication with OAuth2** (centralized user management via Keycloak)

## 🛠 Future Improvements

- Add **Role-Based Access Control (RBAC)** in Keycloak.
- Improve **error handling** in PHP Kafka consumers.
- Deploy the architecture to a **cloud provider** (AWS, GCP, Azure).

💡 **Contributions & feedback are welcome!**
