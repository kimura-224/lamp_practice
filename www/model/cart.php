<?php 
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'db.php';

function get_user_carts($db, $user_id){
  $sql = "
    SELECT
      items.item_id,
      items.name,
      items.price,
      items.stock,
      items.status,
      items.image,
      carts.cart_id,
      carts.user_id,
      carts.amount
    FROM
      carts
    JOIN
      items
    ON
      carts.item_id = items.item_id
    WHERE
      carts.user_id = ?
  ";
  return fetch_all_query($db, $sql,[$user_id]);
}

function get_user_cart($db, $user_id, $item_id){
  $sql = "
    SELECT
      items.item_id,
      items.name,
      items.price,
      items.stock,
      items.status,
      items.image,
      carts.cart_id,
      carts.user_id,
      carts.amount
    FROM
      carts
    JOIN
      items
    ON
      carts.item_id = items.item_id
    WHERE
      carts.user_id = ?
    AND
      items.item_id = ?
  ";

  return fetch_query($db, $sql,[$user_id,$item_id]);

}

function add_cart($db, $user_id, $item_id ) {
  $cart = get_user_cart($db, $user_id, $item_id);
  if($cart === false){
    return insert_cart($db, $user_id, $item_id);
  }
  return update_cart_amount($db, $cart['cart_id'], $cart['amount'] + 1);
}

function insert_cart($db, $user_id, $item_id, $amount = 1){
  $sql = "
    INSERT INTO
      carts(
        item_id,
        user_id,
        amount
      )
    VALUES(?, ?, ?)
  ";

  return execute_query($db, $sql,[$item_id,$user_id,$amount]);
}

// 履歴への追加
function insert_histry($db, $user_id){
  $sql = "
    INSERT INTO
        history(
        user_id,
        create_datetime 
      )
    VALUES( ? ,now())
  ";

  return execute_query($db, $sql,[$user_id]);
}

// 明細への追加
function insert_details($db, $item_id, $order_number, $price, $amount){
  $sql = "
    INSERT INTO
      details(
        item_id,
        order_number,
        price,
        amount
      )
    VALUES(?, ?, ?, ?)
  ";

  return execute_query($db, $sql,[$item_id, $order_number, $price, $amount]);
}

function update_cart_amount($db, $cart_id, $amount){
  $sql = "
    UPDATE
      carts
    SET
      amount = ?
    WHERE
      cart_id = ?
    LIMIT 1
  ";
  return execute_query($db, $sql,[$amount,$cart_id]);
}

function delete_cart($db, $cart_id){
  $sql = "
    DELETE FROM
      carts
    WHERE
      cart_id = ?
    LIMIT 1
  ";

  return execute_query($db, $sql,[$cart_id]);
}

function purchase_carts($db, $carts){
  if(validate_cart_purchase($carts) === false){
    return false;
  }
  $db->beginTransaction();
  foreach($carts as $cart){
    if(update_item_stock(
        $db, 
        $cart['item_id'], 
        $cart['stock'] - $cart['amount']
      ) === false){
      set_error($cart['name'] . 'の購入に失敗しました。');
    }
  }
  
  delete_user_carts($db, $carts[0]['user_id']);
  insert_histry($db, $carts[0]['user_id']);
  $order_id = $db->lastInsertId();
  foreach($carts as $cart) {
    insert_details($db, $cart['item_id'], $order_id, $cart['price'], $cart['amount']);
  }
  if(has_error()){
    $db->rollback();
  } else{
    $db->commit();
  }
}

function delete_user_carts($db, $user_id){
  $sql = "
    DELETE FROM
      carts
    WHERE
      user_id = ?
  ";

  execute_query($db, $sql,[$user_id]);
}


function sum_carts($carts){
  $total_price = 0;
  foreach($carts as $cart){
    $total_price += $cart['price'] * $cart['amount'];
  }
  return $total_price;
}

function validate_cart_purchase($carts){
  if(count($carts) === 0){
    set_error('カートに商品が入っていません。');
    return false;
  }
  foreach($carts as $cart){
    if(is_open($cart) === false){
      set_error($cart['name'] . 'は現在購入できません。');
    }
    if($cart['stock'] - $cart['amount'] < 0){
      set_error($cart['name'] . 'は在庫が足りません。購入可能数:' . $cart['stock']);
    }
  }
  if(has_error() === true){
    return false;
  }
  return true;
}


function get_history($db, $order_number) {
  $sql = "
    SELECT
     order_number,
     create_datetime,
     (SELECT SUM(price * amount) FROM details WHERE order_number = history.order_number ) as total

    FROM
      history
    WHERE
    order_number = ? ;   
  ";
  return fetch_query($db, $sql,[$order_number]);

}


function get_histories($db, $user_id = NULL){
  $params = [];
  $sql = "
    SELECT
     order_number,
     create_datetime,
     (SELECT SUM(price * amount) FROM details WHERE order_number = history.order_number ) as total

    FROM
      history 
  ";
  if($user_id !== NULL) {
    $sql .= "WHERE user_id = ? ";
    $params[] = $user_id;
  }
  return fetch_all_query($db, $sql,$params);

}

function get_details($db, $order_number, $user_id = NULL) {
  $params = [$order_number];
  $sql = "
    SELECT
     name,
     details.price,
     amount,
     details.price * amount as subtotal

    FROM
      details 
    JOIN
      items on details.item_id = items.item_id
    WHERE
      order_number = ?
  ";
  if($user_id !== NULL) {
    $sql .= "AND exists(select * FROM history WHERE order_number = ? AND user_id = ?)";
    $params[] = $order_number;
    $params[] = $user_id;

  }
  return fetch_all_query($db, $sql,$params);
}

