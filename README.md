# Task Manager
Just task managing app.  

Based on symfony docker template:  
https://github.com/dunglas/symfony-docker

## Setup
Build react files:
```
npm install --force
npm run watch 
```

Initialize project:
```
docker compose up
docker exec -it task-manager_php_1 bin/console app:create-user test@email.com password
```


