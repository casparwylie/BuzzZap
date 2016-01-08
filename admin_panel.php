<?php
if($check_valid!="true"){
	header("Location: index.php?page=home");
	exit();
}
if(loggedin_as_admin()){
	if(isset($_GET['sp'])){
		?>
		<script>
			$(document).ready(function(){
				$(".admin-sub-page").hide();
				$("#admin-page-<?php echo $_GET['sp']; ?>").show();
			});
		</script>
		<?php
	}
?>
	<div id = "admin-title">BuzzZap Admin Panel</div>
	<br><br><div id = "admin-m-c" style = "font-size:110%;"></div><br>
	 
	<div id="side-bar-menu-admin">
		<a href = "index.php?page=logout" style = 'color:#71C671;'>Logout ></a><br><br>
		<a href = "index.php?page=home&sp=0" style = 'color:#71C671;'>Home ></a><br><br>
		<a href = "index.php?page=home&sp=1" style = 'color:#71C671;'>User Actions ></a><br>
		<a href = "index.php?page=home&sp=2" style = 'color:#71C671;'>User Banned List ></a><br>
		<a href = "index.php?page=home&sp=3" style = 'color:#71C671;'>User Reporting ></a><br><br>
		<a href = "index.php?page=home&sp=4" style = 'color:#71C671;'>Site activation ></a><br>
		<a href = "index.php?page=home&sp=5" style = 'color:#71C671;'>Direct MYSQL ></a><br>
		<a href = "index.php?page=home&sp=6" style = 'color:#71C671;'> BuzzZap News ></a><br>
		<a href = "index.php?page=home&sp=7" style = 'color:#71C671;'>Community payments ></a><br>
		<a href = "index.php?page=home&sp=8" style = 'color:#71C671;'>Static Content ></a><br><br>
		<a href = "index.php?page=home&sp=9" style = 'color:#71C671;'>Use Encryption Method ></a><br>
	
	
	</div>
	<div id = "admin-page-container">
		<div id = "admin-page-0" class = 'admin-sub-page'>
			Welcome to the BuzzZap admin panel, used for controlling and monitoring the site.
		</div>
		<div id = "admin-page-1" class = 'admin-sub-page'>
			<b>User Action</b>
			<br><br>
			 <form action = "" method = "POST">
				Enter the relevant user:<br>
				<input type = "text" name = "username_action" placeholder = "Enter Username..." class = "leader-cp-fields">
				<select class = "leader-cp-fields" name = "user_action">
					<option value = "na">Select Action</option>
					<option value = "del_user">Delete User (deletes the user for good)</option>
					<option value = "ban_user">Ban/suspend User (make account unaccessable)</option>
					<option value = "unban_user">Unban User (make account accessable)</option>
					<option value = "edit_user">Edit User (edit users infomation)</option>
					<option value = "reset_user">Reset User (deletes posts, reputation, votes, etc)</option>
					<option value = "cm_user">Turn on close moderation (every post made by this user will have to be approved by a leader before it is visible)</option>
					<option value = "tcm_user">Turn off close moderation (the user can post freely, without the need of a leader approving it)</option>
				</select>
				<input type = "submit" value = "Submit" class = "leader-cp-submit">	
			 </form>
 
			 <?php
			 if(isset($_POST['username_action'],$_POST['user_action'])){
				if($_POST['user_action']!=="na"){
					$username = htmlentities($_POST['username_action']);
					$action = htmlentities($_POST['user_action']);
					$valid_user_check = $db->query("SELECT user_username FROM users WHERE user_username = ".$db->quote($username))->fetchColumn();
					if(!empty($valid_user_check)){
						if($action=="edit_user"){
							// real column => display name
							$allowed_columns = array("user_username"=>"Username", "user_password"=>"Password", "user_firstname"=>"Firstname",
												"user_lastname"=>"Lastname", "user_email"=>"Email", "user_rep"=>"Reputation", "user_com"=>"Community", "user_rank"=>"Rank",
												"user_code"=>"User Code");
							$options = "<option val = ''>-----</option>";
							foreach($allowed_columns as $column=>$display_name){
								$options .= "<option value = '".$column."'>".$display_name."</option>";
							}
							?>
								<div id = 'user_edit_form' style = "position:absolute;background-color:grey;padding:20px;margin-top:-100px;margin-left:540px;">
									<span style = 'color:black;'>
										<span id = 'close_edit_form' style = 'float:right;'>x</span><br>
										<center><b><u>Edit User</b></u></center><br><br>
										<form action = '' method = 'POST'>
											Change <?php echo $username; ?>'s...<br><br>
											 <select name = 'edit_column' id ='edit_column' class = 'leader-cp-fields' style = 'background:white;'>
											<?php echo $options; ?>
											</select><br><br>
											<input type = 'text' name = 'new_val' placeholder = 'New Value...' class = 'leader-cp-fields' id = 'edit_new_value'>
											<input type = 'hidden' value = '<?php echo $username; ?>' name = 'username'><br><br>
											<input type = 'submit' class = 'leader-cp-submit'>
							
										</form>
									</span>
								</div>	
									<script>
										$(document).ready(function(){
											$('#close_edit_form').click(function(){
												$('#user_edit_form').fadeOut();	
											});
											$('#edit_column').change(function(){
												if($(this).val()!='-----'){
													var dis_name = $(this).val().substring(5);
												}else{
													var dis_name = '...';
												}	
												if($(this).val()=='user_password'){
													$('#edit_new_value').attr('type', 'password');
												}else{
													$('#edit_new_value').attr('type', 'text');
												}
												$('#edit_new_value').attr('placeholder', 'New '+dis_name);
											});
										});
									</script>
							<?php
				
					
						}else{
							$user_id = $db->query("SELECT user_id FROM users WHERE user_username = '$username'")->fetchColumn();
							action_user($user_id, $action);
							header("Location: index.php?page=home&m=11Successfully altered user!");
						}
					}else{
						header("Location: index.php?page=home&m=01The user entered is invalid.");
					}
				}			
			}
			if(isset($_POST['edit_column'], $_POST['new_val'], $_POST['username'])){
				$username = htmlentities($_POST['username']);
				$euser_id = $db->query("SELECT user_id FROM users WHERE user_username = ".$db->quote($username))->fetchColumn();
				$new_val = htmlentities($_POST['new_val']);
				$field = htmlentities($_POST['edit_column']);
				if((($field=="user_email")&&(filter_var($new_val, FILTER_VALIDATE_EMAIL)))||($field!="user_email")){
					if($field == "user_password"){
						$new_val = encrypt($new_val);
					}
					if(update_user_field($euser_id, $new_val, $field)){
						echo "d";
						header("Location: index.php?page=home&m=11Successfully edited user.");
					}else{
						header("Location: index.php?page=home&m=01Unknown error.");
					}
					
				}else{
					header("Location: index.php?page=home&m=01Invalid email.");
				}
		
			}
	
					if(isset($_GET['m'])){
						?>
						<script>
						$(document).ready(function(){
							$("#admin-m-c").fadeIn();
							$("#admin-m-c").html("<?php echo substr($_GET['m'], 2);?>");
							if("<?php echo substr($_GET['m'], 0, 1);?>"=="0"){
								$("#admin-m-c").css('color', 'red');
							}else{
								$("#admin-m-c").css('color', '#71C671');
							}
							var sp = "<?php echo substr($_GET['m'], 1, 1);?>";
							setTimeout(function(){window.location="index.php?page=home&sp="+sp;}, 2000);
						});
						</script>
				<?php
				 }
				 ?><br><br>
				 <b>View User Field</b><br>
				 <i>*NOTE: fields will be shown as exact value from database</i>
				 <br>
				 <form action = "" method = "POST">
				 	<input type = "text" placeholder = "Enter username..." class = "leader-cp-fields" name = "view_user_info">
				 	<input type = "submit" class = "leader-cp-submit">
				 	
				 </form>
				 <?php
				 	if(isset($_POST['view_user_info'])){
				 		$username = htmlentities($_POST['view_user_info']);
				 		if($db->query("SELECT user_username FROM users WHERE user_username = ".$db->quote($username))->fetchColumn()!=""){
				 			$columns = array("user_id", "user_username", "user_password", "user_firstname", "user_lastname", "user_group", "user_com",
				 			"user_rank", "user_email", "user_rep", "user_code", "close_mod");
				 			
				 			foreach($columns as $column){
				 				echo $column.": ".get_user_field($db->query("SELECT user_id FROM users WHERE user_username = ".$db->quote($username))->fetchColumn(), $column)."<br>";
				 			} 
				 		}else{
				 			header("Location: index.php?page=home&m=01Invalid user.");
				 		}
				 	}
				 ?>
		</div>	
		<div id = "admin-page-2" class = 'admin-sub-page'>
		<b>All Banned Users</b><br><br>
		<hr size = '1'>
			<?php
				
				$banned_users = $db->prepare("SELECT user_username FROM users WHERE user_rank = 0");
				$banned_users->execute(array());
				if($banned_users->rowCount()!==0){
					while($row = $banned_users->fetch(PDO::FETCH_ASSOC)){
						echo $row['user_username']." - <a href = 'index.php?page=home&sp=2&ub=".$row['user_username']."'>Unban</a><hr size = '1'><br>";	
					}
				}else{
					echo "There are no banned users. ";	
				}
				
				if(isset($_GET['ub'])){
					$username = htmlentities($_GET['ub']);
					if(action_user($db->query("SELECT user_id FROM users WHERE user_username=".$db->quote($username))->fetchColumn(), "unban_user")){
						header("Location: index.php?page=home&sp=2&m=12Unbanned successfully.");
					}
				}
			?>	
		</div>
		<div id = "admin-page-3" class = 'admin-sub-page'>
			Welcome to the BuzzZap admin panel, used for controlling and monitoring the site.
		</div>
		<div id = "admin-page-4" class = 'admin-sub-page'>
			<b>Disable/Enable Specific Site Features</b><br><br>
				<form action = "" method = "POST">
					<?php
						$action_dis = array();
						$action_dis[0]= (get_feature_status("site")=="1")? "Enable":"Disable";
						$action_dis[1]= (get_feature_status("login")=="1")? "Enable":"Disable";
						$action_dis[2]= (get_feature_status("new_coms")=="1")? "Enable":"Disable";
						$action_dis[3]= (get_feature_status("new_users")=="1")? "Enable":"Disable";
					?>
					<select class = "leader-cp-fields" name = "sf_control">
							<option value = "na">Select feature</option>
							<option value = "site"><?php echo $action_dis[0]; ?> site</option>
							<option value = "login"><?php echo $action_dis[1]; ?> login (this will not apply to admins)</option>
							<option value = "new_coms"><?php echo $action_dis[2]; ?> new communities</option>
							<option value = "new_users"><?php echo $action_dis[3]; ?> new users</option>
					</select>
					<input type = "submit" values = "Submit" class = "leader-cp-submit">
				</form>
				<hr size = "1">
				<b>Feature disabled bypass code</b><br>
				<i>A code used to bypass any disabled feature.</i><br><br>
				
				<?php
				$get_code = $db->query("SELECT pass FROM feature_activation WHERE feature='site'")->fetchColumn();
				$counter = 1;
				$dis_pass = "";
				while($counter <= strlen($get_code)){
					$dis_pass .="*";
					$counter++;
				}
				
				echo $dis_pass." - <a href = 'index.php?page=home&sp=4&cdp=true'>Change</a>";
				
				if(isset($_GET['cdp'])){
					?>
					<form method = "POST">
						<input type = "password" name = "cdp" class = "leader-cp-fields">
						<input type = "submit" class = "leader-cp-submit">
					</form>
					<?php
				}
				if(isset($_POST['cdp'])){
					$update = $db->prepare("UPDATE feature_activation SET pass = :cdp");
					$update->execute(array("cdp"=>$_POST['cdp']));
					header("Location: index.php?page=home&sp=4&m=14Successfully updated.");
				}	
				?>
				<hr size = "1">
				<b>Feature disabled message/reason for public</b><br>
				<i>What the public will read when confronted with a disabled feature</i><br>
				<i>*Note: XSS works.</i><br><br>
				<form action = "" method = "POST" style = "font-size:80%;">
				
					Site disabled:<br><textarea style = "resize:none;background-color:grey;border:1px solid black;height:100px;width:600px;" name = "m_site"><?php echo get_disabled_message('site'); ?></textarea><br>
					Login disabled:<br><textarea style = "resize:none;background-color:grey;border:1px solid black;height:100px;width:600px;" name = "m_login"><?php echo get_disabled_message('login'); ?></textarea><br>
					New community disabled:<br><textarea style = "resize:none;background-color:grey;border:1px solid black;height:100px;width:600px;"name = "m_new_coms"><?php echo get_disabled_message('new_coms'); ?></textarea><br>
					New user disabled:<br><textarea style = "resize:none;background-color:grey;border:1px solid black;height:100px;width:600px;" name = "m_new_users"><?php echo get_disabled_message('new_users'); ?></textarea><br>
					<br><br><input type = "submit" class = "leader-cp-submit" value = "Save">
				</form>
			<?php
				if(isset($_POST['m_site'], $_POST['m_login'], $_POST['m_new_coms'], $_POST['m_new_users'])){
					$array = array("site", "login", "new_coms", "new_users");
					foreach($array as $element){
						$update= $db->prepare("UPDATE feature_activation SET message = :message WHERE feature = :feature");
						$update->execute(array("message"=>$_POST['m_'.$element], "feature"=>$element));	
					}
					
					header("Location: index.php?page=home&sp=4&m=14Successfully saved messages.");
				}
				if(isset($_POST['sf_control'])){
					$valid = array("site", "login", "new_coms", "new_users");
					if(in_array($_POST['sf_control'], $valid)){
						$new_act = e_d_feature($_POST['sf_control']);
						if($new_act==1){
							$action_word = "disabled feature.";
						}else{
							$action_word = "enabled feature.";
						}
						header("Location: index.php?page=home&sp=4&m=14Successfully ".$action_word);
					}else{	
						header("Location: index.php?page=home&m=04Unkown Error.");
					}
				}
			?>
		</div>
		<div id = "admin-page-5" class = 'admin-sub-page'>
			<b>RUN DIRECT MYSQL QUERY</b><br><br>
			
			<form action = "" method = "POST">
				<input type = "text" style ="background-color:black;font-size:20px;height:60px;width:1000px;color:green;font-family:courier new;" name = "mysql_query">
				<input type = "submit" value = "Submit Query" class = "leader-cp-submit" style = "width:200px;">
			</form>
			
			<?php
				if(isset($_POST['mysql_query'])){
					$query = $_POST['mysql_query'];
					$run = run_admin_query($query);
					
					if($run==="false"){
						header("Location: index.php?page=home&m=05Unkown Error.");
					}else if($run==="true"){
						header("Location: index.php?page=home&m=15Successfully ran query.");
					}else{
						while($row = $run->fetch(PDO::FETCH_ASSOC)){
							foreach($row as $column=>$value){
								echo $column. ": ".$value. "<br>";
							}
							echo "<br><hr><br>";
						}
					}
				}
			?>
		</div>
		<div id = "admin-page-6" class = 'admin-sub-page'>
			<b>BuzzZap News Manager</b><br><br>
			
			Add/ (edit) new feed:
			<?php
				if(isset($_GET['edit_bn'])){
					$id = htmlentities($_GET['edit_bn']);
					$e_title = $db->query("SELECT title FROM site_news WHERE feed_id=".$db->quote($id))->fetchColumn();
					$e_text = str_replace("<br />", " ",$db->query("SELECT feed_text FROM site_news WHERE feed_id=".$db->quote($id))->fetchColumn());
				}else{
					$e_title ="";
					$e_text="";
				}
			?>
			<form action = "" method = "POST">
				<input type = "text" style ="width: 300px;height: 30px;font-size: 130%;" placeholder = "Title..." name = "bn_title" value = "<?php echo $e_title; ?>"><br>
				<textarea placeholder = "Body..." name = "bn_text" style = "text-align: center;width: 800px;height: 300px;font-size: 130%;resize: none;"><?php echo $e_text; ?></textarea><br>
				<input type = "submit" value = "Post/Update" class = "leader-cp-submit" style = "width:200px;">
			</form>
			<hr size = "1">
			View feeds:<br>
			<?php
				$get = $db->query("SELECT * FROM site_news");
				if($get->rowCount()>0){
					foreach($get as $value){
						echo "<div style = 'background: lightgrey;'>
						<a href = 'index.php?page=home&sp=6&del_bn=".$value['feed_id']."' style = 'color: salmon;'>DELETE</a>|
						<a href = index.php?page=home&sp=6&edit_bn=".$value['feed_id']."' style = 'color: salmon;'>EDIT</a>
							<br><u>".$value['title']."</u>
							-<i>".date("d/M/Y H:i", $value['time'])."</i>
							<br><span style = 'font-size: 80%'>
							".$value['feed_text']."
							</span>
						</div><br>";
					}
				}else{
					echo "<br>There are no feeds";
				}	
				if(isset($_POST['bn_text'], $_POST['bn_title'])){
					if(isset($_GET['edit_bn'])){
						$edit = htmlentities($_GET['edit_bn']);
					}else{
						$edit = "";
					}
					$title = htmlentities($_POST['bn_title']);
					$text = htmlentities($_POST['bn_text']);
					if(!empty($title)&&!empty($text)){
						add_b_news(nl2br($text), $title, $edit);
						header("Location: index.php?page=home&m=16Successful.");
					}else{
						header("Location: index.php?page=home&m=06Error.");
					}
				}
				if(isset($_GET['del_bn'])){
					$id = htmlentities($_GET['del_bn']);
					del_b_news($id);
					header("Location: index.php?page=home&m=16Successfully deleted.");
				}	
			?>
		</div>
		<div id = "admin-page-7" class = 'admin-sub-page'>
			<b>BuzzZap Community activation</b><br><br>
			
			<u>Not paid/disabled</u><br><br>
			<?php
				$get_un = $db->prepare("SELECT com_id FROM com_act WHERE act = 0");
				$get_un->execute();
				if($get_un->rowCount()>0){
				
					while($row=$get_un->fetch(PDO::FETCH_ASSOC)){
				
						$name = $db->query("SELECT com_name FROM communities WHERE com_id = ".$row['com_id'])->fetchColumn();
						$leadername = $db->query("SELECT user_firstname FROM users WHERE user_com = ".$row['com_id']. " AND user_rank = 3")->fetchColumn();
						$leadername = $leadername." ".$db->query("SELECT user_lastname FROM users WHERE user_com = ".$row['com_id']. " AND user_rank = 3")->fetchColumn();
						$leaderemail = $db->query("SELECT user_email FROM users WHERE user_com = ".$row['com_id']. " AND user_rank = 3")->fetchColumn();
						$default_msg = "Dear ".$leadername.", \r\n \r\nBuzzZap is still waiting for your payment before your community can be accessed. \r\nTo pay now, please visit: \r\n\r\nhttp://www.buzzzap.com?page=home&go_to=4&pay=t \r\n\r\nOnce you have paid, it may take up to 48 hours for your community to be accessible.\r\n \r\n Thank you!";
						echo $name." - ".$leaderemail."-".$leadername."-  <a href = 'index.php?page=home&sp=7&&actc=".$row['com_id']."&lname=".$leadername."&lemail=".$leaderemail."'>Activate</a> or
						email:<form method = 'POST'>
							<textarea name = 'emailc' style = 'height: 150px;width: 400px;'>".$default_msg."</textarea>
							<input type = 'hidden' name = 'com_id' value = '".$row['com_id']."'>
							<input type = 'submit'>
						</form> <br><hr size = '1'>";
					}
				}else{
					echo "No unactivated communities";
				}
				
				if(isset($_GET['actc'], $_GET['lname'], $_GET['lemail'])){
					$com_id = htmlentities($_GET['actc']);
					$leadername = htmlentities($_GET['lname']);
					$email = htmlentities($_GET['lemail']);
					if($db->query("UPDATE com_act SET act = 1 WHERE com_id = ".$db->quote($com_id))){
						$headers  = 'MIME-Version: 1.0' . "\r\n";
						$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
						$headers .= "From: Administration@buzzzap.com" . "\r\n";
						$com_name = $db->query("SELECT com_name FROM communities WHERE com_id = ".$db->quote($com_id))->fetchColumn();
						$body = "Dear ".$leadername.", <br> BuzzZap has now recieved your successful payment and your community ".$com_name." has <br> been activated. To login, please visit http://www.buzzzap.com and login with your personal username, <br> password and then your community's passcode. <br><br> If you have any questions please contact us or study the <a href = 'http://buzzzap.com/ext/buzzzap_site_manual.pdf'>site manual</a>. <br><br> Thank you!";
						
						mail($email,"BuzzZap Community Activation",$body,$headers);
						header("Location: index.php?page=home&m=17Successfully activated.");
					}
				}
				if(isset($_POST['emailc'], $_POST['com_id'])){
					$com_id = htmlentities($_POST['com_id']);
					$body = nl2br(htmlentities($_POST['emailc']));
					$email = $db->query("SELECT user_email FROM users WHERE user_com = ".$db->quote($com_id). "AND user_rank = 3")->fetchColumn();
					$headers  = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
					$headers .= "From: Billing@buzzzap.com" . "\r\n";
					mail($email,"BuzzZap payments",$body,$headers);
					header("Location: index.php?page=home&m=17Successfully emailed.");
				}
			?>
			
		</div>
		<div id = "admin-page-8" class = 'admin-sub-page'>
			<b>BuzzZap Site Content</b><br><br>
			<?php
				$get_conts  = $db->query("SELECT * FROM static_content");
				foreach($get_conts as $cont){
					echo $cont['cont_name'].":<br> 
					<form action = '' method = 'POST'>
						<textarea style = 'height: 150px;width: 400px;' name = 'sc_".$cont['cont_name']."'>".$cont['cont']."</textarea>
						<input type = 'submit' value = 'Update'>
					</form>
					";
					
					if(isset($_POST['sc_'.$cont['cont_name']])){
						$cont_txt = $_POST['sc_'.$cont['cont_name']];
						if(!empty($cont_txt)){
							$new_cont = $cont_txt;
							change_static_content($cont['cont_name'], $new_cont);
							header("Location: index.php?page=home&m=18Successfully updated.");
						}else{
							header("Location: index.php?page=home&m=08Error.");
						}
					}
				}
			?>
		
			
		</div>
		<div id = "admin-page-9" class = 'admin-sub-page'>
			<b>Use BuzzZap's Custom Encryption Method</b><br><br>
			<form action = "" method = "POST">
				<input type = "text" style ="width: 500px;height: 30px;font-size: 130%;" placeholder = "String..." name = "en_string"><br>
				<input type = "submit" class = "leader-cp-submit" value = "Encrypt">
			</form>
			<?php
				if(isset($_POST['en_string'])){
					$str = htmlentities($_POST['en_string']);
					echo encrypt($str);
				}
			?>
		
			
		</div>
	</div>	
<?php		
}else{
	header("Location: index.php?page=loggout");
}
?>