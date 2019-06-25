<?php
session_start();
include('initial.php');
include('user.php');
include('share_user.php');
include('expense.php');
include('settle.php');
if(isset($_SESSION["username"])){
	$userLoggedIn = $_SESSION["username"];	
}
else{
	header("Location:register.php");
}
$currentUser = new User($con,$userLoggedIn);
$invalidName = false;
$invalidCost = false;
$friends = array();
$friendsCost = array();
$moneyLent = 0;

if(isset($_POST['addbutton'])){
	$title = $_POST['title'];
	$description = $_POST['description'];
	$cost = $_POST['cost'];
	$shareUser = 1;
	while(isset($_POST[$shareUser]) && isset($_POST['amt'.$shareUser])){
		array_push($friends, $_POST[$shareUser]);
		array_push($friendsCost,$_POST['amt'.$shareUser]);
		$moneyLent += $_POST['amt'.$shareUser];
		$shareUser++;
	}
	$shareCost = new ShareCost($con,$friends,$friendsCost);
	$validateExpense = $shareCost->validateExpense($cost);
	if($validateExpense == -1){
		echo '<script>alert("The total of everyone\'s share is different from the total cost")</script>';
		$invalidCost = true;
	}
	if($validateExpense == 0){
		$invalidCost = true;
	}
	if(($shareCost->validateFriends() == 1 && $shareCost->validateExpense($cost) == 1) || count($friends) == 0){
		$expense = new Expense($con,$userLoggedIn,$title,$description,$cost,"",$friends,$friendsCost);
		$wasSuccessful = $expense->addExpense();
	}
	else if($shareCost->validateFriends() == 0){
		$invalidName = true;
	}
}

if(isset($_POST['quantity'])){
	$title = $_POST['titleDelete'];
	$description = $_POST['descriptionDelete'];
	$cost = $_POST['costDelete'];
	$date = $_POST['timeDelete'];
	$friendsDelete = $_POST['friendsDelete'];
	$amtdelete = $_POST['lentDelete'];
	$deleteExpense = new Expense($con,$userLoggedIn,$title,$description,$cost,$date,$friendsDelete,$amtdelete);
	$wasSuccessful = $deleteExpense->deleteExpense();
}

if(isset($_POST['settleButton'])){
	$payee = $_POST['payee'];
	$payeeAmt = $_POST['payeeAmt'];
	$settle = new Settle($con,$userLoggedIn,$payee,$payeeAmt);
  //add validation

	$settle->settleExpense();


}

if(isset($_POST['logout'])){
	session_destroy();
	header("Location:register.php");
}



