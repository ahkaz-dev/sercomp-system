-- Создание таблицы Model
CREATE TABLE IF NOT EXISTS `Model` (
  `Id` INT NOT NULL AUTO_INCREMENT,
  `Name` VARCHAR(55) COLLATE utf8mb4_general_ci NOT NULL,
  `Year` YEAR NOT NULL,
  PRIMARY KEY (`Id`)
);

-- Вставка данных в Model без указания Id
INSERT INTO `Model` (`Name`, `Year`) VALUES
('iPhone 13 Pro', 2021),
('Samsung Galaxy S22', 2022),
('MacBook Pro 14', 2021),
('Lenovo ThinkPad X1 Carbon Gen 8', 2020),
('iPad Air (5th gen)', 2022),
('Sony WH-1000XM5', 2022),
('Dell XPS 13 Plus', 2022),
('Google Pixel 6', 2021),
('Canon i-SENSYS MF445dw', 2020),
('HP LaserJet Pro M404dn', 2019),
('Epson EcoTank L3250', 2021),
('Brother DCP-L2530DW', 2018),
('Philips 276E8VJSB', 2021),
('LG 27UL500-W', 2020),
('Samsung Galaxy Tab S7', 2021);

-- Создание таблицы Device
CREATE TABLE IF NOT EXISTS `Device` (
  `Id` INT NOT NULL AUTO_INCREMENT,
  `Name` VARCHAR(55) COLLATE utf8mb4_general_ci NOT NULL,
  `Description` TEXT COLLATE utf8mb4_general_ci NOT NULL,
  `Model_id` INT DEFAULT NULL,
  `Serial_num` VARCHAR(55) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `Model_id` (`Model_id`),
  CONSTRAINT `Device_ibfk_1` FOREIGN KEY (`Model_id`) REFERENCES `Model` (`Id`)
);

-- Вставка данных в Device без указания Id
INSERT INTO `Device` (`Name`, `Description`, `Model_id`, `Serial_num`) VALUES
('Смартфон Apple', 'Смартфон премиум-класса с OLED-экраном', 1, 'APL-IP13P-XR9821'),
('Телефон Samsung', 'Флагманская модель с AMOLED-дисплеем', 2, 'SMSNG-S22-KL7833'),
('Ноутбук MacBook', 'Компактный ноутбук для работы и творчества', 3, 'MAC-14PRO-M1ZX21'),
('ThinkPad X1', 'Бизнес-ноутбук с надежной клавиатурой', 4, 'LNV-TPCG8-2201Z'),
('Планшет Apple', 'Планшет с поддержкой Apple Pencil', 5, 'IPAD-AR5-QW6789'),
('Наушники Sony', 'Флагманская модель с шумоподавлением', 6, 'SONY-WHXM5-ZD5523'),
('Dell XPS', 'Ультрабук с безрамочным дисплеем', 7, 'DELL-XPS13P-VR8820'),
('Смартфон Pixel', 'Смартфон от Google с чистым Android', 8, 'GOOGL-PX6-YY1245'),
('МФУ Canon', 'Многофункциональное устройство для офиса', 9, 'CANON-MF445-ZX4452'),
('Принтер HP', 'Черно-белый лазерный принтер', 10, 'HP-LJ404DN-AB1234'),
('Принтер Epson', 'Цветной струйный принтер с СНПЧ', 11, 'EPS-L3250-CN3421'),
('МФУ Brother', 'Компактное лазерное МФУ', 12, 'BR-DCP2530DW-FG8721'),
('Монитор Philips', '27-дюймовый 4K-монитор', 13, 'PH-276E8-4KXD98'),
('Монитор LG', 'IPS монитор с HDR10', 14, 'LG-27UL500-HDR9021'),
('Планшет Samsung', 'Планшет с S Pen и 120 Гц дисплеем', 15, 'SMSG-TABS7-GR2210');

-- Создание таблицы Service
CREATE TABLE IF NOT EXISTS `Service` (
  `Id` INT NOT NULL AUTO_INCREMENT,
  `Name` VARCHAR(55) COLLATE utf8mb4_general_ci NOT NULL,
  `Price` DECIMAL(10,2) NOT NULL,
  `About` VARCHAR(100) COLLATE utf8mb4_general_ci NOT NULL,
  `Short_desc` VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
  `Full_desc` TEXT COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`Id`)
);

