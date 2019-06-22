<?php 

class Expense {
	private $con;
	private $username;
	private $title;
	private $description;
	private $cost; 
	private $date;
	private $friends;
	private $friendsCost;	

	function __construct($con,$userLoggedIn,$title,$description,$cost,$date,$friends = array(),$friendsCost=array()){
		$this->con = $con;
		$this->username = $userLoggedIn;
		$this->title = $title;
		$this->description = $description;
		$this->cost = $cost;
		$this->date = $date;
		$this->friends = $friends;
		$this->friendsCost = $friendsCost;
	}

	public function addExpense(){
		$this->date = date("Y-m-d");
		if(empty($this->friends)){
			$query = "INSERT INTO expense (username,title,description,cost,amount_owe,cur_date) VALUES ('$this->username','$this->title','$this->description','$this->cost','0','$this->date')";
			$insert = mysqli_query($this->con,$query);
		}
		else {
			$combineArray = array_combine($this->friends,$this->friendsCost);
			$friends = implode(",",$this->friends);
			$friendsCost = implode(",",$this->friendsCost);
			$amount_lent = 0;
					foreach ($this->friendsCost as $key => $value) {
						$amount_lent += $value;
					}
			$query = "INSERT INTO expense (username,title,description,cost,friend_name,amount_owe,amount_lent,cur_date) VALUES ('$this->username','$this->title','$this->description','$this->cost','$friends','$friendsCost','$amount_lent','$this->date')";
			$insert = mysqli_query($this->con,$query);
			foreach ($combineArray as $key => $value) {
				$query = "UPDATE person SET amount_borrowed = amount_borrowed + '$value' WHERE username = '$key'";
				$result = mysqli_query($this->con,$query);	
			}	
            $this->updateSettle(1);
		}	
		

		$balanceUpdated = $this->updateBalance(1);
		$this->updateDue(1);
		$this->updateActivity();
		return true;
	}

	private function updateBalance($num){
		$query = "SELECT balance FROM person WHERE username = '$this->username'";
		$result = mysqli_query($this->con,$query);
		$balance = $result->fetch_assoc();
		$newBalance = $num * $this->cost + $balance['balance'];	 
		if($newBalance < 0){
			$newBalance = 0;
		} 		
		$query = "UPDATE person SET balance='$newBalance' WHERE username= '$this->username'";
		$result = mysqli_query($this->con,$query);
		return $result;
	}

	public function deleteExpense(){
		$query = "DELETE FROM expense WHERE username = '$this->username' and title='$this->title' and description='$this->description' and cost='$this->cost' and cur_date = '$this->date' and ((friend_name = '$this->friends' and amount_owe = '$this->friendsCost') or (friend_name IS NULL and amount_owe = '0'))";
		$del = mysqli_query($this->con,$query);
		$friends = explode(",",$this->friends);
		$friendsCost = explode(",",$this->friendsCost);
		$combineArray = array_combine($friends,$friendsCost);
		foreach ($combineArray as $key => $value) {
			$query = "SELECT * FROM person WHERE username = '$key'";
			$result = mysqli_query($this->con,$query);
			$row = $result->fetch_assoc();
			if($row['amount_borrowed'] - $value < 0){
				$amount = 0;
			}
			else {
				$amount = $row['amount_borrowed'] - $value;
			}
			$query = "UPDATE person SET amount_borrowed = '$amount' WHERE username = '$key'";
			$del = mysqli_query($this->con,$query);
		}
		$result = $this->updateBalance(-1);
		$result = $this->updateDue(-1);
		$this->updateSettle(-1);
		$this->updateActivityDelete();
		return $result;
	}

	private function updateDue($num){
		$query = "SELECT amount_lent FROM person WHERE username = '$this->username'";
		$result = mysqli_query($this->con,$query);
		$amountDue = $result->fetch_assoc();
		if($num == -1){
		$friendsCost = explode(",",$this->friendsCost);
	    }
	    else {
	    	$friendsCost = $this->friendsCost;
	    }
		$newAmountDue = $amountDue['amount_lent'];
		foreach ($friendsCost as $key => $value) {
			$newAmountDue += ($value * $num);
		}
		if($newAmountDue < 0){
			$newAmountDue = 0;
		}
		$query = "UPDATE person SET amount_lent='$newAmountDue' WHERE username='$this->username'";
		$result = mysqli_query($this->con,$query);
	} 

	private function updateSettle($num){
		
		   $this->date = date("Y-m-d");
		    if($num == 1){
		    	 $combineArray = array_combine($this->friends,$this->friendsCost);
		foreach ($combineArray as $key => $value) {
                $query = "INSERT INTO settle (borrower,lender,amt_borrowed,cur_date) VALUES ('$key','$this->username','$value','$this->date')";
                $result = mysqli_query($this->con,$query);
		}
		}
		else if($num == -1){
			$friends = explode(",",$this->friends);
		$friendsCost = explode(",",$this->friendsCost);
		 $combineArray = array_combine($friends,$friendsCost);
			foreach ($combineArray as $key => $value) {
                $query = "DELETE FROM settle WHERE lender = '$this->username' and borrower = '$key' and amt_borrowed = '$value'";
                $result = mysqli_query($this->con,$query);
		}
		}		
	}

	private function updateActivity(){
		 $combineArray = array_combine($this->friends,$this->friendsCost);
		 foreach ($combineArray as $key => $value) {
	        $message = "YOU lent ". $key . " Rs ". $value;
       $name = $this->username;
       $query = "INSERT INTO activities (username,message,cur_date) VALUES ('$name','$message','$this->date')";	 
        $result = mysqli_query($this->con,$query);
       $message = "YOU borrowed". " Rs ".$value." from ".$this->username;
       $name = $key;
       $query = "INSERT INTO activities (username,message,cur_date) VALUES ('$name','$message','$this->date')";
       $result = mysqli_query($this->con,$query);	
		 }
	  
	}	

	private function updateActivityDelete(){
		$message = "YOU deleted an Expense";
		 $query = "INSERT INTO activities (username,message,cur_date) VALUES ('$this->username','$message','$this->date')";	 
        $result = mysqli_query($this->con,$query);
	}
}
?>