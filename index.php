<!DOCTYPE html>
<html>
<head>
	<title>Notas</title>
</head>
<body>
<?php
$dict1 = ["background:transparent;","background:green;","background:transparent;","background:red;"];
//strtotime(date("d.m.Y")) < strtotime("15.10.2020") сравнение дат
$connect = mysqli_connect("127.0.0.1","root","root","Voldemar");

$users_query = mysqli_query($connect,"SELECT * FROM users_vkhack20 WHERE id='".$_GET['user_id']."'");
$user = $users_query->fetch_assoc();
$users_query = mysqli_query($connect,"SELECT * FROM users_vkhack20");
$users = [];
for ($i=0; $i<$users_query->num_rows; $i++) { 
	$users[$i] = $users_query->fetch_assoc();
}

$tasks_query = mysqli_query($connect,"SELECT * FROM tasks_vkhack20 WHERE worker_id='".$_GET['user_id']."' AND parent_id=''");
$tasks_main = [];
for ($i=0; $i<$tasks_query->num_rows; $i++) { 
	$tasks_main[$i] = $tasks_query->fetch_assoc();
}

if ($user["admin"]==1) {
	echo "Страница от лица администратора. Он может просматривать свои раб задачии, выполнять и просрачивать; работать со своими заметками: добавлять, редактировать, удалять; добавлять задачи своим подчинённым (на данный момент всем остальным), ставить им exp и проверять эти работы при их выполнении<br>";
	echo "Заметки: создание, редактирование, удаление и наследование.<br>";
	$tasks_query = mysqli_query($connect,"SELECT * FROM tasks_vkhack20 WHERE parent_id!=''");
	$tasks_light = [];
	for ($i=0; $i<$tasks_query->num_rows; $i++) { 
		$tasks_light[$i] = $tasks_query->fetch_assoc();
		if ($tasks_light[$i]["deadline"]!='') {
			echo $tasks_light[$i]["title"]." (until ".$tasks_light[$i]["deadline"].")<br>";
		} else {
			echo $tasks_light[$i]["title"]."<br>";
		}
		//дочерние задачи неопубл - 1
	}
	echo "Добавить заметку:"; ?>
	<form method="post" action="ins_task.php">
		<input type="text" name="title">
		<input type="hidden" name="exp" value="0">
		<input type="date" name="deadline">
		<input type="hidden" name="worker_id" value="<?php echo $user["id"]; ?>">
		<input type="hidden" name="parent_id" value="0">
		<input type="submit" name="">
	</form>
	<?php echo "раб задачи на выполнение:<br>";
	for ($i=0; $i < count($tasks_main); $i++) { 
		$iplus1 = $i+1;
		echo $iplus1.") ".$tasks_main[$i]["title"]." - ".$tasks_main[$i]["exp"]." exp (".$dict1[$tasks_main[$i]['status']].")<br>";
		?><p>Завершить:</p>
		<form method="post" action="upd.php?op=0">
			<input type="" name="description" placeholder="комментарии и ссылка на работу">
			<input type="hidden" name="id_task" value="<?php echo $tasks_main[$i]['title']; ?>">
			<input type="hidden" name="user_id" value="<?php echo $user["id"]; ?>">
			<input type="submit" name="" value="Отправить на проверку">
		</form>
		<?php
	}
	echo "добавление тасков для других работников:<br>";
	$users_query = mysqli_query($connect,"SELECT * FROM users_vkhack20");
	$rabi = []; //заполнение массива подчинёнными юзера
	for ($i=0; $i<$users_query->num_rows; $i++) { 
		$rab = $users_query->fetch_assoc();
		$admins_id = explode(' ,', $rab["admins_id"]);
		if (in_array($_GET['user_id'], $admins_id)) {
			array_push($rabi, $rab);
		}
	}
	for ($i=0; $i < count($rabi); $i++) { 
		echo $rabi[$i]["login"].":<br>";
		$tasks_query = mysqli_query($connect,"SELECT * FROM tasks_vkhack20 WHERE worker_id='".$rabi[$i]["id"]."'");
		for ($j=0; $j<$tasks_query->num_rows; $j++) {
			$task = $tasks_query->fetch_assoc();
			echo $task["title"]." - ".$task["exp"]." exp<br>";
		}
		?>
		<form method="post" action="ins_task.php">
			<input type="text" name="title">
			<input type="text" name="exp">
			<input type="date" name="deadline">
			<input type="hidden" name="worker_id" value="<?php echo $rabi[$i]["id"]; ?>">
			<input type="hidden" name="parent_id" value="">
			<input type="submit" name="">
		</form>
		<?php 
	}
	

	?>
	<script type="text/javascript">
		
	</script>
<?php
}
?>

</body>
</html>