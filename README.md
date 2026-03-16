# CarRent Backend

Бэкенд для сервиса аренды автомобилей.

Инструкция по запуску в Readme.md проекта develop

## API

**Авторизация:** POST /auth/register, POST /auth/login. С токеном: GET /auth/get-user, POST /auth/refresh, POST /auth/logout.

**Автомобили:** GET/POST /cars, GET/PUT/DELETE /cars/{id}. PATCH для смены статуса, госномера, класса. GET /cars-available — свободные машины. PATCH /cars/{id}/renter — сменить арендатора.

**Клиенты:** GET/POST /clients, GET/PUT/DELETE /clients/{id}. POST /clients/{id}/license_scan — загрузка скана водительского удостоверения.

**Заявки:** GET /rental-requests, POST /rental-requests, GET/POST/DELETE по id. approve, reject, complete, send-agreement — отдельные POST на /rental-requests/{id}/...

**История аренды:** GET /rent_histories, GET/DELETE /rent_histories/{id}.

**Отчёты (доступно только роли admin):** POST /reports, GET /reports, GET /reports/{id}/status, GET /reports/{id}/download.

Доступы для ролей:
- admin имеет доступ ко всем методам,
- manager — ко всем, кроме отчётов,
- user — свои заявки, клиентский профиль, доступные для аренды автомобили


---

## Примеры

Регистрация:
```bash
curl -X POST http://localhost/api/auth/register -H "Content-Type: application/json" -d '{"name":"Test","email":"test@test.com","password":"password","role":"user"}'
```

Логин:
```bash
curl -X POST http://localhost/api/auth/login -H "Content-Type: application/json" -d '{"email":"test@test.com","password":"password"}'
```
