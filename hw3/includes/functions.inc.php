<?php
function process_script() {
	
	$action=strtolower(getRequest('action',true,'get'));
	$accepted_actions='login,signup,account,edit,logout,cancel';
	$actionsArray = explode(",", $accepted_actions);
	
	
	if(!in_array($action, $actionsArray)) { //action is not a specified action go right to login
		$action = "login";
	}
	
	if(get_SESSION("id") === null && ($action !== "signup")) {//session doesn't exist and not on signup page, go to login
		$action = 'login';
	} else if(get_SESSION("id") != null && ($action === "login" || $action === "signup")) { //session already exists, go right to account instead of login screen or sign up
		$action = "account";
	}

	return get_template($action, $action()); 
}

function set_header($action=null) {
	$url = (empty($action)) ? urlhost() : urlhost().'?action='.$action;
	header('Location: '. $url );
	exit();
}

function &account() {
	$HTML=array();
	return $HTML;
}

/*
* deletes the user of the current session and destroys the session
*/
function &cancel() {
	
	$userID = get_SESSION("id");
	mysql_query(sprintf("delete from users where userID='%s'", mysql_real_escape_string($userID)));
	
	session_destroy();
	set_header('login'); 
	exit();
}

/*
* destroys the session of whoever was logged in
*/
function &logout() {
	session_destroy();
	set_header('login'); 
	exit();
}

/*
* Where the login is handled. Checks to see if the user is in the data base. If yes, start a session for that user and direct to the account page
* Produces error messages if email is incorrect, if the user isn't found, database errors, etc.
*/
function &login(){
	$HTML=array();
	$HTML['email']='';
	$HTML['password']='';
	
	$HTML['login_error']=''; //Reset Error

	if (getRequest('submitted',true,'post') !=='yes') {return $HTML;} //request isn't a post, return html as is
	
	foreach($HTML as $key=> &$value) {
		$value=getRequest($key, true, 'post');
	}
	
	//validation takes place here
	$userID=0;
	if (empty($HTML['email'])) {
		$HTML['login_error'] = 'Email Cannot be empty';
	}
	elseif (empty($HTML['password'])) { 
		$HTML['login_error'] ='Password cannot be empty'; //Security measure!
	}
	elseif (filter_var($HTML['email'],FILTER_VALIDATE_EMAIL) ===false) {
		$HTML['login_error'] ='Invalid Email Address';
	}
	else {
		$userID=validate_record($HTML['email'],$HTML['password']);
		if($userID === "Database Error") {
			$HTML['login_error'] = "Database Error";
			
		} else if ($userID < 1) {
			$HTML['login_error'] ='Account not found or invalid Email/Password';
			$HTML['password'] = '';
		}
	}

	
	if (empty($HTML['login_error'])) { //no errors
		$result = mysql_query(sprintf("select userID from users where email='%s'", mysql_real_escape_string($HTML['email'])));
		if(!$result) { //check if there was a database error
			$HTML['login_error'] = "Database Error";
			return $HTML;
		} 
		$row = mysql_fetch_array($result);
		set_SESSION("id", $row{'userID'});
		set_header('account'); 
		exit();
	}
	
	

	return $HTML;
}

/*
* Edit is used for when the user wants to edit their account. They can change their email to an email not existing in the database, 
* and edit other parts of their account here. This function will update the user's information assuming they typed in all the correct information
*/
function &edit() {
	$userID = get_SESSION("id");
	$result = mysql_query(sprintf("select email, city, country from users where userID='%s'", mysql_real_escape_string($userID))); //gets users current data
	if(!$result) { //check if there was a database error
		$HTML['email_error'] = "Database Error";
		return $HTML;
	}
	$row = mysql_fetch_array($result);
	
	$HTML=array();
	$HTML["email"] = $row{'email'};
	$HTML["city"] = $row{'city'};
	
	$HTML['country_options_escape'] = getOptionValues($row{'country'}); //populate the country drop down
	
	if (getRequest('submitted',true,'post') !=='yes') {return $HTML;}
	
	//gets all the values needed from the post request
	$email = $_POST["email"];
	$password = $_POST["password"];
	$confirmPassword = $_POST["confirm_password"];
	$city = $_POST["city"];
	$country = $_POST["countryID"];
	
	$HTML = validateUserValues($email, $password, $confirmPassword, $city, $country, true); //called to validate all the changed info
	
	if (empty($HTML['email_error']) && empty($HTML['confirm_password_error']) && empty($HTML['city_error']) && empty($HTML['countryID_error'])) { //no errors
		$result = mysql_query(sprintf("update users set password='%s', city='%s', country='%s' where userID='%s'",    mysql_real_escape_string($HTML['password']), 
																									mysql_real_escape_string(removeSpacing($city)), 
																									mysql_real_escape_string($country),
																									get_SESSION("id")));
		if(!$result) { //check if there was a database error
			$HTML['email_error'] = "Database Error";
			return $HTML;
		}
		set_header('account'); 
		exit();
	}
	
	return $HTML;
} 