-- Вставка данных в Service без указания Id
INSERT INTO `Service` (`Name`, `Price`, `About`, `Short_desc`, `Full_desc`) VALUES
('Ремонт ноутбуков', 2500, 'Ремонт и настройка', 'Быстрый и качественный ремонт любых ноутбуков.', 'Мы выполняем ремонт ноутбуков любых брендов: ASUS, HP, Lenovo, Dell и других. Диагностика и устранение проблем.'),
('Настройка Wi-Fi', 1200, 'Сетевые услуги', 'Настройка домашнего и офисного Wi-Fi.', 'Настройка роутера, устранение мертвых зон, подключение устройств.'),
('Установка Windows', 1500, 'Переустановка ОС', 'Установка и настройка Windows любой версии.', 'Переустановка Windows 10/11 с сохранением данных. Установка драйверов и ПО.'),
('Чистка системного блока', 1000, 'Профилактика', 'Удаление пыли, замена термопасты.', 'Чистка ПК от пыли и замена термопасты для предотвращения перегрева.'),
('Диагностика ПК', 500, 'Обнаружение неисправностей', 'Быстрая диагностика любых проблем.', 'Полная проверка всех компонентов компьютера.'),
('Замена дисплея', 3000, 'Ремонт экрана', 'Замена разбитого дисплея ноутбука или планшета.', 'Качественная замена экрана на ноутбуках, планшетах и смартфонах.'),
('Настройка ПО', 800, 'Установка программ', 'Установка приложений и антивирусов.', 'Установка офисных пакетов, браузеров, антивирусов и мессенджеров.'),
('Ремонт смартфонов', 2000, 'Ремонт экранов и аккумуляторов', 'Замена экранов, батарей и разъемов зарядки.', 'Ремонт смартфонов iPhone, Samsung, Xiaomi и др.'),
('Ремонт планшетов', 2200, 'Ремонт экранов и зарядных разъемов', 'Замена дисплеев, ремонт разъемов питания.', 'Ремонт планшетов Apple, Samsung и других.'),
('Ремонт МФУ и принтеров', 2800, 'Обслуживание техники', 'Удаление замятий, замена картриджей.', 'Диагностика и ремонт принтеров и МФУ Canon, HP, Epson, Brother.'),
('Ремонт мониторов', 2500, 'Ремонт ЖК и LED дисплеев', 'Восстановление подсветки и пикселей.', 'Ремонт мониторов LG, Samsung, Philips.'),
('Восстановление данных', 6000, 'Реанимация HDD и SSD', 'Восстановление удалённых файлов.', 'Восстановление данных с HDD и SSD после сбоев.'),
('Ремонт роутеров', 1800, 'Сетевое оборудование', 'Настройка и ремонт роутеров.', 'Обновление прошивки, ремонт портов и Wi-Fi.'),
('Ремонт телевизоров', 3500, 'Ремонт ЖК и LED ТВ', 'Замена подсветки и блоков питания.', 'Ремонт телевизоров Samsung, LG, Sony.'),
('Ремонт приставок', 3000, 'Консоли PlayStation, Xbox, Nintendo', 'Ремонт контроллеров, дисководов.', 'Ремонт игровых приставок и аксессуаров.'),
('Ремонт фотоаппаратов', 2800, 'Фототехника', 'Замена объективов и сенсоров.', 'Ремонт камер Canon, Nikon, Sony.'),
('Обслуживание ИБП', 1500, 'Аккумуляторы и диагностика', 'Замена батарей и тестирование.', 'Ремонт ИБП APC, Eaton и других.'),
('Установка ПО', 900, 'Инсталляция программ', 'Установка драйверов и офисных пакетов.', 'Помощь в установке антивирусов и утилит.'),
('Удаление вирусов', 1800, 'Очистка ПК', 'Удаление вредоносного ПО.', 'Очистка автозагрузки, ускорение ПК.'),
('Пользовательская услуга', 0, 'Выбрана пользователем вручную', '', '');

