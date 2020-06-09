<?php
// ----------------------------конфигурация-------------------------- //

$adminemail="land.dsv.com.ua@gmail.com";  // e-mail админа

$date=date("d.m.y"); // число.месяц.год
$time=date("H:i"); // часы:минуты:секунды
$backurl="http://adres.d-sv.com.ua/ru/thnks-order.html";  // На какую страничку переходит после отправки письма

$name = $_POST['user_name2'];

$phone = $_POST['user_phone2'];


$options = $_POST['optionsFinalText'];

$products = $_POST['ordinfo'];

$total_price = $_POST['total_price'];

$timer_text = implode(' ',explode("\n", $_POST['timer_text']));

$data_lead = [
    'name'          => $name,
    'phone'         => $phone,
    'options'       => $options,
    'products'      => $products,
    'timer_text'    => $timer_text,
    'total_price'   => $total_price
];

$msg="
Ім'я: $name
Телефон: $phone
Повідомлення: Замовлення таблички;
Опції: $options.
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
            'OPPORTUNITY' => $data_lead["total_price"],
            'CURRENCY_ID' => "UAH",
			'PHONE' => array(array('VALUE' => $data_lead["phone"], 'VALUE_TYPE' => 'WORK')),
			'COMMENTS' => $data_lead["options"]. '  ' .$data_lead["timer_text"],
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


createLPOrder($data_lead);

print "<script language='Javascript'><!--
function reload() {location = \"$backurl\"}; setTimeout('reload()', 0);
//--></script>";
exit;

function createLPOrder ($data) {

    // формируем массив с товарами в заказе (если товар один - оставляйте только первый элемент массива)

        $products_list = array();

        if (!empty($data['products'])) {
            foreach ($data['products'] as $product) {
                $products_list[] = array(
                        'product_id' => $product['id'],
                        'price' => $product['price'],
                        'count' => '1',
                );
            }
        }

        $products_str = urlencode(serialize($products_list));
        $sender = urlencode(serialize($_SERVER));
        // параметры запроса

        $data = array(
            'key'             => '40c6bcf758a4553e7182e41e94659e1e', //Ваш секретный токен
            'order_id'        => number_format(round(microtime(true)*10),0,'.',''), //идентификатор (код) заказа (*автоматически*)
            'country'         => 'UA',                         // Географическое направление заказа
            'office'          => '1',                          // Офис (id в CRM)
            'products'        => $products_str,                    // массив с товарами в заказе
            'bayer_name'      => $data['name'],            // покупатель (Ф.И.О)
            'phone'           => $data['phone'],           // телефон
            'email'           => '',           // электронка
            'comment'         => $data['options']. '  ' .$data['timer_text'],    // комментарий
            'payment'         => '',                           // вариант оплаты (id в CRM)
            'sender'          => $sender,
            'additional_1'    => '',                               // Дополнительное поле 1
            'additional_2'    => '',                               // Дополнительное поле 2
            'additional_3'    => '',                               // Дополнительное поле 3
            'additional_4'    => ''                                // Дополнительное поле 4
        );

        // запрос
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'http://dsv.lp-crm.top/api/addNewOrder.html');
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        $out = curl_exec($curl);
        curl_close($curl);

        //$out – ответ сервера в формате JSON


}
?>