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

$db = get_db_connect();
$user = get_login_user($db);
if(is_admin($user)) {
    $data = get_histories($db);
} else {
    $data = get_histories($db, $user["user_id"]);
}

include_once VIEW_PATH . 'history_view.php';


