<?php
session_start();

$password = $_POST['password'];

// подключаем файл настроек калькулятора
$settingsFile = file_get_contents('../../assets/settings.json');
$settings = json_decode($settingsFile, true);

// если хеши паролей равны
if (password_verify($password, $settings['passwordHash'])) {
    $_SESSION['login'] = true;
    echo true;
} else {
    echo false;
}