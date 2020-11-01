<!doctype html>
<html lang="en">
  <head>





  	<!-- Основной файл для работы. Т.к. регистрация и логин не сделаны, добавляйте после названия файла в url строке '?user_id=' и id -->





	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
	<link rel="stylesheet" href="stylecustom.css">
	<link rel="stylesheet" href="/path/to/cdn/bootstrap.min.css" />
	<link rel="stylesheet" href="css/calendar.css" />

	
	<title>Ulula</title>
	<!-- Core Stylesheet -->
	<link rel="stylesheet" href="src/mini-event-calendar.min.css">
  </head>
  <body>
<?php
function is_admin_for($user_id, $admins)
{
    $admins_id = explode(', ', $admins);
	if (in_array($user_id, $admins_id)) return true;
	else return false;
}

$dict1 = ["background:transparent; color:#38CEC5;","background:green; color: #ffffff;","background:transparent; color:#38CEC5;","background:red; color:#ffffff;"];
//strtotime(date("d.m.Y")) < strtotime("15.10.2020") сравнение дат
$connect = mysqli_connect("127.0.0.1","root","root","Voldemar");

$users_query = mysqli_query($connect,"SELECT * FROM users_vkhack20 WHERE id='".$_GET['user_id']."'");
$user = $users_query->fetch_assoc();
$users_query = mysqli_query($connect,"SELECT * FROM users_vkhack20");
$users = [];
$rabi = [];
for ($i=0; $i<$users_query->num_rows; $i++) { 
	$users = $users_query->fetch_assoc();
	if (is_admin_for($user['id'], $users["admins_id"])) {
		array_push($rabi, $users);
	}
}

$tasks_query = mysqli_query($connect,"SELECT * FROM tasks_vkhack20 WHERE worker_id='".$_GET['user_id']."' AND parent_id=''");
$tasks_main = [];
for ($i=0; $i<$tasks_query->num_rows; $i++) { 
	$tasks_main[$i] = $tasks_query->fetch_assoc();
}

$notifications_query = mysqli_query($connect,"SELECT * FROM notifications_vkhack20 WHERE worker_id='".$_GET['user_id']."' ORDER BY datetime DESC");
$notifications = [];
for ($i=0; $i<$notifications_query->num_rows; $i++) { 
	$notifications[$i] = $notifications_query->fetch_assoc();
}

?>
<!-- modal окно для добавления заметок --> 
<div class="modal fade" id="addNoteModal" tabindex="-1" aria-labelledby="addNoteModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-lg">
		<div class="modal-content" style="border-radius: 15px;">
			<form action="ins_task.php" method="post">
				<div class="modal-header">
					<h5 class="modal-title" id="addNoteModalLabel">Добавить заметку</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<h5>Название</h5>
					<div class="input-group mb-3">
						<input name="title" type="text" class="form-control" style="background-color: #E8F1F1; border-radius: 15px;" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
					</div>
					<h5>Описание</h5>
					<div class="input-group mb-3">
						<input name="description" type="text" class="form-control" style="background-color: #E8F1F1; border-radius: 15px;" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" placeholder="Добавить более подробное описание...">
					</div>
					<h5>Установить крайний срок (необязательно)</h5>
					<div class="input-group mb-3">
						<input name="deadline" type="date" class="form-control" style="background-color: #E8F1F1; border-radius: 15px;" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" placeholder="Добавить более подробное описание...">
					</div>
					<input type="hidden" name="exp" value="0">
					<input type="hidden" name="worker_id" value="<?php echo $user["id"]; ?>">
					<input type="hidden" name="parent_id" value="0">
					<input type="hidden" name="creator_id" value="<?php echo $user['id']; ?>">
				</div>
				<div class="modal-footer">
					<input type="submit" name="" value="Добавить" class="btn btn-primary" style="background-color: #e8f1f1; border-color: #e8f1f1; color: black;">
				</div>
			</form>
		</div>
	</div>
