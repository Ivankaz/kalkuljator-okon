<?php
session_start();

// удаляем переменную из сессии
unset($_SESSION['login']);
// удаляем сессию
session_destroy();
// перенаправляем в форму входа в настройки калькулятора
$host  = $_SERVER['HTTP_HOST'];
$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
header("Location: https://$host$uri/../../admin/");