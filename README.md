# Калькулятор стоимости пластиковых окон и балконов

### Возможности
- При изменении типа створок изображения окна/балкона изменяются 
- Клиент может создать и отправить сразу нескольких заказов в одной заявке
- Дизайн подстраивается под мобильные устройства
- Есть маска для ввода телефона без ошибок
- Админка калькулятора позволяет владельцу бизнеса изменять цены на работу и материалы без обращения к программистам

### Используемые технологии
Frontend:
- Vue.js (vue-resource)
- jQuery (Inputmask, AJAX)
- Bulma CSS

Backend:
- PHPMailer

### Описание файлов
[index.html](https://github.com/Ivankaz/kalkuljator-okon/blob/main/index.html) - страница калькулятора\
[assets/js/main.js](https://github.com/Ivankaz/kalkuljator-okon/blob/main/assets/js/main.js) - основной скрипт (загрузка данных, расчет цен)\
[assets/php/send.php](https://github.com/Ivankaz/kalkuljator-okon/blob/main/assets/php/send.php) - отправка заявки на почту владельцу сайта\
[assets/settings.json](https://github.com/Ivankaz/kalkuljator-okon/blob/main/assets/settings.json) - настройки калькулятора (цены, материалы, почта для заявок и т.д.)\
[assets/css/style.css](https://github.com/Ivankaz/kalkuljator-okon/blob/main/assets/css/style.css) - стили калькулятора

[admin/index.php](https://github.com/Ivankaz/kalkuljator-okon/blob/main/admin/index.php) - админка калькулятора (пароль: admin)\
[admin/php/login.php](https://github.com/Ivankaz/kalkuljator-okon/blob/main/admin/php/login.php) - вход в админку\
[admin/php/logout.php](https://github.com/Ivankaz/kalkuljator-okon/blob/main/admin/php/logout.php) - выход из админки\
[admin/php/admin.php](https://github.com/Ivankaz/kalkuljator-okon/blob/main/admin/php/admin.php) - API админки (сохранение настроек, изменение пароля)

### Скриншоты
![Калькулятор 1](https://github.com/Ivankaz/kalkuljator-okon/blob/main/screenshots/%D0%9A%D0%B0%D0%BB%D1%8C%D0%BA%D1%83%D0%BB%D1%8F%D1%82%D0%BE%D1%80%201.png)
![Калькулятор 2](https://github.com/Ivankaz/kalkuljator-okon/blob/main/screenshots/%D0%9A%D0%B0%D0%BB%D1%8C%D0%BA%D1%83%D0%BB%D1%8F%D1%82%D0%BE%D1%80%202.png)
![Калькулятор 3](https://github.com/Ivankaz/kalkuljator-okon/blob/main/screenshots/%D0%9A%D0%B0%D0%BB%D1%8C%D0%BA%D1%83%D0%BB%D1%8F%D1%82%D0%BE%D1%80%203.png)
![Админка калькулятора](https://github.com/Ivankaz/kalkuljator-okon/blob/main/screenshots/%D0%90%D0%B4%D0%BC%D0%B8%D0%BD%D0%BA%D0%B0.png)