</div>
<!-- modal окно для редактирования заметок --> 
<div class="modal fade" id="redNoteModal" tabindex="-1" aria-labelledby="redNoteModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-lg">
		<div class="modal-content" style="border-radius: 15px;">
			<div class="modal-header">
				<h5 class="modal-title title_modalNote" id="redNoteModalLabel">название заметки</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form action="upd.php?op=3" method="post" id="form_red_modalNote">
					<h5>Описание</h5>
					<div class="input-group mb-3">
						<input name="description" type="text" class="form-control description_modalNote" style="background-color: #E8F1F1; border-radius: 15px;" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" placeholder="Добавить более подробное описание...">
					</div>
					<h5>Крайний срок</h5>
					<div class="input-group mb-3">
						<input name="deadline" type="date" class="form-control deadline_modalNote" style="background-color: #E8F1F1; border-radius: 15px;" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" placeholder="Добавить более подробное описание...">
					</div>
					<input class="worker_id_modalNote" type="hidden" name="worker_id" value="<?php echo $user["id"]; ?>">
					<input class="note_id_modalNote" type="hidden" name="id" value="">
					<input class="user_id_modalNote" type="hidden" name="user_id" value="">
				</form>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-outline-primary" form="form_red_modalNote">Сохранить изменения</button>
				<form action="del.php?op=0" method="post">
					<input class="note_id_modalNote2" type="hidden" name="id" value="">
					<input class="user_id_modalNote2" type="hidden" name="user_id" value="">
					<input type="submit" name="" value="Выполнено (удалить заметку)" class="btn btn-outline-success">
				</form>	
			</div>
		</div>
	</div>
</div>
<!-- modal окно для добавления задач --> 
<div class="modal fade" id="addTaskModal" tabindex="-1" aria-labelledby="addTaskModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-lg">
		<div class="modal-content" style="border-radius: 15px;">
			<form action="ins_task.php" method="post">
				<div class="modal-header">
					<h5 class="modal-title" id="addTaskModalLabel">Добавить задачу</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<h5>Название</h5>
					<div class="input-group mb-3">
						<input name="title" type="text" class="form-control" style="background-color: #E8F1F1; border-radius: 15px;" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
					</div>
					<input type="hidden" name="description" value="">
					<input type="hidden" name="creator_id" value="<?php echo $user['id']; ?>">
					<input type="hidden" name="worker_id" id="id_task_modal_inp">
					<div class="dropdown mt-3">
					  	<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					    Выбрать работника
					  	</button>
					  	<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
					    	<?php for ($i=0; $i < count($rabi); $i++) { ?>
								<span style="cursor: pointer;" class="dropdown-item" onclick="document.getElementById('id_task_modal_inp').value='<?php echo $rabi[$i]["id"]; ?>';"><?php echo $rabi[$i]["login"]; ?></span>
							<?php } ?>
					  	</div>
					</div>
					<h5 class="mt-3">Установить крайний срок</h5>
					<div class="input-group mb-3">
						<input name="deadline" type="date" class="form-control" style="background-color: #E8F1F1; border-radius: 15px;" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" placeholder="Deadline">
					</div>
					<h5>Количество XP</h5>
					<div class="input-group mb-3">
						<input name="exp" type="text" class="form-control" style="background-color: #E8F1F1; border-radius: 15px;" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" placeholder="XP">
					</div>

					<input type="hidden" name="parent_id" value="">
				</div>
				<div class="modal-footer">
					<input type="submit" name="" value="Добавить" class="btn btn-primary" style="background-color: #e8f1f1; border-color: #e8f1f1; color: black;">
				</div>
			</form>
		</div>
	</div>