function getValue($string){
	if(isset($_POST[$string])){
		echo $_POST[$string];
	}
}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Dashboard:Wallet Manager</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="register.css">
</head>
<body>
	<?php 
	echo "<h1>".$userLoggedIn."'s DASHBOARD</h1><br>";
	?>
	<div class="maincontainer">
		<div class="column">
			<div class="dashboard">
				<div class="input-group-prepend">
					<span  class="input-group-text"><i class="fas fa-plus-square"></i></span><input type="button" id="moreExpense" value="ADD EXPENSE">
				</div>	
				<div class="input-group-prepend"> 
					<span class="input-group-text"><i class="fas fa-list"></i></span><input type = "button" id="viewList" name="viewList" value="ALL EXPENSES">	
				</div>
				<div class="input-group-prepend"> 
					<span class="input-group-text"><i class="fas fa-list"></i></span><input type = "button" id="viewSettle" name="viewSettle" value="SETTLE">	
				</div>
			</div>
			<div class="activity">
				<table>
					<?php 
					$currentUser->viewActivity();
					?>
				</table>
			</div>	
		</div>
		

		<div class="middle">
			<div id="expenses">
				<h5>YOUR EXPENSES:</h5>
				<table class="list">
					<?php
					$currentUser->viewExpense();
					?>
				</table>
			</div>

			<div id="settle" class="hidden">
				<form action="dashboard.php" method="POST">
					<div class="input-group-prepend">
						<span>YOU PAYING </span><input type="text"  style="margin:5px;height:10;" name="payee" placeholder="Payee">
						<input type="number" name="payeeAmt"  style="margin:5px;height:10;width:20;" placeholder="Amount">
						<button type="submit"  style="margin:5px;height:10;" name="settleButton">SETTLE</button>
					</div>
				</form>

			</div>
			<div id="addExpense" class="hidden">
				<form action="dashboard.php" method="POST">
					<div id="friends">
						<button type="button" id="moreUser" style="margin: 5px;">Split the Bill?</button><br>
						<?php if($invalidName){
							echo "Enter valid Usernames<br>";
							echo '<script> 
							var hidden = document.querySelector(".hidden");
							document.querySelector("#addExpense").classList.remove("hidden");
							document.querySelector("#expenses").classList.add("hidden");</script>';
							$invalid = false;               
						}

						if($invalidCost){
							echo "Enter Valid Costs<br>";
							echo '<script> 
							var hidden = document.querySelector(".hidden");
							document.querySelector("#addExpense").classList.remove("hidden");
							document.querySelector("#expenses").classList.add("hidden");</script>';
							$invalidCost = false;   
						}
						?>
					</div>
					<div class="row">
						<div class="input-group-prepend">
							<span  class="input-group-text"><i class="fas fa-heading"></i></span><input type="text" name="title" placeholder="TITLE" required>
						</div>		
					</div>
					<div class="row">
							<textarea cols="26" rows="6" name="description" placeholder="description(optional)"></textarea>
					</div>
					<div class="row">
						<div class="input-group-prepend">
							<span  class="input-group-text"><i class="fas fa-rupee-sign"></i></span><input type="number" name="cost" placeholder="Amount You Paid" required>
						</div>	 
					</div>
					<div class="row">
						<button type="submit" name="addbutton">Add</button>
					</div>
				</form>
			</div>

		</div>

		<div id="balance">
			<form action="dashboard.php" method="POST">
				<input type="submit" name="logout" style="width:230px;margin-bottom: 15px;" value="Logout">	
			</form>
			<?php
			$currentUser->displayBalance();
			?>
		</div>

	</div>


	<script type="text/javascript">
		var expenseList = document.querySelector("#expenses");
		var viewList = document.querySelector("#viewList");
		var moreExpense = document.querySelector("#moreExpense");
		var addExpense = document.querySelector("#addExpense");
		var deleteExpense = document.querySelector("#deleteExpense");
		var friends = document.querySelector("#friends");
		var settle = document.querySelector("#settle");
		var viewSettle = document.querySelector("#viewSettle");
		var hidden = document.querySelector(".hidden");
		var friend = 1;
		var moreUser = document.querySelector("#moreUser");
		viewList.addEventListener("click",function(){
			expenseList.classList.remove("hidden");
			addExpense.classList.add("hidden");
		});
		moreExpense.addEventListener("click",function(){
			addExpense.classList.remove("hidden");
			expenseList.classList.add("hidden");
			settle.classList.add("hidden");
		})
		moreUser.addEventListener("click",function(){
			var input = document.createElement("input");
			var amt = document.createElement("input");
			var friendCost = "amt" + friend;
			input.setAttribute("type","text");
			input.setAttribute("name",friend);
			input.setAttribute("placeholder","Enter Username");
			input.style.marginRight = "10px";
			input.style.marginBottom = "10px";
			amt.setAttribute("type","number");
			amt.setAttribute("name",friendCost);
			amt.setAttribute("placeholder","Their Share");
			amt.style.marginBottom = "10px";
			input.required = true;
			amt.required = true;
			amt.style.width = "90px";
			friends.appendChild(input);
			friends.appendChild(amt);
			var br = document.createElement('br');
			friends.appendChild(br);
			friend++;
		})

		viewSettle.addEventListener("click",function(){
			settle.classList.remove("hidden");
			addExpense.classList.add("hidden");
		})

	</script>

</body>
</html>