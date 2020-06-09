<?php

createLPOrder();

exit();

function createLPOrder () {
    
    // формируем массив с товарами в заказе (если товар один - оставляйте только первый элемент массива)
        $products_list = array(
            0 => array(
                    'product_id' => 18,    //код товара (из каталога CRM)
                    'price'      => 460, //цена товара 1
                    'count'      => '1',                     //количество товара 1
                    // если есть смежные товары, тогда количество общего товара игнорируется
            ),
            1 => array(
                    'product_id' => 13,    //код товара 2 (из каталога CRM)
                    'price'      => 400, //цена товара 2
                    'count'      => '1',                     //количество товара 2
                    // если есть смежные товары, тогда количество общего товара игнорируется
                )
        );
        
        
        $products = urlencode(serialize($products_list));
        $sender = urlencode(serialize($_SERVER));
        // параметры запроса
                
        $data = array(
            'key'             => '40c6bcf758a4553e7182e41e94659e1e', //Ваш секретный токен
            'order_id'        => number_format(round(microtime(true)*10),0,'.',''), //идентификатор (код) заказа (*автоматически*)
            'country'         => 'UA',                         // Географическое направление заказа
            'office'          => '1',                          // Офис (id в CRM)
            'products'        => $products,                    // массив с товарами в заказе
            'bayer_name'      => 'test',            // покупатель (Ф.И.О)
            'phone'           => '09247120124',           // телефон
            'email'           => 'test@test.ru',           // электронка
            'comment'         => 'comment test',    // комментарий
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
        echo '<pre>';
        var_dump(json_decode($out));
        exit();
        //$out – ответ сервера в формате JSON


}