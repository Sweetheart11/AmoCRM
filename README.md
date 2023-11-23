# Отправка заявок на AmoCRM

## Развертывание

```console
docker-compose up -d --build 

composer install && npm install

npm run dev
```

## Структура

Используется паттерн MVC с использованием микрофреймворка Slim и других библиотек. 

* В файле config/app.php конфигурация CRM клиента
* В папке app основная логика проекта
* /auth - получение токена

FormController отвечает за отправку данных из формы. CRMAuthController за получение токена авторизации. Логика обоих контроллеров вынесена в сервисы AmoCRMAuthService(авторизация и обновление токена) и AmoCRMCurlService(отправка запроса с помощью curl).

## Итог

![Форма](https://github.com/Sweetheart11/AmoCRM/blob/b4fd981acb9a5840841dd8c289ebc7d32c6893b0/Screenshot_20231124_050343.png)

![Сделка](https://github.com/Sweetheart11/AmoCRM/blob/b4fd981acb9a5840841dd8c289ebc7d32c6893b0/Screenshot_20231124_050438.png)

