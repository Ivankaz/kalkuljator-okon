<?php
$action = isset($_GET['action']) ? $_GET['action'] : exit;

// подключаем файл настроек калькулятора
$settingsFile = file_get_contents('../../assets/settings.json');
$settings = json_decode($settingsFile, true);

switch ($action) {
    case "save":
        $settings = isset($_POST['settings']) ? $_POST['settings'] : exit;
        $settings = json_encode($settings, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
        if (file_put_contents('../../assets/settings.json', $settings)!==false) {
            echo true;
        } else {
            echo false;
        }
        break;
    case "changePassword":
        $password = isset($_POST['password']) ? $_POST['password'] : exit;
        $settings["passwordHash"] = password_hash($password, PASSWORD_DEFAULT);
        $settings = json_encode($settings, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
        if (file_put_contents('../../assets/settings.json', $settings)!==false) {
            echo true;
        } else {
            echo false;
        }
        break;
}