<?php
class User {
	private $con;
	private $userLoggedIn;
	private $rowNum;

	function __construct($con,$userLoggedIn){
		$this->con = $con;
		$this->userLoggedIn = $userLoggedIn;
		$this->rowNum = 0;
	}

	function displayBalance(){
		$query = "SELECT * FROM person WHERE username = '$this->userLoggedIn'";
		$checkBalance = mysqli_query($this->con,$query);
		if(mysqli_num_rows($checkBalance) == 1){
			$row = $checkBalance->fetch_assoc();
			$balance = $row['balance'];
			$amountLent = $row['amount_lent'];
            $amountBorrow = $row['amount_borrowed'];
			echo '<div style="text-align:center;background-color:rgba(0,0,0,0.3)">TOTAL AMOUNT SPENT <br><span style="font-size:3em;">Rs '. $balance. '</span></div>';
			echo '<div style="text-align:center;background-color:rgba(0,0,0,0.3)"><br>YOU LENT <br> <span style="font-size:3em;">Rs '. $amountLent . '</span></div>';
			echo '<div style="text-align:center;background-color:rgba(0,0,0,0.3)"><br>YOU BORROWED <br> <span style="font-size:3em;">Rs '. $amountBorrow . '</span></div>';
		}
	}

	public function viewExpense(){
		$query = "SELECT * FROM expense WHERE username = '$this->userLoggedIn' ORDER BY cur_date";
		$view = mysqli_query($this->con,$query);
		if($view){
			if(mysqli_num_rows($view) == 0){
				echo '<div style="background-color:rgba(0,0,0,0.3);text-align:center;font-size:20px;margin-top:20px;">To Add a new expense <br>Click the Add Expense button to your left</div>';
			}
			else{
				while($row = $view->fetch_assoc()){
					$timeStamp = strtotime($row['cur_date']);
					$friendsCost = explode(",",$row['amount_owe']);
					$amount_owe = 0;
					foreach ($friendsCost as $key => $value) {
						$amount_owe += $value;
					}
					echo '<tr><td>'.date("Y",$timeStamp).'<br>'.date("M d",$timeStamp).'</td>
					<td style="text-align:left">'. $row['title'].'<br>'. $row['description'].'</td>
					<td style="text-align:right"><div>You Paid<br><span>'. $row['cost'].'</span></div></td>
					<td style="text-align:right"><div>You Lent<br><span>'. $amount_owe.'</span></div></td> 
					<td>
					<form action="dashboard.php" method="POST">
					<button name = "quantity" type="submit" value="delete" style="width:40px;height:30px"><i class="fas fa-trash-alt" font-size="30px"></i></button>
					<input name="titleDelete" type="hidden" value="'.$row['title'].'">
					<input name="descriptionDelete" type="hidden" value="'.$row['description'].'">
					<input name="costDelete" type="hidden" value="'.$row['cost'].'">
					<input name="lentDelete" type="hidden" value="'.$row['amount_owe'].'">
					<input name="usernameDelete" type="hidden" value="'.$this->userLoggedIn.'">
					<input name="timeDelete" type="hidden" value="'.$row['cur_date'].'">
					<input name="friendsDelete" type="hidden" value="'.$row['friend_name'].'">
					</form></td></tr>';
				}	
			}

		}
	}

	public function viewActivity(){
       $query = "SELECT * FROM activities WHERE username = '$this->userLoggedIn' ORDER BY cur_date";
       $result = mysqli_query($this->con,$query);
       while($row = $result->fetch_assoc()){
       	$timeStamp = strtotime($row['cur_date']);
       	echo '<tr style="border-bottom:1px solid white;margin:1px;border-top:0px;border-right:0px;border-left:0px;"><td style="text-align:left">'.date("Y",$timeStamp).'<br>'.date("M d",$timeStamp).'<br></td><td style="padding-left:2px;text-align:right">'.$row['message'].'</td></tr>';
       }
	}

}

?>