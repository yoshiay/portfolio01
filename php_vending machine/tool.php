<?php

$host   = ''; 
$user   = '';  
$passwd = '';    
$dbname = '';
$drink_name = '';
$price ='';
$quantity ='';
$massage = array();
$status = 1 ;
$file = '';
$err_msg = array();
$data = array();
$sql_kind = '';
$new_quantity = '';
$drink_id = '';


if($link = mysqli_connect($host, $user, $passwd, $dbname)){
    
    mysqli_set_charset($link, 'UTF8');
    
  
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        if(isset($_POST['sql_kind'])){
            $sql_kind = $_POST['sql_kind'];
        }
        $date = date('Y-m-d H:i:s');
        if($sql_kind === 'insert'){
            if(isset($_POST['drink_name'])){
                $drink_name = $_POST['drink_name'];
            }
            if(isset($_POST['price'])){
                $price = $_POST['price'];
            }
            if(isset($_POST['quantity'])){
                $quantity = $_POST['quantity'];
            }
            if(isset($_POST['status'])){
                $status = $_POST['status'];
            }
            if($drink_name === ''){
                $err_msg[] = '名前を入力して下さい';
            }
            if($price === ''){
                $err_msg[] = '値段を入力して下さい';
            }else if(preg_match("/^([0-9]+[0-9]*)$/",$price) !== 1){
                $err_msg[] = '値段を半角数字で入力して下さい';
            }
            if($quantity === '' ){
                $err_msg[] = '数量を入力して下さい';
            }else if(preg_match("/^([1-9]+[0-9]*)$/",$quantity) !== 1){
                $err_msg[] = '数量を半角数字で入力して下さい';
            }
            if (is_uploaded_file($_FILES['file']['tmp_name'])) {
                $filename = './images/' . $_FILES['file']['name'];
                $type = mime_content_type($_FILES['file']['tmp_name']);
                if($type === 'image/jpeg' || $type === 'image/png'){
                    if ( move_uploaded_file($_FILES['file']['tmp_name'] , $filename )) {
        	            /*$massage[] = $filename . "をアップロードしました。";*/
        	            $file = $_FILES['file']['name'];
                    } else {
                        $err_msg[] = "ファイルをアップロードできません。";
                    }
                }else{
                    $err_msg[] = 'ファイルの種類が異なります。';
                }
            } else {
                $err_msg[] = "ファイルが選択されていません。";
            }
            if(empty($err_msg) === true){
                mysqli_autocommit($link, false);
                $sql = 'INSERT INTO drink_table(drink_name, price, created_at, status, image) VALUE ("' . $drink_name . '",' . $price . ',"' . $date .'",'. $status . ',"' . $file .'")';
                /*print $sql;*/
                
                if(mysqli_query($link, $sql) === true){
                    $drink_id = mysqli_insert_id($link);
                    $sql = 'INSERT INTO inventory_table(drink_id,quantity) VALUE ('.$drink_id .',' . $quantity . ')';  
                    if(mysqli_query($link, $sql) === TRUE){
                        $massage[] = '商品追加の処理完了';
                    }else{    
                        $err_msg[] = 'inventory_table:insertエラー';
                    }
                }else{
                   $err_msg[] = 'drink_table:insertエラー'; 
                }
                if (count($err_msg) === 0) {
                   // 処理確定
                   mysqli_commit($link);
                } else {
                   // 処理取消
                   mysqli_rollback($link);
                }
            
            }
        }else if($sql_kind === 'update_stock'){
            if(isset($_POST['new_quantity'])){
                $new_quantity = $_POST['new_quantity'];
            }
            print $new_quantity;
            if(isset($_POST['drink_id'])){
                $drink_id = $_POST['drink_id'];
            }
            if($new_quantity === ''){
                $err_mag[] = '変更数量を入力して下さい';
            }else if(ctype_digit($new_quantity) !== true){
                $err_msg[] = '0以上の整数で入力して下さい';
            }
            if($drink_id === ''){
                $err_msg[] = '$drink_id,エラー';
            }
            if(empty($err_msg) === true){
                $sql = 'UPDATE inventory_table SET quantity = ' . $new_quantity . ' WHERE drink_id = ' . $drink_id;
                if(mysqli_query($link, $sql) === TRUE){
                    $massage[] = '数量変更の処理完了しました';
                }else{
                    $err_msg[] = '数量を入力して下さい';
                }
            }
        
        }else if($sql_kind === 'update_status'){
            if(isset($_POST['status'])){
                $status = $_POST['status'];
            }
            if($status === '公開'){
                $status = 1;
            }else{
                $status = 0;
            }
            if($status === ''){
                $err_msg[] = 'ステータスの値を入力して下さい';
            }
        
            if(isset($_POST['drink_id'])){
                $drink_id = $_POST['drink_id'];
            }
            if($drink_id === ''){
            $err_msg[] = '$drink_id,エラー';
            }
            if(empty($err_msg) === true){
        
                $sql = 'UPDATE drink_table SET status = ' . $status . ' WHERE drink_id = ' . $drink_id;
                if(mysqli_query($link, $sql) === TRUE){
                    $massage[] = 'ステータス変更の処理完了';
                }else{
                    $err_msg[] = 'ステータス変更の失敗';
                }
                
            }
        }
          
    }else{
        $err_msg[] = '';
    }
    // 商品一覧の取得
    $sql = 'SELECT drink_table . drink_id , drink_table . drink_name , drink_table . price ,drink_table . status , drink_table . image , inventory_table . quantity FROM drink_table JOIN inventory_table on drink_table . drink_id = inventory_table . drink_id';
    
    if ($result = mysqli_query($link, $sql)){ 
       while($row = mysqli_fetch_assoc($result)){
       $data[] = $row;
       }
       /*print '<pre>';
       var_dump($data);
       print '</pre>';*/
       
    } else {
       $err_msg[] = 'SQL失敗:' . $sql;
    }
}
?>
<!DOCTYPE HTML>
<html lang="ja">
<head>
   <meta charset="UTF-8">
   <title>自動販売機システム</title>