</div>
<!-- modal окно для отправки задач --> 
<div class="modal fade" id="redTaskModal" tabindex="-1" aria-labelledby="redTaskModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-lg">
		<div class="modal-content" style="border-radius: 15px;">
			<div class="modal-header">
				<h5 class="modal-title title_modalTask" id="redTaskModalLabel">Название задачи (</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<input type="hidden" name="worker_id" id="id_task_modal_inp">
				<h5 class="status_modalTask">Статус: </h5>
				<div class="row mt-3 mb-3">
					<div class="col-6">
						<h5>Дата создания задачи</h5>
						<div class="input-group">
							<input disabled="true" name="" type="date" class="form-control date_modalTask" style="background-color: #E8F1F1; border-radius: 15px;" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" placeholder="">
						</div>
					</div>
					<div class="col-6">
						<h5>Крайний срок</h5>
						<div class="input-group">
							<input disabled="true" name="deadline" type="date" class="form-control deadline_modalTask" style="background-color: #E8F1F1; border-radius: 15px;" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" placeholder="">
						</div>
						<div class="change_deadline">
							<h5 class="mt-2">Изменить крайний срок</h5>
							<form method="post" action="upd.php?op=4">
								<div class="input-group">
									<input name="deadline" type="date" class="form-control deadline_modalTask" style="background-color: #E8F1F1; border-radius: 15px;" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" placeholder="Поставьте новый deadline">
								</div>
								<input type="hidden" name="id" class="task_id_modalTask">
								<input type="hidden" name="user_id" class="user_id_modalTask">
								<input type="submit" value="Изменить" class="btn btn-primary mt-2" style="background-color: #e8f1f1; border-color: #e8f1f1; color: black;">
							</form>
						</div>
					</div>
				</div>
				<h5>Количество XP</h5>
				<div class="input-group mb-3">
					<input disabled="true" name="exp" type="text" class="form-control xp_modalTask" style="background-color: #E8F1F1; border-radius: 15px;" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" placeholder="XP">
				</div>
				

			</div>
			<form method="post" action="upd.php?op=0">
				<div class="modal-footer">
					<input type="hidden" name="id" class="task_id_modalTask2">
					<input type="hidden" name="user_id" class="user_id_modalTask2">
					<input type="hidden" name="creator_id" class="creator_id_modalTask">
					<div class="input-group mb-3">
						<input name="description" type="text" class="form-control description_modalTask" style="background-color: #E8F1F1; border-radius: 15px;" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" placeholder="Ссылка на работу и комментарии">
					</div>
					<input type="submit" name="" value="Отправить на проверку" class="btn btn-outline-success proverka_btn_modalTask">
				</div>
			</form>
		</div>
	</div>
</div>
<!-- modal окно для проверки выполнения задач --> 
<div class="modal fade" id="revTaskModal" tabindex="-1" aria-labelledby="revTaskModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-lg">
		<div class="modal-content" style="border-radius: 15px;">
			<div class="modal-header">
				<h5 class="modal-title title_revmodalTask" id="revTaskModalLabel">Название задачи (</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<input type="hidden" name="worker_id" id="id_task_modal_inp" class="worker_id_revmodalTask">
				<div class="row mt-3 mb-3">
					<div class="col-6">
						<h5>Дата создания задачи</h5>
						<div class="input-group">
							<input disabled="true" name="" type="date" class="form-control date_revmodalTask" style="background-color: #E8F1F1; border-radius: 15px;" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" placeholder="">
						</div>
					</div>
					<div class="col-6">
						<h5>Крайний срок</h5>
						<div class="input-group">
							<input disabled="true" name="deadline" type="date" class="form-control deadline_revmodalTask" style="background-color: #E8F1F1; border-radius: 15px;" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" placeholder="">
						</div>
					</div>
				</div>
				<h5>Количество XP</h5>
				<div class="input-group mb-3">
					<input disabled="true" name="exp" type="text" class="form-control xp_revmodalTask" style="background-color: #E8F1F1; border-radius: 15px;" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" placeholder="XP">
				</div>
				<h5>Описание от работника</h5>
				<div class="input-group mb-3">
					<input disabled="true" type="text" class="form-control description_old_revmodalTask" style="background-color: #E8F1F1; border-radius: 15px;" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" placeholder="Нет описания, попросите подробностей у работника">
				</div>

			</div>
			<div class="modal-footer">
				<form method="get" action="upd.php?op=2" id="form4" class="col-12">
					<input type="hidden" name="id" class="task_id_revmodalTask">
					<input type="hidden" name="user_id" class="user_id_revmodalTask">
					<div class=" col-12 input-group mb-3">
						<input name="description" type="text" class="form-control description_new_revmodalTask" style="background-color: #E8F1F1; border-radius: 15px;" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" placeholder="Добавьте комментарий если отклоните работу">
					</div>
				</form>
				<div class="d-flex">
					<input type="submit" name="" value="Отклонить работу" class="btn btn-outline-danger" form="form4" style="margin-right: 460px">
					<form method="post" action="upd.php?op=1" id="form5">
						<input type="hidden" name="exp" class="xp_revmodalTask2">
						<input type="hidden" name="id" class="task_id_revmodalTask2">
						<input type="hidden" name="user_id" class="user_id_revmodalTask2">
						<input type="submit" name="" value="Принять работу" class="btn btn-outline-success">
					</form> 
				</div>
			</div>
		</div>
	</div>
</div>

	<div class="row mt-3 ml-5 col-12">
		<h1 class=" col-4 ">LOGO <br> OOO "Магазин Одежды"</h1>
		<div class="col-2"></div>
		<div class="col-1">
			<button type="button" class="btn  font-weight-bold mt-4 " style="height: 50px; width: 350px; background-color: #E8F1F1;">Запустить таймер</button>
		</div>
		<div class="col-2 " style="margin-left:450px">
			<div class="row">
				<div>
					<h2 style=""><?php echo $user["login"]; ?></h2>
					<div class="row ml-2">
						<span class="text-light font-weight-bold rounded-lg px-2 py-1" style="background-color:#38CEC5;">Бухгалтер</span>
						<span class="text-light font-weight-bold rounded-lg px-2 py-1 ml-2" style="background-color:#38CEC5;">Бухгалтер</span>
					</div>
				</div>
				<div class="ml-4">
					<img src="avatar.svg" alt="" style="width: 120%;">
				</div>
			</div>
		   
			
		   
		</div>

	</div>
   
	
	<!-- Tasks -->
  
	<div class="row">
		<div class=" col-1 rounded-left rounded-bottom" style="height: 1000px; border-radius: 30px; background-color: #38CEC5; padding: 30px;">
			<a href="" class="text-center">
				<img src="Home.svg" class="mt-5 text-center mx-auto w-100" alt="" style="">
				<h5 class="text-light mt-2 ">Главная</h5>
			</a>

			<div href="" class="text-center notifications_label" >
				<img src="Settings.svg" class="mt-5 text-center w-100" alt="" style="">
				<h4 style="color: red; display: none;" class="new_notifications">NEW</h4>
				<h5 class="text-light mt-2">Уведомления</h5>
			</div>

			<a href="#" class="text-center">
				<img src="Profile.svg" class="mt-5 text-center w-100" alt="" style="height: 10%;">
				<h5 class="text-light mt-2">Профиль</h5>
			</a>

			<a href="#" class="text-center" >
				<img src="CogOutline.svg" class="mt-5 text-center w-100" alt="" style="height: 10%;">
				<h5 class="text-light mt-2">Настройки</h5>
			</a>
		</div>
		  <!-- Личные Задачи -->
		<div class="col-3  mt-5 tasks_main_div" style="padding: 0 20px 0 20px;">
			<div style="background-color: #E8F1F1; border-radius: 30px;">
				<div class="text-center border-bottom pt-4" style="height: 90px;  border-bottom-color: #38CEC5; width: 100%;">
					<h3 class="">Личные задачи</h3>
				</div>
				
				<div class=" border-top border-bottom px-3" style="min-height: 500px;">
					<?php
					$tasks_query = mysqli_query($connect,"SELECT * FROM tasks_vkhack20 WHERE parent_id!='' AND worker_id='".$_GET['user_id']."'");
					$tasks_light = [];
					for ($i=0; $i<$tasks_query->num_rows; $i++) { 
						$tasks_light[$i] = $tasks_query->fetch_assoc();?>
						<div class="mt-3 mb-3 text-left note_div py-2 px-3" style="border-radius: 8px; background-color:white; min-height: 50px; cursor: pointer;" data-target="#redNoteModal" data-toggle="modal">
							<p class="" style="font-size: 15px;"><?php echo $tasks_light[$i]["title"]; ?></p>
						</div>	
					<?php } ?>
				</div>
				<div class="border-top text-center pt-2 pb-3" style="font-size: 23px; cursor: pointer" data-target="#addNoteModal" data-toggle="modal">
					<span style="color: #38CEC5; font-size: 35px;">+</span> Добавить заметку
				</div>
			</div>
		</div>
	   <!-- Профильные задачи -->
	   <div class="col-3  mt-5 tasks_main_div2" style="height: 800px; padding: 0 20px 0 20px;">
	   	<div style="background-color: #E8F1F1; border-radius: 30px; ">
			<div class="text-center border-bottom pt-4" style="height: 90px;  border-bottom-color: #38CEC5; width: 100%;">
				<h3 class="" style="height: 70px;">Профильные задачи</h3>
			</div>
			
			<div class=" border-top border-bottom px-3" style="min-height: 500px;">
				<?php for ($i=0; $i < count($tasks_main); $i++) { ?>
					<div class="mt-3 mb-3 text-left task_div" style="border-radius: 8px; background-color:white; cursor: pointer;" data-target="#redTaskModal" data-toggle="modal">
						<div class="col-12 row mr-0 pr-0 ml-0" style="">
							<div class="col-9 border-right w-100 py-2 px-0" style="font-size: 15px; "><?php echo $tasks_main[$i]["title"]; ?>
							</div>
							<div class="col-3 border-left px-4 text-center"style="border-radius: 0 10px 10px 0; min-height: 50px;<?php echo $dict1[$tasks_main[$i]['status']]; ?>">
								<h5 class="mb-0"><?php echo $tasks_main[$i]["exp"]; ?> XP</h5>
							</div>
						</div>
					</div>				
				<?php } ?>
			</div>
			<div class="border-top text-center pt-2 pb-3" style="font-size: 23px; cursor: pointer" data-target="#addTaskModal" data-toggle="modal">
				<span style="color: #38CEC5; font-size: 35px;">+</span> Добавить задачу
			</div>
		</div>
	</div>
	<!-- notifications -->
	<div class="col-6 notifications_main_div mt-5" style=" display: none; padding: 0 20px 0 20px;">
		<div class="p-3" style="background-color: #E8F1F1; border-radius: 30px;  min-height: 700px;">
			<h3 class="mb-3">Уведомления</h3>
			<?php for ($i=0; $i < count($notifications); $i++) { ?>
				<div class="p-3 mt-3 notifications_div" style="background-color: #fff; border-radius: 30px; cursor: pointer;" data-target="#revTaskModal" data-toggle="modal">
					<h5><?php echo $notifications[$i]["title"]; ?></h5>
					<p><?php echo $notifications[$i]["description"]; ?></p>
				</div>
			<?php } ?>
		</div>
	</div>
	<!-- posts -->
		<div class="col-3 px-3 mt-5">
			<div class="p-4" style="border-radius: 30px; background-color: #E8F1F1;">
				<p class="text-left" style="width: 60%;">Lorem ipsum, dolor sit amet consectetur adipisicing elit. Magni molestiae numquam velit ducimus voluptates ut tempora officiis debitis minus unde illo optio, eligendi quibusdam repellat corporis pariatur molestias veniam rem.</p>
				<p class="text-right"> - Lorem Ipsum</p>    
			</div>
			<div class="mt-3 p-4" style="border-radius: 30px; background-color: #E8F1F1;">
				<p class="text-left" style="width: 60%;">Lorem ipsum, dolor sit amet consectetur adipisicing elit. Magni molestiae numquam velit ducimus voluptates ut tempora officiis debitis minus unde illo optio, eligendi quibusdam repellat corporis pariatur molestias veniam rem.</p>
				<p class="text-right"> - Lorem Ipsum</p>    
			</div>
			<div class="mt-3 p-4" style="height: 250px; border-radius: 30px; background-color: #E8F1F1;">
				   
			</div>
		</div>
		<div>   
		</div>

	<!-- user -->
	<div class="col-2">
		
		<div class="" style="">
			<h4>Выполнено:</h4>
			<span class="rounded-pill"style="background-color: #38CEC5;"></span>
			<progress min="0" max="100" value="25" class="font-weight-bold text-center"></progress>
		</div>
		<!-- другие работники -->

		<div class="col-12 rounded-right rounded-bottom text-light" style="height: 925px; border-radius: 30px; background-color: #38CEC5;">
			<h6 class="text-center py-3 border-bottom mb-3">Другие работники</h6>
		   
			<!-- users -->
			<div class="ml-2">
				<div class="row">
					<img src="icon.svg" alt="" style="width: 18%;">
					<h4 class="ml-2 mt-2">admin <span style="color: orange;">★</span> 
					   <br> <span class="rounded-pill bg-light" style="font-size: 10px;"></span>
					</h4>
				</div>
			</div>

			<div class="ml-2 mt-2">
				<div class="row">
					<img src="icon.svg" alt="" style="width: 18%;">
					<h4 class="ml-2 mt-2">user1 <span style="color: orange;"></span> 
					   <br> <span class="rounded-pill bg-light" style="font-size: 10px;"></span>
					</h4>
				</div>
			</div>

		</div>
	</div>





	</div>


	<!-- Optional JavaScript; choose one of the two! -->

	<!-- Option 1: jQuery and Bootstrap Bundle (includes Popper) -->
	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
	<script src="jquery.min.js"></script>
	<script src="plugin.js"></script>
	<script src="evo-calendar.js"></script>
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" 
		integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" 
		crossorigin="anonymous">
	</script>
	<script src="src/mini-event-calendar.min.js"></script>
	<script src="/path/to/cdn/jquery.slim.min.js"></script>
	<script src="/path/to/cdn/bootstrap.min.js"></script>
	<script src="/path/to/cdn/moment.min.js"></script>

	<script>
	let active_i

	let note_divs = document.querySelectorAll('.note_div');
	let title_modalNote = document.querySelector('.title_modalNote');
	let description_modalNote = document.querySelector('.description_modalNote');
	let deadline_modalNote = document.querySelector('.deadline_modalNote');
	let worker_id_modalNote = document.querySelector('.worker_id_modalNote');
	let note_id_modalNote = document.querySelector('.note_id_modalNote');
	let note_id_modalNote2 = document.querySelector('.note_id_modalNote2');
	let user_id_modalNote = document.querySelector('.user_id_modalNote');
	let user_id_modalNote2 = document.querySelector('.user_id_modalNote2');
	let notes = []
	<?php for ($i=0; $i < count($tasks_light); $i++) { ?>
		notes.push({
			"title": '<?php echo $tasks_light[$i]["title"]; ?>',
			"description": '<?php echo $tasks_light[$i]["description"]; ?>',
			"deadline": '<?php echo $tasks_light[$i]["deadline"]; ?>',
			"worker_id": '<?php echo $tasks_light[$i]["worker_id"]; ?>',
			"id": '<?php echo $tasks_light[$i]["id"]; ?>'
		})
	<?php } ?>
	for (let i = 0; i < notes.length; i++) {
		note_divs[i].onclick = function(){
			active_i = i
			title_modalNote.innerHTML = notes[i]['title'];
			description_modalNote.value = notes[i]['description'];
			deadline_modalNote.value = notes[i]['deadline'];
			worker_id_modalNote.value = notes[i]['worker_id'];
			note_id_modalNote.value = notes[i]['id'];
			note_id_modalNote2.value = notes[i]['id'];
			user_id_modalNote.value = <?php echo $user['id'].';'; ?>
			user_id_modalNote2.value = <?php echo $user['id']; ?>
		}
	}

	let task_divs = document.querySelectorAll('.task_div');
	let title_modalTask = document.querySelector('.title_modalTask');
	let status_modalTask = document.querySelector('.status_modalTask');
	let description_modalTask = document.querySelector('.description_modalTask');
	let date_modalTask = document.querySelector('.date_modalTask');
	let deadline_modalTask = document.querySelector('.deadline_modalTask');
	let task_id_modalTask = document.querySelector('.task_id_modalTask');
	let task_id_modalTask2 = document.querySelector('.task_id_modalTask2');
	let xp_modalTask = document.querySelector('.xp_modalTask');
	let user_id_modalTask = document.querySelector('.user_id_modalTask');
	let user_id_modalTask2 = document.querySelector('.user_id_modalTask2');
	let proverka_btn_modalTask = document.querySelector('.proverka_btn_modalTask');
	let creator_id_modalTask = document.querySelector('.creator_id_modalTask');
	let change_deadline = document.querySelector('.change_deadline');
	let status_of_task = ["не выполнено", "выполнено", "отправлено на проверку", "просрочено (баллов за выполнение не начислится)"];
	let tasks = []
	<?php for ($i=0; $i < count($tasks_main); $i++) { ?>
		tasks.push({
			"title": '<?php echo $tasks_main[$i]["title"]; ?>',
			"description": '<?php echo $tasks_main[$i]["description"]; ?>',
			"date": '<?php echo $tasks_main[$i]["date"]; ?>',
			"deadline": '<?php echo $tasks_main[$i]["deadline"]; ?>',
			"id": '<?php echo $tasks_main[$i]["id"]; ?>',
			"status": '<?php echo $tasks_main[$i]["status"]; ?>',
			"exp": '<?php echo $tasks_main[$i]["exp"]; ?>',
			"admin_for": <?php if (is_admin_for($user["id"], $user["admins_id"])) {echo 'true';} else {echo 'false';} ?>,
			"creator_name": '<?php echo mysqli_query($connect,"SELECT * FROM users_vkhack20 WHERE id='".$tasks_main[$i]["creator_id"]."'")->fetch_assoc()["login"]; ?>',
			"creator_id": '<?php echo $tasks_main[$i]["creator_id"]; ?>'
		})
	<?php } ?>
	for (let i = 0; i < tasks.length; i++) {
		task_divs[i].onclick = function(){
			active_i = i
			title_modalTask.innerHTML = tasks[i]['title']+' (от: '+tasks[i]['creator_name']+')';
			status_modalTask.innerHTML = 'Статус: '+status_of_task[tasks[i]['status']];
			//description_modalTask.value = tasks[i]['description'];
			date_modalTask.value = tasks[i]['date'];
			deadline_modalTask.value = tasks[i]['deadline'];
			task_id_modalTask.value = tasks[i]['id'];
			xp_modalTask.value = tasks[i]['exp'];
			user_id_modalTask.value = <?php echo $user['id'].';'; ?>
			user_id_modalTask2.value = <?php echo $user['id'].';'; ?>
			task_id_modalTask2.value = tasks[i]['id'];
			creator_id_modalTask.value = tasks[i]['creator_id'];
			if (tasks[i]['admin_for']) {
				change_deadline.style.display = 'block'
			} else {
				change_deadline.style.display = 'none'
			}
			if (tasks[i]['status']==2 || tasks[i]['status']==1) {
				description_modalTask.style.display = 'none';
				proverka_btn_modalTask.style.display = 'none';
			} else {
				description_modalTask.style.display = 'block';
				proverka_btn_modalTask.style.display = 'inline-block';
			}
		}
	}

	<?php if ($notifications[0]["view"]==0) { ?>
		let new_notifications = document.querySelector(".new_notifications");
		new_notifications.style.display = "block";
	<?php } ?>
	let notifications_main_div = document.querySelector(".notifications_main_div");
	let tasks_main_div = document.querySelector(".tasks_main_div");
	let tasks_main_div2 = document.querySelector(".tasks_main_div2");
	let notifications_label = document.querySelector(".notifications_label");
	let notifications_disabled = true;
	notifications_label.onclick = function(){
		if (notifications_disabled) {
			new_notifications.style.display = 'none';
			tasks_main_div.style.display = 'none';
			tasks_main_div2.style.display = 'none';
			notifications_main_div.style.display = 'block';
			notifications_disabled = !notifications_disabled
		} else {
			notifications_main_div.style.display = 'none';
			tasks_main_div.style.display = 'block';
			tasks_main_div2.style.display = 'block';
			notifications_disabled = !notifications_disabled
		}
	}

	let notifications_divs = document.querySelectorAll('.notifications_div');
	let title_revmodalTask = document.querySelector('.title_revmodalTask');
	let worker_id_revmodalTask = document.querySelector('.worker_id_revmodalTask');
	let date_revmodalTask = document.querySelector('.date_revmodalTask');
	let deadline_revmodalTask = document.querySelector('.deadline_revmodalTask');
	let xp_revmodalTask = document.querySelector('.xp_revmodalTask');
	let description_old_revmodalTask = document.querySelector('.description_old_revmodalTask');
	let xp_revmodalTask2 = document.querySelector('.xp_revmodalTask2');
	let task_id_revmodalTask = document.querySelector('.task_id_revmodalTask');
	let task_id_revmodalTask2 = document.querySelector('.task_id_revmodalTask2');
	let user_id_revmodalTask = document.querySelector('.user_id_revmodalTask');
	let user_id_revmodalTask2 = document.querySelector('.user_id_revmodalTask2');
	let notifications = []
	<?php for ($i=0; $i < count($notifications); $i++) { ?>
		notifications.push({
			"title": '<?php echo mysqli_query($connect,"SELECT * FROM tasks_vkhack20 WHERE id='".$notifications[$i]["task_id"]."'")->fetch_assoc()["title"]; ?>',
			"description_old": '<?php echo mysqli_query($connect,"SELECT * FROM tasks_vkhack20 WHERE id='".$notifications[$i]["task_id"]."'")->fetch_assoc()["description"]; ?>',
			"date": '<?php echo mysqli_query($connect,"SELECT * FROM tasks_vkhack20 WHERE id='".$notifications[$i]["task_id"]."'")->fetch_assoc()["date"]; ?>',
			"deadline": '<?php echo mysqli_query($connect,"SELECT * FROM tasks_vkhack20 WHERE id='".$notifications[$i]["task_id"]."'")->fetch_assoc()["deadline"]; ?>',
			"xp": '<?php echo mysqli_query($connect,"SELECT * FROM tasks_vkhack20 WHERE id='".$notifications[$i]["task_id"]."'")->fetch_assoc()["exp"]; ?>',
			"worker_id": '<?php echo mysqli_query($connect,"SELECT * FROM tasks_vkhack20 WHERE id='".$notifications[$i]["task_id"]."'")->fetch_assoc()["worker_id"]; ?>',
			"notification_id": '<?php echo $notifications[$i]["id"]; ?>',
			"task_id": '<?php echo $notifications[$i]["task_id"]; ?>',
		})
	<?php } ?>
	for (let i = 0; i < notifications.length; i++) {
		notifications_divs[i].onclick = function(){
			active_i = i
			title_revmodalTask.innerHTML = notifications[i]['title'];
			worker_id_revmodalTask.value = notifications[i]['worker_id'];
			date_revmodalTask.value = notifications[i]['date'];
			deadline_revmodalTask.value = notifications[i]['deadline'];
			//task_id_modalTask.value = tasks[i]['id'];
			xp_revmodalTask.value = notifications[i]['xp'];
			xp_revmodalTask2.value = notifications[i]['xp'];
			user_id_revmodalTask.value = <?php echo $user['id'].';'; ?>
			user_id_revmodalTask2.value = <?php echo $user['id'].';'; ?>
			task_id_revmodalTask.value = notifications[i]['task_id'];
			task_id_revmodalTask2.value = notifications[i]['task_id'];
		}
	}
	</script>
	<!-- Option 2: jQuery, Popper.js, and Bootstrap JS
	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
	-->
  </body>
</html>