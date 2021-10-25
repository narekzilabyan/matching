# Matching
### Step 1: Use docker for local deployment
!!! If you don't have docker and docker-compose, you can install it from the official site https://docs.docker.com/get-docker/

- Local deployment (Services: {php, nginx}):
#### Run project
```bash
    docker-compose up -d --build
```

### Step 2: Run composer for install project packages
```bash
    docker-compose exec matching-php composer install
```

!!! Checking the availability of the application (IP-localhost, NGINX - by default 9101)
http://localhost:9101/