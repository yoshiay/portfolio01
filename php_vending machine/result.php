<?php
$host   = ''; 
$user   = '';  
$passwd = '';    
$dbname = '';
$order = '';
$price = '';
$quantity = '';
$drink_id = '';
$date = '';
$err_msg = array();
$massage1 = '';
$massage2 = '';
$row = '';

if($link = mysqli_connect($host, $user, $passwd, $dbname)){
    
    mysqli_set_charset($link, 'UTF8');
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        if(isset($_POST['order'])){
        $order = $_POST['order'];
        }
        if(isset($_POST['price'])){
        $price = $_POST['price'];
        }
        $date = date('Y-m-d H:i:s');
        if($order === ''){
            $err_msg[] = '商品を選択して下さい';
        }
        if($price === ''){
            $err_msg[] = '金額を投入して下さい';
        }
        if(empty($err_msg) === true){
            $sql = 'SELECT drink_table . drink_id , drink_table . drink_name , drink_table . price , drink_table . image ,drink_table . status, inventory_table . quantity FROM drink_table JOIN inventory_table on drink_table . drink_id = inventory_table . drink_id WHERE drink_table . drink_id = ' . $order;
            
            if($result = mysqli_query($link, $sql)){ 
                $row = mysqli_fetch_assoc($result);
                
                print $row['drink_name'];
                if($price < $row['price']){
                     $err_msg[] = '投入金額が足りません';
                }
                $quantity = $row['quantity'];
                if($quantity === '0'){
                     $err_msg[] = '商品がありません';
                }
                $status = $row['status'];
                if($status === '0'){
                    $err_msg[] = '商品を購入できません';
                }
                if(empty($err_msg) === true){
                    $quantity = $quantity - 1;
                    mysqli_autocommit($link, false);
                    $sql = 'UPDATE inventory_table SET quantity = ' . $quantity . ' WHERE drink_id = ' . $order;
                    if(mysqli_query($link, $sql) === true){
                        /*$order = mysqli_insert_id($link);*/
                        $sql = 'INSERT INTO history_table(drink_id,purchased_at) VALUES (' . $order .', now())' ;
                        if(mysqli_query($link, $sql) === true){
                            $massage[] = '購入履歴の処理完了';
                        }else{    
                            $err_msg[] = 'history_table:updertエラー';
                        }
                    }else{
                        $err_msg[] = 'drink_table:updertエラー'; 
                }
                if (count($err_msg) === 0) {
                   // 処理確定
                   mysqli_commit($link);
                   $price = $price - $row['price'];
                   $massage1 = 'ガシャン！【' . $row['drink_name'] . '】が買えました！';
                   $massage2 = 'お釣りは【' . $price . '円】です';
                } else {
                   // 処理取消
                   mysqli_rollback($link);
                }   
                }
            }
            
        }
            
    }    
}
?>

<html lang="ja">
<head>
   <meta charset="UTF-8">
   <title>自動販売機システム</title>
</head>
<body>
    <h1>自動販売機結果</h1>
    <img src="./images/<?php print $row['image'];?>">
    <?php 
    foreach($err_msg as $value){
        print $value.'<br>';
    }?>
     <?php print $massage1.'<br>';?>
     <?php print $massage2.'<br>';?>
    <a href='http://codecamp25631.lesson9.codecamp.jp//php/2110/index.php'>戻る</a>
</body>
</html
