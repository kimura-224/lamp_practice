<!DOCTYPE html>
<html lang="ja">
<head>
<?php include VIEW_PATH . 'templates/head.php'; ?>
  <title>購入明細</title>
  <!-- レイアウト呼び出し -->
  <link rel="stylesheet" href="<?php print(STYLESHEET_PATH . 'cart.css'); ?>">
</head>
<body>
  <?php include VIEW_PATH . 'templates/header_logined.php'; ?>
  <h1>購入明細</h1>
  <div class="container">

    <?php include VIEW_PATH . 'templates/messages.php'; ?>

    <?php if(count($data) > 0){ ?>
        <table class="table table-bordered">
        <thead class="thead-light">
          <tr>
            <th>注文番号</th>
            <th>購入日時</th>
            <th>合計金額</th>
          </tr>
        </thead>
<!-- データの表示 -->
        <tbody>
          <tr>
            <td><?php print($header['order_number']); ?></td>
            <td><?php print($header['create_datetime']); ?></td>
            <td><?php print($header['total']); ?></td>
          </tr>
        </tbody>
      </table>

      <table class="table table-bordered">
        <thead class="thead-light">
          <tr>
            <th>商品名</th>
            <th>購入価格</th>
            <th>購入数</th>
            <th>合計金額</th>
          </tr>
        </thead>
<!-- データの表示 -->
        <tbody>
          <?php foreach($data as $datas){ ?>
          <tr>
            <td><?php print($datas['name']); ?></td>
            <td><?php print($datas['price']); ?></td>
            <td><?php print($datas['amount']); ?></td>
            <td><?php print($datas['subtotal']); ?></td>
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