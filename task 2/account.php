<?php

  class Account {
    private $error;
    private $con;

  	
  	function __construct($con)
  	{
  		$this->error = array();
  		$this->con = $con;
  	}

  	public function register($fn,$ln,$un,$pass,$pass2){
  		$this->validateFirstName($fn);
  		$this->validateLastName($ln);
  		$this->validateUsername($un);
  		$this->validatePassword($pass,$pass2);
  		if(empty($this->error)){
  			return $this->insertDetails($fn,$ln,$un,$pass);	
  		}
  		else {
  			return false;
  		}
  	}

  	private function insertDetails($fn,$ln,$un,$pass){
  		$encrypt = password_hash($pass,PASSWORD_DEFAULT);
  		$query = "INSERT INTO person (first_name,last_name,username,password,balance,amount_lent,amount_borrowed) VALUES ('$fn','$ln','$un','$encrypt','0','0','0')";
  	    $result = mysqli_query($this->con,$query);
  	    return $result;
  	}

  	private function validateFirstName($fn){
  		if(strlen($fn) > 20 || strlen($fn) < 5){
  			array_push($this->error,"Invalid First Name");
  			return;
  		}
  	}

  	private function validateLastName($ln){
  		if(strlen($ln) > 20){
  			array_push($this->error,"Invalid Last Name");
  			return;
  		}
  	}

  	private function validateUsername($un){
  		if(strlen($un) > 20){
          array_push($this->error,"Invalid Username");
          return;
  		}

  		$query = "SELECT * FROM person WHERE username = '$un'";
  		$existingUsername = mysqli_query($this->con,$query);
  		if(mysqli_num_rows($existingUsername) > 0){
  			array_push($this->error, "Username Exists");
  			return;
  		}
  	}

  	public function validatePassword($pass,$pass2){
  		if(strlen($pass) < 6 || strlen($pass2) < 6){
  			array_push($this->error,"Longer Password required");
  			return;
  		}
  		if($pass != $pass2){
  			array_push($this->error, "Passwords don't match");
  		}
  	}

  	public function login($username,$password){

  		$query = "SELECT * FROM person WHERE username = '$username'";
  		$checkLogin = mysqli_query($this->con,$query);
        if(mysqli_num_rows($checkLogin) == 1){
  	       $row = $checkLogin->fetch_assoc();
  	       $passwordCorrect = password_verify($password,$row['password']);
  	       if($passwordCorrect){
  	       	return true;
  	       }
  	       else{
  	       	 array_push($this->error,"Incorrect Password");
             return false;
           }
  	    }
  	    else {
  	    	array_push($this->error,"Username doesn't exist");
  	    	return false;
  	    }
  	}

  	public function displayError($error){
  		if(!in_array($error,$this->error)){
  		       $error = "";
  		}
      if($error != "")
  		  return '<span style="background-color:rgba(0,0,0,0.6);">'.$error.'</span><br>';
      
  	}

}

?>