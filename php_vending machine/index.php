<?php
$host   = 'localhost'; // データベースのホスト名又はIPアドレス
$user   = 'codecamp25631';  // MySQLのユーザ名
$passwd = 'XRMPISCA';    // MySQLのパスワード
$dbname = 'codecamp25631';
$data = array();
$drink ='';
$price ='';

if($link = mysqli_connect($host, $user, $passwd, $dbname)){

    mysqli_set_charset($link, 'UTF8');
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        if(isset($_POST['price']) === true){
            $price = $_POST['price'];
        }    
    } else {
        $err_msg[] = 'SQL失敗:' /*. $sql*/;
    }
        $sql = 'SELECT drink_table . drink_id , drink_table . drink_name , drink_table . price , drink_table . image , inventory_table . quantity FROM drink_table JOIN inventory_table on drink_table . drink_id = inventory_table . drink_id WHERE status = 1';
        if ($result = mysqli_query($link, $sql)){ 
           while($row = mysqli_fetch_assoc($result)){
           $data[] = $row;
           }
           /*print '<pre>';
           var_dump($data);
           print '</pre>';*/
        }else{ 
            $err_msg[] = 'SQL失敗:' . $sql;
        }
}
?>

<html lang="ja">
<head>
   <meta charset="UTF-8">
   <title>自動販売機システム</title>
</head>
<body>
    <h1>ドリンク購入ページ</h1>
    
    <form action="result.php" method="post">  
    金額:<input type='text' name='price' value='<?php print $price;?>'></input>

        <table border=1>
            <tr>
                <th>商品画像</th>
                <th>商品名</th>
                <th>価格</th>
                <th>購入ボタン</th>
                
            </tr>
       </tr>
<?php   foreach ($data as $value) { ?>
            <tr> 
                <td><img src="./images/<?php print $value['image'];?>"></td> 
                <td><span><?php print $value['drink_name'].'<br>'; ?></span></td>
                <td><span><?php print $value['price']; ?>円</span></td>
                <td>
<?php   if ($value['quantity'] > '0' ) { ?>
            <input  type="radio" name="order" value="<?php print $value['drink_id']; ?>">
<?php   }else{ 
            print '売り切れ';
}?>
                </td> 
            </tr>
<?php   } ?> 
        </table>
           <input type='submit' value='・・・購入・・・'>
    </form>
</body>
</html>