</head>
<body>
    <h1>ドリンク管理システム</h1>
    <?php
    foreach($err_msg as $value){
        print $value.'<br>';
    }?>
    <?php
    foreach($massage as $value){
        print $value.'<br>';
    }
    ?>
    <p>□新規商品追加</p>
    <form action="tool.php" method="post" enctype="multipart/form-data">
            <label>商品名:<input type="text" name="drink_name" value="<?php print $drink_name; ?>"></label><br>
            <label>値段:<input type="text" name="price" value="<?php print $price; ?>"></label><br>
            <label>数量:<input type="text" name="quantity" value="<?php print $quantity; ?>"></label><br>
            <input type="file" name="file" value=""><br>
            <select name='status'>
                <option value='1'>公開</option>
                <option value='0'>非公開</option>
            </select><br>
            <input type="submit" value="商品追加">
            <input type="hidden" name="sql_kind" value="insert">
    </form>
    <h1>商品情報変更</h1>
    <table border="1" >
        <tr>
            <th>商品画像</th>
            <th width="150px">商品名</th>
            <th>価格</th>
            <th>在庫数</th>
            <th width="150px">ステータス</th>
       </tr>
<?php
foreach($data as $value){
?>
    
        <tr>
            <td><img src="./images/<?php print $value['image'];?>"></td>
            <td><?php print $value['drink_name'];?></td>
            <td><?php print $value['price'];?></td>
    
            <td>
                <form action="tool.php" method="post">
                <input type="text" name="new_quantity" value="<?php print $value['quantity'];?>">
                <input type='submit' value='変更'>
                <input type="hidden" name="sql_kind" value="update_stock">
                <input type="hidden" name="drink_id" value="<?php print $value['drink_id'];?>">
                </form>
            </td>
            
            <td>
                <form action="tool.php" method="post">
                <?php if($value['status'] === '1'){?>
                <input type='submit' name='status' value='非公開' style="width:120px">
                <?php }else{ ?>
                <input type='submit' name='status' value='公開' style="width:120px">        
                <?php }?>
                <input type="hidden" name="sql_kind" value="update_status">
                <input type="hidden" name="drink_id" value="<?php print $value['drink_id'];?>">
                </form>
            </td>   
        </tr>
    

<?php
    
}
?>

    </table>
</body>
</html>
