# Cabletorg CMS

Учебный проект CMS для магазина кабельной продукции.

## Стек
- PHP
- MySQL
- HTML/CSS/JS

---

## Локальный запуск через XAMPP

1. Скопировать папку проекта в `C:\xampp\htdocs\` (или путь к `htdocs` на вашей системе).  
2. Запустить XAMPP: включить Apache и MySQL.  
3. Открыть phpMyAdmin (`http://localhost/phpmyadmin`) и создать базу данных, например `cabletorg`.  
4. Импортировать файл `sql/cabletorg.sql`.  
5. Настроить `config.php`:
   ```php
   $host = "localhost";
   $user = "root"; // стандартный XAMPP
   $pass = "";     // стандартный XAMPP
   $db   = "cabletorg"; // имя базы, созданной на шаге 3
Открыть в браузере http://localhost/cabletorg-cms/.

Сайт должен загрузиться и работать.
