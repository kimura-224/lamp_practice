<?php
require_once '../conf/const.php';
require_once '../model/functions.php';
require_once '../model/user.php';
require_once '../model/item.php';
require_once '../model/cart.php';

session_start();

if(is_logined() === false){
  redirect_to(LOGIN_URL);
}
$order_number = get_get('order_number');
$db = get_db_connect();
$user = get_login_user($db);
if(is_admin($user)) {
    $data = get_details($db, $order_number);
} else {
    $data = get_details($db, $order_number ,$user["user_id"]);
}
$header = get_history($db, $order_number);

include_once VIEW_PATH . 'detail_view.php';