/*
* sign up is the same as edit but for when the user is signing up for the first time. They can input any email that doesn't exist in the database. 
* Validation takes place to make sure everything is input correctly and then the session is started and user is added to db.
*/
function &signup($edit=false){
	
	$HTML=array();
	
	$country = "";
	if (getRequest('submitted',true,'post') ==='yes') {
		$country = $_POST['countryID']; //used for if the user posts the wrong data, want to keep the country selected
	}
	
	$HTML['country_options_escape'] = getOptionValues($country); //options for the dropdown
	
	if (getRequest('submitted',true,'post') !=='yes') {return $HTML;}
	
	//gets all the values needed from the post request
	$email = $_POST["email"];
	$password = $_POST["password"];
	$confirmPassword = $_POST["confirm_password"];
	$city = $_POST["city"];
	$country = $_POST["countryID"];
	
	$HTML = validateUserValues($email, $password, $confirmPassword, $city, $country, false);
	
	if (empty($HTML['email_error']) && empty($HTML['confirm_password_error']) && empty($HTML['city_error']) && empty($HTML['countryID_error'])) { //no errors
		$result = mysql_query(sprintf("insert into users (email, password, city, country) values ('%s', '%s', '%s', '%s')", mysql_real_escape_string($email), 
																													mysql_real_escape_string($HTML['password']), 
																													mysql_real_escape_string(removeSpacing($city)), 
																													mysql_real_escape_string($country)));
		if(!$result) { //check if there was a database error
			$HTML['email_error'] = "Database Error";
			return $HTML;
		}
		$id = mysql_query(sprintf("select userID from users where email ='%s'", mysql_real_escape_string($email)));//gets the id to start the session
		if(!$id) { //check if there was a database error
			$HTML['email_error'] = "Database Error";
			return $HTML;
		}
		$row = mysql_fetch_array($id);
		
		set_SESSION("id", $row{'userID'}); //setting session for the user
		set_header('account'); 
		exit();
	}
	
	return $HTML;
}

/*
* Takes a country id as a parameter to know which country was most recently selected and populates the options that go in the select tag
* to show the drop down of the list of countries.
*/
function getOptionValues($country) {
	$result = mysql_query("select countryID, country from countries where active =\"yes\" order by country");
	if(!$result) { //check if there was a database error
		$options = "Database Error";
		return $options;
	}
	$options = "<option value=\"Please Select\">Please Select</option>";
	while ($row = mysql_fetch_array($result)) {
		if($country !== $row{'countryID'}) {
			$options .= "<option value='".$row{'countryID'}."'>".$row{'country'}."</option>";
		} else {
			$options .= "<option value='".$row{'countryID'}."' selected>".$row{'country'}."</option>";
		}
	}
	return $options;
}

/*
* Takes parameters email, password, confirm password, city and country and determines whether or not they are valid
* returns an array of html values to be used by the template pages. This is used as a function because the sign up page
* and the edit page are the same aside from which page they redirect to.
*/
function validateUserValues($email, $password, $confirmPassword, $city, $country, $editPage) {
	$HTML=array();
	$HTML['email'] = utf8HTML($email);
	$email = utf8HTML($email);
	
	$result = mysql_query(sprintf("select userID, password, email from users where email = '%s'", mysql_real_escape_string($email))); //for checking if an email already exists
	if(!$result) { //check if there was a database error
		$HTML['email_error'] = "Database Error";
		return $HTML;
	}
	$row = mysql_fetch_array($result);
	if(empty($_POST["email"])) {
		$HTML['email_error'] = 'Email cannot be empty!';
	} else if(filter_var($email,FILTER_VALIDATE_EMAIL) === false) {
		$HTML['email_error'] = "Invalid Email Address.";
	} else if(strtolower($row{'email'}) === strtolower($email) && !$editPage) { //there is a row with the same email
		$HTML['email_error'] = "Email Already Exists.";
	} else if(strtolower($row{'email'}) === strtolower($email) && $editPage && $row{'userID'} !== get_SESSION("id")) { //if editing, cannot change email to an address already existing.
		$HTML['email_error'] = "Email Already Exists.";
	}
	
	$HTML['password'] = '';
	$HTML['confirm_password'] = '';
	$HTML['hidden_password'] = $password;
	if(empty($_POST["password"]) && empty($_POST["confirm_password"]) && !$editPage) {
		$HTML['confirm_password_error'] = 'Password cannot be empty';
		$HTML['hidden_password'] = '';
	} else if(empty($_POST["password"]) && empty($_POST["confirm_password"]) && $editPage) {
		$HTML['password'] = $row{'password'};
	} else if($password !== $confirmPassword) {
		$HTML['confirm_password_error'] = 'Passwords do not match';
		$HTML['confirm_password'] = '';
		$HTML['hidden_password'] = '';
	} else if (!preg_match("#^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[-!$%^&*()_@+|~=`{}\[\]:;'<>?,.\/\#]).{6,20}$#", $password) && $HTML['hidden_password'] !== HIDDEN_PW) { 
		$HTML['confirm_password_error'] = 'Password must be between 6-20 characters and secure!'; //must have uppercase, lowercase, number and symbol in it and be 6-20 length
		$HTML['confirm_password'] = '';
		$HTML['hidden_password'] = '';
	} else if($HTML['hidden_password'] !== HIDDEN_PW){ //password is okay and is not hidden
		$HTML['password'] = md5($password); //displayed in hidden tag, don't want to show actual password
		$HTML['hidden_password'] = HIDDEN_PW; //place holder for password
	} else  { //keep the password already typed
		$HTML['password'] = $_POST["password_encrypted"];
	}
	
	$HTML['city'] = removeSpacing($city);
	$city = removeSpacing($city);
	if(empty($_POST["city"])) {
		$HTML['city_error'] = 'City cannot be empty';
		$HTML['city'] = utf8HTML($city);
	} else if (preg_match("#(?=.*[-!$%^&*()_@+|~=`{}\[\]:;'<>?,.\/\#])#", $city)) {
		$HTML['city_error'] = 'City cannot have a symbol in it';
		$HTML['city'] = utf8HTML($city);
	}
	
	if($country === "Please Select") {
		$HTML['countryID_error'] = 'A Country Must Be Selected.';
	} 
	$HTML['country_options_escape'] = getOptionValues($country);
	return $HTML;
}

