<?php
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

// получаем параметры заказа
$orders = $_POST['orders'];
$clientName  = filter_input(INPUT_POST, 'clientName', FILTER_SANITIZE_STRING);
$clientPhone = filter_input(INPUT_POST, 'clientPhone', FILTER_SANITIZE_STRING);
$total = filter_input(INPUT_POST, 'total', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION | FILTER_FLAG_ALLOW_THOUSAND);


// подключаем файл настроек калькулятора
$settingsFile = file_get_contents('../settings.json');
$settings = json_decode($settingsFile, true);

$mail = new PHPMailer(true);
try {
    //$mail->isSMTP();
    //$mail->SMTPDebug = 3;
    $mail->CharSet = 'UTF-8';
    $mail->setFrom('zakaz@rybinsk-okna.ru', 'ЗАКАЗЫ');
    $emails = explode(',', $settings['emails']);
    foreach ($emails as $email) {
        $mail->addAddress(trim($email));
    }
    $mail->Subject = 'Заказ с Тильда — '.$total.' руб.';
    $mail->isHTML(true);

    // содержимое письма
    $text = "
    <h3>Клиент</h3>
    <b>Имя:</b> $clientName<br/>
    <b>Телефон:</b> <a href=\"tel: " . (preg_replace('/[+\s()-]/', '', $clientPhone)) . "\">$clientPhone</a><br/>";

    // заказы
    foreach ($orders as $order) {
        $conf = $order["configuration"];
        $text .= "<br/><hr/><h3>".$conf["name"]."</h3>";

        $text .= "<b>Ширина окна:</b> ".$conf["windowWidth"]." мм<br/>";
        $text .= "<b>Высота окна:</b> ".$conf["windowHeight"]." мм<br/>";
        if ($conf["type"]==="balcony") {
            $text .= "<b>Ширина двери:</b> ".$conf["doorWidth"]." мм<br/>";
            $text .= "<b>Высота двери:</b> ".$conf["doorHeight"]." мм<br/>";
        }

        $text .= "<br/>";

        foreach ($conf["sashes"] as $i => $sash) {
            $text .= "<b>Створка №".($i+1).":</b> ".$sash["openingType"]["name"]."<br/>";
        }

        $text .= "<br/><img width='200px' src='".$order["image"]."'/><br/><br/>";

        $text .= "<b>Тип дома:</b> ".$order["houseType"]["name"]."<br/>";
        $text .= "<b>Профиль:</b> ".$order["profile"]["name"]."<br/>";
        $text .= "<b>Стеклопакет:</b> ".$order["glassPackage"]["name"]."<br/>";
        $text .= "<b>Цвет:</b> ".$order["color"]["name"]."<br/><br>";

        $text .= "<b>Откосы:</b> ".($order["slopes"]==="true" ? '<b>Да</b>' : 'Нет')."<br/>";
        $text .= "<b>Подоконник:</b> ".($order["sill"]==="true" ? '<b>Да</b>' : 'Нет')."<br/>";
        $text .= "<b>Отливы:</b> ".($order["apron"]==="true" ? '<b>Да</b>' : 'Нет')."<br/>";
        $text .= "<b>Москитная сетка:</b> ".($order["mosquitoNet"]==="true" ? '<b>Да</b>' : 'Нет')."<br/>";
        $text .= "<b>Микропроветривание:</b> ".($order["microVentilation"]==="true" ? '<b>Да</b>' : 'Нет')."<br/>";
        $text .= "<b>Монтаж:</b> ".($order["mounting"]==="true" ? '<b>Да</b>' : 'Нет')."<br/><br/>";

        $text .= "<b>ЦЕНА:</b> ".$order["total"]." руб.<br/>";
    }

    // итого
    $text .= "<br/><hr><h3>ИТОГО: ".$total." руб.</h3><br/>";

    $mail->Body = $text;


    // отправляем письмо
    $mail->send();

    echo true;
} catch (Exception $e) {
    echo false;
    // только для отладки
    echo 'Не удалось отправить сообщение о новой заявке. Ошибка: ', $mail->ErrorInfo;
}
