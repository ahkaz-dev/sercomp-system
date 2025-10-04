# SerComp

Веб-приложение для автоматизации приёма и обработки заявок на ремонт офисной техники.  
Проект написан на нативном PHP, решающий реальные бизнес-задачи сервисной организации.

---

## 🚀 Возможности
- Регистрация пользователей и личный кабинет  
- Создание и редактирование заявок на ремонт  
- Отслеживание статуса заявок в реальном времени  
- Панель администратора для управления пользователями, услугами и заявками  
- Система отзывов и комментариев  

---

## 🛠 Технологии
- PHP — основная логика приложения  
- MySQL — хранение данных  
- HTML/CSS/JS — клиентская часть  

---

## 📂 Структура проекта
```
sercomp/
├── account/       # Личный кабинет 
├── admin/         # Панель администратора
├── auth/          # Авторизация 
├── db/            # Подключение к базе 
├── include/       # Общие шаблоны 
├── request/       # Управление заявками
├── review/        # Отзывы 
└── static/        # Статические файлы 
```
---

## ⚙️ Установка и запуск
1. Клонировать репозиторий:
     git clone https://github.com/ahkaz-dev/sercomp.git
2. Настроить веб-сервер (Apache/Nginx) и PHP (>=7.4).

3. Создать базу данных MySQL и импортировать запросы (mainsql.txt).

4. В файле db/connect.php указать свои параметры подключения.

5. Перейти по адресу проекта в браузере.

---

## 🔒 Безопасность

- Проверка и фильтрация входных данных

- Защита от XSS и SQL-инъекций

- Разграничение прав доступа

---

## 🖼 Скриншоты

| Главная страница | Услуги | Отзывы |
|-----------------|----------------|----------------|
| ![Главная](https://raw.githubusercontent.com/ahkaz-dev/sercomp-system/main/static/img/screens/index.png) | ![Услуги](https://raw.githubusercontent.com/ahkaz-dev/sercomp-system/main/static/img/screens/our-service.png) | ![Отзывы](https://raw.githubusercontent.com/ahkaz-dev/sercomp-system/main/static/img/screens/review.png) 

| Мои заявки |
|-----------------|
|  ![Мои заявки](https://raw.githubusercontent.com/ahkaz-dev/sercomp-system/main/static/img/screens/my-request.png) |

| Админ панель | Все заявки | Заявка |
|-----------------|----------------|----------------|
| ![Админ панель](https://raw.githubusercontent.com/ahkaz-dev/sercomp-system/main/static/img/screens/admin.png) | ![Все заявки](https://raw.githubusercontent.com/ahkaz-dev/sercomp-system/main/static/img/screens/all-requests.png) | ![Заявка](https://raw.githubusercontent.com/ahkaz-dev/sercomp-system/main/static/img/screens/request.png) 