<?php 
  class ShareCost {
  	private $con;
  	private $friends;
    private $friendsCost;

    function __construct($con,$friends = array(),$friendsCost = array()){
    	$this->con = $con;
    	$this->friends = $friends;
    	$this->friendsCost = $friendsCost;
    }

    public function validateFriends(){
    	foreach ($this->friends as $key => $value) {
    		$query = "SELECT username FROM person WHERE username = '$value'";
    		$result = mysqli_query($this->con,$query);
    		if(mysqli_num_rows($result) == 0){
    			return 0;
    		}
    	}
    	return 1;
    }

    public function validateExpense($cost){
          $moneyLent = 0;
    	foreach ($this->friendsCost as $key => $value) {
    		if($value <= 0){
    			return 0;
    		}
    	}
    	foreach ($this->friendsCost as $key => $value) {
    		$moneyLent += $value;
    	}
    	if($moneyLent >= $cost){
    		return -1;
    	}

        return 1;
    }

  }
?>