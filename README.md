# Setup

yii2-basic
```
composer create-project --prefer-dist yiisoft/yii2-app-basic task09122022
```

clone repository files

```
cd task09122022

docker-compose -f docker/docker-compose.yml up -d
```

In composer php-container

```
yii migrate

yii rbac/init
```

p.s - в RbacController делаю иницализацию пользователей
<br>
p.s2 - Если нужен phpmyadmin server = db;
