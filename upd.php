<?php
$connect = mysqli_connect("127.0.0.1","root","root","voldemar");
if ($_GET["op"]=='0') {
	$upd = mysqli_query($connect,"UPDATE `tasks_vkhack20` SET `description` = '".$_POST["description"]."', `status` = '2' WHERE `tasks_vkhack20`.`id` = ".$_POST["id"]);
	$name = mysqli_query($connect,"SELECT * FROM users_vkhack20 WHERE id='".$_POST['user_id']."'")->fetch_assoc()["login"];
	$title = mysqli_query($connect,"SELECT * FROM tasks_vkhack20 WHERE id='".$_POST['id']."'")->fetch_assoc()["title"];
	$ins = mysqli_query($connect,"INSERT INTO `notifications_vkhack20` (`id`, `task_id`, `type`, `title`, `description`, `work_description`, `datetime`, `view`, `worker_id`) VALUES (NULL, '".$_POST['id']."', '0', '".$name." отправил задачу на проверку', '".$name." отправил задачу \"".$title."\" на проверку. Вам требуется проверить её и принять или отклонить работу. Нажмите на уведомление для доп. информации и ссылки на работу.', '".$_POST['description']."', '".date('Y-m-d H:i')."', '0', '".$_POST['creator_id']."')");
} else if ($_GET["op"]=='3') {
	$upd = mysqli_query($connect,"UPDATE `tasks_vkhack20` SET `description` = '".$_POST["description"]."', `deadline` = '".$_POST["deadline"]."' WHERE `tasks_vkhack20`.`id` = ".$_POST["id"]);
} else if ($_GET["op"]=='4') {
	$upd = mysqli_query($connect,"UPDATE `tasks_vkhack20` SET `deadline` = '".$_POST["deadline"]."' WHERE `tasks_vkhack20`.`id` = ".$_POST["id"]);
} else if ($_GET["op"]=='1') {
	$upd = mysqli_query($connect,"UPDATE `tasks_vkhack20` SET `status` = '1' WHERE `tasks_vkhack20`.`id` = ".$_POST["id"]);
}
header('Location: admin.php?user_id='.$_POST["user_id"]);