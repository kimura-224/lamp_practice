<!-- ここから -->
<!DOCTYPE html>
<html lang="ja">
<head>
<?php include VIEW_PATH . 'templates/head.php'; ?>
  <title>購入履歴</title>
  <!-- レイアウト呼び出し -->
  <link rel="stylesheet" href="<?php print(STYLESHEET_PATH . 'cart.css'); ?>">
</head>
<body>
  <?php include VIEW_PATH . 'templates/header_logined.php'; ?>
  <h1>購入履歴</h1>
  <div class="container">

    <?php include VIEW_PATH . 'templates/messages.php'; ?>

    <?php if(count($data) > 0){ ?>
      <table class="table table-bordered">
        <thead class="thead-light">
          <tr>
            <th>購入番号</th>
            <th>合計金額</th>
            <th>購入日時</th>
            <th>購入明細</th>
          </tr>
        </thead>
<!-- データの表示 -->
        <tbody>
          <?php foreach($data as $datas){ ?>
          <tr>
            <td><?php print($datas['order_number']); ?></td>
            <td><?php print($datas['total']); ?></td>
            <td><?php print($datas['create_datetime']); ?></td>
            <td>
              <form action = "detail.php">
                <input type="submit" value="購入明細表示">
                <!-- トークン追加のための処理 -->
                <input type="hidden" name="order_number" value="<?php print($datas['order_number']); ?>">
              </form>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    <?php } else { ?>
      <p>履歴がありません。</p>
    <?php } ?> 
  </div>
</body>
</html>