-- Создание таблицы Users
CREATE TABLE IF NOT EXISTS `Users` (
  `Id` INT NOT NULL AUTO_INCREMENT,
  `Login` VARCHAR(25) COLLATE utf8mb4_general_ci NOT NULL,
  `Password` VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
  `Email` VARCHAR(320) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Name` VARCHAR(55) COLLATE utf8mb4_general_ci NOT NULL,
  `Phone_number` VARCHAR(20) COLLATE utf8mb4_general_ci NOT NULL,
  `Status` VARCHAR(25) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`Id`)
);

-- Создание таблицы Request
CREATE TABLE IF NOT EXISTS `Request` (
  `Id` INT NOT NULL AUTO_INCREMENT,
  `Register_date` DATE NOT NULL,
  `What_date` DATE DEFAULT NULL,
  `Desc_problem` VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
  `User_id` INT DEFAULT NULL,
  `Service_id` INT NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `User_id` (`User_id`),
  KEY `Service_id` (`Service_id`),
  CONSTRAINT `Request_ibfk_1` FOREIGN KEY (`User_id`) REFERENCES `Users` (`Id`),
  CONSTRAINT `Request_ibfk_2` FOREIGN KEY (`Service_id`) REFERENCES `Service` (`Id`)
);

-- Пример вставки заявок
-- INSERT INTO `Request` (`Register_date`, `What_date`, `Desc_problem`, `User_id`, `Service_id`) VALUES
-- (CURDATE(), CURDATE(), 'Не запускает ПК', 1, 8),
-- (CURDATE(), DATE_ADD(CURDATE(), INTERVAL 2 DAY), 'Что-то произошло с ПК', 2, 8),
-- (CURDATE(), DATE_ADD(CURDATE(), INTERVAL 3 DAY), 'Не включается монитор', 1, 8),
-- (CURDATE(), NULL, 'Хочу пиццу', 2, 6),
-- (CURDATE(), DATE_ADD(CURDATE(), INTERVAL 5 DAY), 'Пользовательская услуга', 1, 20);

-- Создание таблицы Message
CREATE TABLE IF NOT EXISTS `Message` (
  `Id` INT NOT NULL AUTO_INCREMENT,
  `Text` TEXT NOT NULL,
  `Status` ENUM('Скоро приступим','В процессе','Готово') NOT NULL DEFAULT 'Скоро приступим',
  `Created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `Request_id` INT NOT NULL,
  `Notified` TINYINT(1) DEFAULT 0,
  PRIMARY KEY (`Id`),
  KEY `Request_id` (`Request_id`),
  CONSTRAINT `Message_ibfk_1` FOREIGN KEY (`Request_id`) REFERENCES `Request` (`Id`) ON DELETE CASCADE
);

-- Создание таблицы User_comment
CREATE TABLE IF NOT EXISTS `User_comment` (
  `Id` INT NOT NULL AUTO_INCREMENT,
  `Creater` VARCHAR(55) COLLATE utf8mb4_general_ci NOT NULL,
  `Comment` TEXT COLLATE utf8mb4_general_ci NOT NULL,
  `Date` DATE NOT NULL,
  `Rate` TINYINT NOT NULL,
  `Approved` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`Id`)
);

-- Вставка отзывов
INSERT INTO `User_comment` (`Creater`, `Comment`, `Date`, `Rate`, `Approved`) VALUES
('Захар', 'Обслуживание было хорошим, хотя и не выдающимся. Им удалось отремонтировать мой пылесос, что является самой важной частью. Ремонт был выполнен в разумные сроки, а персонал был вежлив. Однако мне показалось, что общий опыт мог бы быть более гладким и профессиональным. Тем не менее, я доволен тем, что мой пылесос снова работает.', '2025-05-19', '5', 1),
('Алексей', 'Очень доволен ремонтом ноутбука. Мастер быстро диагностировал проблему и качественно всё починил. Цена соответствует качеству, рекомендую.', '2025-09-22', '5', 1),
('Марина', 'Обратилась для настройки Wi-Fi в доме. Специалисты приехали вовремя, сделали всё четко и понятно объяснили, как пользоваться роутером. Спасибо за сервис!', DATE_ADD(CURDATE(), INTERVAL 3 DAY), '5', 1),
('Игорь', 'Ремонт смартфона прошёл хорошо, но пришлось ждать пару дней дольше, чем обещали. В целом результат устроил, экран заменили качественно.', DATE_ADD(CURDATE(), INTERVAL 7 DAY), '4', 1),
('Елена', 'Быстро и без проблем установили Windows на мой ПК. Все нужные программы тоже помогли поставить. Очень удобно и профессионально.', DATE_ADD(CURDATE(), INTERVAL 13 DAY), '5', 1);