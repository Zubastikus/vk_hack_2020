<?php
$connect = mysqli_connect("127.0.0.1","root","root","voldemar");
if ($_POST["id"]!="" and $_GET["op"]=="0") {
	$del = mysqli_query($connect,'DELETE FROM `tasks_vkhack20` WHERE `tasks_vkhack20`.`id` = '.$_POST['id']);
}
header('Location: admin.php?user_id='.$_POST["user_id"]);