<?php
// ----------------------------конфигурация-------------------------- //

$adminemail="land.dsv.com.ua@gmail.com";  // e-mail админа

$date=date("d.m.y"); // число.месяц.год
$time=date("H:i"); // часы:минуты:секунды
$backurl="http://adres.d-sv.com.ua/ru/thnks-phone.html";  // На какую страничку переходит после отправки письма

$name  = $_POST['user_name'];

$phone = $_POST['user_phone'];

$email = $_POST['user_email'];

$timer_text = implode(' ',explode("\n", $_POST['timer_text']));

$data_lead = [
    'name'          => $name,
    'phone'         => $phone,
    'email'         => $email,
    'timer_text'    => $timer_text
];

$msg="
Ім'я: $name
Телефон: $phone
E-mail: $email
Повідомлення: Замовлення дзвінка
";

 // Отправляем письмо админу
mail("$adminemail", "$date $time Повідомлення від $name", "$msg");



if(!empty($data_lead)){

	require_once (dirname(__DIR__).'/include_pl/crest.php');

	parse_str(parse_url($_SERVER["HTTP_REFERER"], PHP_URL_QUERY), $utm);

	$params = array(
		"fields" => array(
			'TITLE' => 'Новий лід з ADRES.D-SV.COM.UA',
			'NAME' => $data_lead["name"],
			'EMAIL' => array(array('VALUE' => $data_lead["email"], 'VALUE_TYPE' => 'WORK')),
			'PHONE' => array(array('VALUE' => $data_lead["phone"], 'VALUE_TYPE' => 'WORK')),
			'COMMENTS' => $data_lead["timer_text"],
			'UTM_SOURCE' => $utm["utm_source"],
			'UTM_MEDIUM' => $utm["utm_medium"],
			'UTM_CAMPAIGN' => $utm["utm_campaign"],
			'UTM_CONTENT' => $utm["utm_content"],
			'UTM_TERM' => $utm["utm_term"],
		)
	);
	writeToLog($params, "PARAMS");
	$result = CRest::call('crm.lead.add', $params);
	writeToLog($result, "ADD_LEAD");

}

// Выводим сообщение пользователю

print "<script language='Javascript'><!--
function reload() {location = \"$backurl\"}; setTimeout('reload()', 0);
//--></script>";
exit;

?>