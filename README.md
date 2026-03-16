# CarRent Backend

Бэкенд для сервиса аренды автомобилей.

Инструкция по запуску в Readme.md проекта develop

## API

Все методы кроме register и login требуют заголовок `Authorization: Bearer <token>`.

---

### Авторизация (без токена)

| Метод | URL | Описание |
|-------|-----|----------|
| POST | /auth/register | Регистрация |
| POST | /auth/login | Вход |

**POST /auth/register** — тело:
```json
{
  "name": "Иван",
  "email": "user@example.com",
  "password": "password",
  "role": "admin|manager|user"
}
```
Ответ 201:
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "token_type": "bearer",
  "user": {"id": 1, "name": "Иван", "email": "user@example.com", "role": "user"}
}
```

**POST /auth/login** — тело:
```json
{
  "email": "user@example.com",
  "password": "password"
}
```
Ответ 200:
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "token_type": "bearer",
  "user": {"id": 1, "name": "Иван", "email": "user@example.com", "role": "user"}
}
```

---

### Авторизация (с токеном)

| Метод | URL | Описание |
|-------|-----|----------|
| GET | /auth/get-user | Текущий пользователь |
| POST | /auth/refresh | Обновить токен |
| POST | /auth/logout | Выход |

**GET /auth/get-user** — тело не требуется. Ответ 200: `{"user":{...}}`

**POST /auth/refresh** — тело не требуется. Ответ 200: `{"token":"...","token_type":"bearer"}`

**POST /auth/logout** — тело не требуется. Ответ 200: `{"message":"Вы вышли из системы"}`

---

### Автомобили

**Роли:** admin, manager — полный доступ; user — только cars-available и changeRenter.

| Метод | URL | Описание |
|-------|-----|----------|
| GET | /cars | Список всех автомобилей |
| POST | /cars | Создать автомобиль |
| GET | /cars/{id} | Один автомобиль |
| PUT | /cars/{id} | Обновить автомобиль |
| DELETE | /cars/{id} | Удалить автомобиль |
| PATCH | /cars/{id}/status | Изменить статус |
| PATCH | /cars/{id}/license_plate | Изменить госномер |
| PATCH | /cars/{id}/car_class | Изменить класс и тариф |
| GET | /cars-available | Доступные для аренды |
| PATCH | /cars/{id}/renter | Сменить арендатора |

**GET /cars** — тело не требуется. Ответ 200:
```json
{
  "message": "Список автомобилей",
  "data": [
    {"id": 1, "make": "Toyota", "model": "Camry", "year": 2022, "vin": "ABC12345678901234", "license_plate": "A123BC77", "car_class": "comfort", "status": "available", "current_renter_id": null}
  ]
}
```

**POST /cars** — тело:
```json
{
  "make": "Toyota",
  "model": "Camry",
  "year": 2022,
  "vin": "ABC12345678901234",
  "license_plate": "A123BC77",
  "car_class": "comfort",
  "power": 200,
  "hourly_rate": 1000
}
```
car_class: economy|comfort|business. VIN — 17 заглавных латинских букв и цифр. Ответ 201: `{"message":"Автомобиль создан","data":{...}}`

**GET /cars/{id}** — тело не требуется. Ответ 200: `{"data":{...}}`

**PUT /cars/{id}** — тело:
```json
{
  "status": "available|rented|maintenance",
  "current_renter_id": null
}
```
Ответ 201: `{"message":"Автомобиль успешно обновлен","data":{...}}`

**DELETE /cars/{id}** — тело не требуется. Ответ 204.

**PATCH /cars/{id}/status** — тело:
```json
{
  "status": "available|rented|maintenance"
}
```
Ответ 201: `{"message":"Статус автомобиля обновлен","data":{...}}`

**PATCH /cars/{id}/license_plate** — тело:
```json
{
  "license_plate": "A123BC77"
}
```
Госномер уникальный. Ответ 201: `{"message":"Номер автомобиля обновлен","data":{...}}`

**PATCH /cars/{id}/car_class** — тело:
```json
{
  "car_class": "economy|comfort|business"
}
```
Ответ 201: `{"message":"Класс автомобиля обновлен","data":{...}}`

**GET /cars-available** — тело не требуется. Ответ 200: `{"message":"Доступные автомобили","data":[...]}`

**PATCH /cars/{id}/renter** — тело:
```json
{
  "current_renter_id": 1
}
```
или `{"current_renter_id": null}`. Ответ 201: `{"message":"Арендатор автомобиля обновлен","data":{...}}`

---

### Клиенты

**Роли:** admin, manager — список, удаление; admin, manager, user — создание, просмотр, обновление, загрузка прав.

| Метод | URL | Описание |
|-------|-----|----------|
| GET | /clients | Список клиентов |
| POST | /clients | Создать профиль клиента |
| GET | /clients/{id} | Один клиент |
| PUT | /clients/{id} | Обновить |
| DELETE | /clients/{id} | Удалить |
| POST | /clients/{id}/license_scan | Загрузить фото прав |

**GET /clients** — тело не требуется. Ответ 200: `{"data":[...]}`