/*
* called by the login page to make sure the user with that email and password exists. Returns database error, the user id, or 0 depending on the result. 
* 0 is returned if no user currently exists with that email and password
*/
function validate_record($email, $password=NULL) {
	if (empty($GLOBALS['DB'])) {die ('Database Link is not set'); }
	$result = mysql_query(sprintf("select userID from users where email = '%s' and password = '%s'", $email, md5($password)));
	if(!$result) { //check if there was a database error
		return "Database Error";
	}
	if($row = mysql_fetch_array($result)) {
		return $row{'userID'};
	} else {
		return 0;
	}
}
/*
* Given a key returns the session value (userID in our case) for that key
*/
function get_SESSION($key) {
	return ( !isset($_SESSION[$key]) ) ? NULL : decrypt($_SESSION[$key]);
}

/*
* given a key value pair, sets the session
*/
function set_SESSION($key, $value='') {
	if (!empty($key)) {
		$_SESSION[$key]=encrypt($value);
		return true;
	}
	return false;
}

function util_getenv ($key) {
		return ( isset($_SERVER[$key])? $_SERVER[$key]:(isset($_ENV[$key])? $_ENV[$key]:getenv($key)) );
}


function host ($protocol=null) {
	$host = util_getenv('SERVER_NAME');
	if (empty($host) || ($host=='_')) {	$host = util_getenv('HTTP_HOST'); }
	return (!empty($protocol)) ? $protocol.'//'.$host  : 'http://'.$host;
}


function urlhost ($protocol=null) {
	return host($protocol).$_SERVER['SCRIPT_NAME'];
}


function get_template($file, &$HTML=null){
		$content='';
		ob_start();
			if (@include(TMPL_DIR . '/' .$file .'.tmpl.php')):
			$content=ob_get_contents();
		endif;
		ob_end_clean();
		return $content;
}

function getRequest($str='', $removespace=false, $method=null){
	if (empty($str)) {return '';}
  		switch ($method) {
			case 'get':
				$data=(isset($_GET[$str])) ? $_GET[$str] : '' ;
				break;
			case 'post':
				$data=(isset($_POST[$str])) ? $_POST[$str] : '';
				break;
				
			default:
				$data=(isset($_REQUEST[$str])) ? $_REQUEST[$str] : '';
		}
 		
		if (empty($data)) {return $data;}
		
		
		if (get_magic_quotes_gpc()) {
			$data= (is_array($data)) ? array_map('stripslashes',$data) : stripslashes($data);	
		}

		if (!empty($removespace)) {
			$data=(is_array($data)) ? array_map('removeSpacing',$data) : removeSpacing($data);
		}

		return $data;
	}

function removeSpacing($str) {
		return trim(preg_replace('/\s\s+/', ' ', $str));
}
	
function utf8HTML ($str='') {
  	   	return htmlentities($str, ENT_QUOTES, 'UTF-8', false); 
}
	
/*
* encrypts the session. Takes a parameter to be encrypted and SALT to which is used in the encrypting. If salt is null uses default SECURE_KEY
*/
function encrypt($text, $SALT=null) {
	if($SALT === null) {
		$SALT = SECURE_KEY;
	}
	return base64_encode(mcrypt_ecb(MCRYPT_DES, $SALT, $text, MCRYPT_ENCRYPT));
} 

/*
* Decrypts the session 
*/
function decrypt($text, $SALT=null) { 
	if($SALT === null) {
		$SALT = SECURE_KEY;
	}
	$text = base64_decode($text);
	return removeSpacing(mcrypt_ecb(MCRYPT_DES, $SALT, $text, MCRYPT_DECRYPT));
}  
?>