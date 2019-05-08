<?php
$filename = 'bbs.txt';
$log = date('Y-m-d H:i:s');
$comment = '';
$my_name = '';
$error=array();
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    if (isset($_POST['my_name']) === TRUE ){
        $my_name = $_POST['my_name'];
    }
    if (isset($_POST['comment']) === TRUE ){    
        $comment = $_POST['comment'];
    }
    if(mb_strlen($my_name) > 20){
        $error[] = '名前を20文字以内で入力して下さい';
    }
    if(mb_strlen($my_name) === 0){
        $error[] = '名前を入力して下さい'; 
    }
    if(mb_strlen($comment) > 100){
        $error[] = 'コメントを100文字以内で入力して下さい';
    }
    if(mb_strlen($comment) === 0){
        $error[] = 'コメントを入力して下さい'; 
    }
    if(empty($error) === TRUE ) {
        $box= $my_name . ' ' . $comment . ' ' .$log."\n";
        if ( ($fp = fopen ($filename, 'a') ) !== FALSE ){
            if (fwrite($fp,$box)===FALSE){
                $error[]= 'アクセス読み込みし失敗' . $filename;
            }
            fclose($fp);
        }   
    }   
}

$data =array();
if (is_readable($filename)=== TRUE ){
    if (($fp = fopen($filename,'r'))!==FALSE){
        while (($tmp = fgets ($fp)) !== FALSE){
            $data[]=htmlspecialchars($tmp,ENT_QUOTES,'UTF-8');
        }
        fclose($fp);
    }
}else{
    print 'ファイルが存在してません';
}
?>
<!DOCTYPE HTML>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>ひとこと掲示板</title>
        <style>
            div{
                background-color: #00ffff;
            }
        </style>
    </head>
    <body>
        <div>
            <h2>ひとこと掲示板</h2>
            <?php foreach ($error as $value){?>
            <p><?php print $value;?></p>
            <?php } ?>
            <form method='post'>
                <label>名前：<input type="text" name="my_name" value="<?php print $my_name ?>"></label>
                <label>ひとこと：<input type="textarea" name="comment" size="60" value="<?php print $comment ?>"></label>
                <input type="submit" value="送信">
            </form>
        </div>
            <?php foreach ($data as $read) { ?>
            <p><?php print $read; ?></p>
            <?php } ?>
    </body>
</html>

