<?php 
   class Settle {
   	private $con;
   	private $payer;
   	private $payee;
   	private $amount;

   	function __construct($con,$payer,$payee,$amount){
   		$this->con = $con;
   		$this->payer=$payer;
   		$this->payee=$payee;
   		$this->amount=$amount;
   	}

   	function updateTable(){
        $date = date("Y-m-d");
        $amount = $this->amount;
       $query = "SELECT * FROM person WHERE username='$this->payee'";
       $result = mysqli_query($this->con,$query);
       $row = $result->fetch_assoc();
       if($amount >= $row['amount_lent']){
       	$amount -= $row['amount_lent'];
       	$query = "UPDATE person SET amount_lent = '0' WHERE username = '$this->payee'";
       	$result = mysqli_query($this->con,$query);
       	$query = "UPDATE person SET amount_borrowed = (amount_borrowed +'$amount') WHERE username = '$this->payee'";
       	$result = mysqli_query($this->con,$query);
       	$query = "UPDATE person SET amount_lent = amount_lent + '$amount' WHERE username='$this->payer'";
       	$result = mysqli_query($this->con,$query);
       	$query = "INSERT INTO settle (borrower,lender,amt_borrowed,cur_date) VALUES ('$this->payee','$this->payer','$amount','$date')";
        $result = mysqli_query($this->con,$query);
       }
       else {
       	 	$query = "UPDATE person SET amount_lent = amount_lent - '$this->amount' WHERE username = '$this->payee'";
         	$result = mysqli_query($this->con,$query);
         	$query = "SELECT amount_borrowed FROM person WHERE username = '$this->payer'";
         	$result = mysqli_query($this->con,$query);
         	$row = $result->fetch_assoc();
         	if($row['amount_borrowed'] - $this->amount <= 0){
         		$amt = $this->amount - $row['amount_borrowed'];
         		 $query = "UPDATE person SET amount_lent = (amount_lent + '$amt') WHERE username = '$this->payer'";
         	    $result = mysqli_query($this->con,$query);
         		$query = "UPDATE person SET amount_borrowed = '0' WHERE username = '$this->payer'";
         	    $result = mysqli_query($this->con,$query);
         	}
         	else {
         	$query = "UPDATE person SET amount_borrowed = amount_borrowed - '$this->amount' WHERE username = '$this->payer'";
         	$result = mysqli_query($this->con,$query);	
         	}
         	
       }
       $message = "YOU paid ". $this->payee . " Rs ". $this->amount;
       $name = $this->payer;
       $query = "INSERT INTO activities (username,message,cur_date) VALUES ('$name','$message','$date')";
       $result = mysqli_query($this->con,$query);
       $message = $this->payer." paid YOU"." Rs " . $this->amount;
       $name = $this->payee;
       $query = "INSERT INTO activities (username,message,cur_date) VALUES ('$name','$message','$date')";
       $result = mysqli_query($this->con,$query);

   	}

   	function settleExpense(){
   		$amount = $this->amount;
   		$query = "SELECT * FROM settle WHERE borrower = '$this->payer' and lender = '$this->payee' ORDER BY cur_date";
   		$get = mysqli_query($this->con,$query);
   
   		while($row = $get->fetch_assoc()){
   			$amountBorrowed = $row['amt_borrowed'];
           if($row['amt_borrowed'] <= $amount){
           	$amount -= $row['amt_borrowed'];
           	$query = "DELETE FROM settle WHERE borrower = '$this->payer' and lender = '$this->payee' and amt_borrowed = '$amountBorrowed'";
           	$del = mysqli_query($this->con,$query);
           }
           else {
           	$query = "UPDATE settle SET amt_borrowed = amt_borrowed - '$amount' WHERE borrower = '$this->payer' and lender = '$this->payee' and amt_borrowed = '$amountBorrowed'";
           	$result = mysqli_query($this->con,$query);
           	$amount = 0;
           	break;
           }
   		}
   		$this->updateTable();
   	}	

   }

   ?>