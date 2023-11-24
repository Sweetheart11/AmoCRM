# Отправка заявок на AmoCRM

## Развертывание

```console
docker-compose up -d --build 

composer install && npm install

npm run dev
```

## Структура

Используется паттерн MVC с использованием микрофреймворка Slim и других библеотек. 

* В файле config/app.php конфигурация CRM клиента
* В папке app основная логика проекта
* /auth - получение токена

FormController отвечает за отправку данных из формы. CRMAuthController за получение токена авторизации. Логика обоих контроллеров вынесена в сервисы AmoCRMAuthService(авторизация и обновление токена) и AmoCRMCurlService(отправка запроса с помощью curl).

## Итог