**POST /clients** — тело:
```json
{
  "age": 25,
  "phone": "79991234567",
  "driving_experience": 5
}
```
name, email берутся из текущего пользователя. license_scan — опционально, multipart (pdf, jpeg, png, до 10 МБ). Телефон уникальный. Ответ 201: `{"message":"Клиент добавлен","data":{...}}`

**GET /clients/{id}** — тело не требуется. Ответ 200: `{"data":{...}}`

**PUT /clients/{id}** — тело:
```json
{
  "phone": "79991234567",
  "email": "new@example.com",
  "driving_experience": 5
}
```
Ответ 200: `{"message":"Данные клиента обновлены"}`

**DELETE /clients/{id}** — тело не требуется. Ответ 200: `{"message":"Клиент удалён"}`

**POST /clients/{id}/license_scan** — multipart, поле license_scan, файл (pdf, jpeg, png, до 10 МБ). Ответ 200: `{"message":"Водительское удостоверение обновлено"}`

---

### Заявки на аренду

**Роли:** admin, manager — список, просмотр, approve, reject, complete, send-agreement, delete; admin, manager, user — создание.

| Метод | URL | Описание |
|-------|-----|----------|
| GET | /rental-requests | Список заявок |
| POST | /rental-requests | Создать заявку |
| GET | /rental-requests/{id} | Одна заявка |
| POST | /rental-requests/{id}/approve | Одобрить |
| POST | /rental-requests/{id}/reject | Отклонить |
| POST | /rental-requests/{id}/complete | Завершить |
| POST | /rental-requests/{id}/send-agreement | Отправить договор |
| DELETE | /rental-requests/{id} | Удалить |

**GET /rental-requests** — тело не требуется. Ответ 200: `{"data":[...]}`

**Автомобили:** GET/POST /cars, GET/PUT/DELETE /cars/{id}. PATCH для смены статуса, госномера, класса. GET /cars-available — свободные машины. PATCH /cars/{id}/renter — сменить арендатора.

**Клиенты:** GET/POST /clients, GET/PUT/DELETE /clients/{id}. POST /clients/{id}/license_scan — загрузка скана водительского удостоверения.

**Заявки:** GET /rental-requests, POST /rental-requests, GET/POST/DELETE по id. approve, reject, complete, send-agreement — отдельные POST на /rental-requests/{id}/...

**История аренды:** GET /rent_histories, GET/DELETE /rent_histories/{id}.

**Отчёты (доступно только роли admin):** POST /reports, GET /reports, GET /reports/{id}/status, GET /reports/{id}/download.

Доступы для ролей:
** admin имеет доступ ко всем методам,
** manager — ко всем, кроме отчётов,
** user — свои заявки, клиентский профиль, доступные для аренды автомобили


---

### История аренды

**Роли:** admin, manager.

| Метод | URL | Описание |
|-------|-----|----------|
| GET | /rent_histories | Список |
| GET | /rent_histories/{id} | Одна запись |
| DELETE | /rent_histories/{id} | Удалить |

**GET /rent_histories** — тело не требуется. Ответ 200: `{"data":[...]}`

**GET /rent_histories/{id}** — тело не требуется. Ответ 200: `{"data":{...}}`

**DELETE /rent_histories/{id}** — тело не требуется. Ответ 204.

---

### Отчёты

**Роль:** только admin.

| Метод | URL | Описание |
|-------|-----|----------|
| POST | /reports | Создать отчёт |
| GET | /reports | Готовые отчёты |
| GET | /reports/{id}/status | Статус отчёта |
| GET | /reports/{id}/download | Скачать файл |

**POST /reports** — тело:
```json
{
  "type": "rent_histories|rental_requests",
  "date_from": "2024-01-01",
  "date_to": "2024-12-31"
}
```
Ответ 202:
```json
{
  "id": 1,
  "status": "pending"
}
```
Отчёт формируется асинхронно.

**GET /reports** — тело не требуется. Ответ 200: `{"data":[...]}`

**GET /reports/{id}/status** — тело не требуется. Ответ 200:
```json
{
  "status": "pending|processing|finished|failed"
}
```

**GET /reports/{id}/download** — тело не требуется. Скачивание файла. Доступно только при status=finished.

---

## Примеры curl

Регистрация:
```bash
curl -X POST http://localhost/api/auth/register -H "Content-Type: application/json" -d '{"name":"Test","email":"test@test.com","password":"password","role":"user"}'
```

Логин:
```bash
curl -X POST http://localhost/api/auth/login -H "Content-Type: application/json" -d '{"email":"test@test.com","password":"password"}'
```

Создание автомобиля:
```bash
curl -X POST http://localhost/api/cars -H "Authorization: Bearer YOUR_TOKEN" -H "Content-Type: application/json" -d '{"make":"Toyota","model":"Camry","year":2022,"vin":"ABC12345678901234","license_plate":"A123BC77","car_class":"comfort","power":200,"hourly_rate":1000}'
```

Коды ответов: 200 OK, 201 создано, 202 принято, 204 без тела, 401 не авторизован, 403 нет прав, 404 не найдено, 422 ошибка валидации.
