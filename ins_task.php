<?php
if ($_POST["title"]!='' AND $_POST["worker_id"]!='') {
	$connect = mysqli_connect("127.0.0.1","root","root","voldemar");
	$ins = mysqli_query($connect,'INSERT INTO `tasks_vkhack20` (`id`, `title`, `status`, `description`, `exp`, `date`, `deadline`, `parent_id`, `worker_id`, `creator_id`) VALUES (NULL,"'.$_POST["title"].'","0","'.$_POST["description"].'","'.$_POST["exp"].'","'.date("Y-m-d").'","'.$_POST["deadline"].'","'.$_POST["parent_id"].'","'.$_POST["worker_id"].'","'.$_POST["creator_id"].'")');
	header('Location: admin.php?user_id='.$_POST["creator_id"]);
} else {
	header('Location: admin.php?user_id='.$_POST["creator_id"]);
}

