﻿## Composer
https://packagist.org/packages/smce/framework

# Ornekler

Controller
--------------------------

``` php
<?php

use smce\SmController;
use smce\DB;


class SiteController extends SmController
{

    public $layout='//layouts/column1';

    public function actionIndex()
    {
		
        if (Smce::app()->getState("name")=="") {
             $this->render("index",array(
                "model"=>"Site controllerden gelen bi yazıdır",
                "grup"=>"PHP-TR grubu",
                "grup_site"=>"https://www.facebook.com/groups/tr.developers/",
             ));
        } else {
            $this->redirect("panel/index");
        }
    }
	

    public function actionAbout()
    {
         $this->render("pages/about");
    }

	
    public function actionLogin()
    {
		
        $model=new LoginForm();

        if (isset($_POST["LoginForm"])) {
            $post=(object) $_POST["LoginForm"];

            $model->username    =    $post->username;
            $model->password    =    $post->password;
            $model->rememberMe    =    isset($post->rememberMe);
			
            if ($model->validate() && $model->login()) {
			
                //redirect url
                $this->redirect("panel/index");

            }
        }

        $this->render("login",array(
            "model"=>$model,
         ));

    }

    public function actionLogout()
    {
        Smce::app()->stateClear();
        $this->redirect("site/index");
    }

    public function error($err)
    {
         $this->render("error",array(
            "code"=>$err,
         ));
    }
}



```

View
--------------------------

``` php

<b>Proje BaseUrl</b>: <?PHP echo Smce::app()->baseUrl?> <br />
<b>Proje BasePath</b>: <?PHP echo Smce::app()->basePath?><br />
<b>Layout/MasterPage</b>: <?PHP echo $this->layout?>
<br />
<br />
<br />
<b>Controller</b>: <?PHP echo BASE_CONTROLLER?><br />
<b>View</b>: <?PHP echo BASE_VIEW?>
<br />
<br />
<br />

<b>İp Adres</b>: <?PHP echo Smce::app()->ip?><br />
<br />
<br />

<b>SiteController gelen veri</b>: <?PHP echo $model?><br />
<b>Grup</b>: <?PHP echo $grup?>	<br />
<b>Grup-Site</b>: <?PHP echo $grup_site?>	<br />

```


Config
--------------

``` php
<?php
	
  
return array(
	'name'=>'SmceFramework',
	
	// autoloading model and component classes
	'import'=>array(
		'models',
		'components',
	),
	
	'debug'=>true,
	
	'components'=>array(
		'db'=>array(
			'user'=>"root",
			'password'=>"",
			'name'=>"",
			'host'=>"localhost",
		),
	),
);


?>
```
	
Model
---------------

``` php
<?php

/**
 * LoginForm class.
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
use smce\SmFormModel;

class LoginForm extends SmFormModel
{
	public $username;
	public $password;
	public $rememberMe;

	private $_identity;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			// username and password are required
            array('username, password', 'required'),
			// rememberMe needs to be a boolean
            array('rememberMe', 'boolean'),
			// password needs to be authenticated
            array('password', "after", 'authenticate'),//array('password', false, 'authenticate'),
			
        );
		
		/*
			
			-----RULES-----
			
				required
				valid_email
				max_len,1
				min_len,4
				exact_len,10
				alpha
				alpha_numeric
				numeric
				integer
				boolean
				float
				valid_url
				url_exists
				valid_ip
				valid_ipv4
				valid_ipv6
				valid_name
				contains,free pro basic
				
			-----RULES-----
			
		*/
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'rememberMe'=>'Beni Hatırla',
			'username'=>'E-mail',
			'password'=>'Parola',
		);
	}

	/**
	 * Authenticates the password.
	 * This is the 'authenticate' validator as declared in rules().
	 */
	 
	public function authenticate()
	{
		
		$this->_identity=new UserIdentity($this->username,$this->password);
		if($this->_identity->authenticate() && !$this->error)
			$this->addError('password','Kullanıcı ve/veya Parola hatalı.');

	}

	/**
	 * Logs in the user using the given username and password in the model.
	 * @return boolean whether login is successful
	 */
	public function login()
	{
		if ($this->_identity===null) {
			$this->_identity=new UserIdentity($this->username,$this->password);
			$this->_identity->authenticate();
		}
		if ($this->_identity->errorCode===UserIdentity::ERROR_NONE) {
			$duration=$this->rememberMe ? 3600*24*30 : 0; // 30 days
            Smce::app()->login($this->_identity,$duration);
			return true;
		} else
			return false;
	}
}



```

Model 2
---------------------------------------

``` php
<?php

use smce\DB;

class UsersModel
{
	public function __construct()
	{
		DB::$error_handler = false;
		DB::$throw_exception_on_error = true;
	}

	public function create($array)
	{
		  //insert
          $query=DB::insert('users',$array);

	}

	public function update($array)
	{
	     //update
          $query=DB::update('users', $array, "userID=%s", $array["userID"]);

	}

	public function admin()
	{
		 //select
         $query=DB::query("SELECT * FROM users");
	     return $query;
	}

}



```

# Composer

SmTemplate // Template Engine
-----

``` php

<?php

require("vendor/autoload.php");

use \SmTemplate\SmTemplate;

$SmTemplate=new SmTemplate();
$SmTemplate->setView("index",$array);
$SmTemplate->setLayout("column1");
$SmTemplate->setThemeDirectory("");
$SmTemplate->run();
//print_r($SmTemplate->getError());
?>

```

Layout //column1.php

``` php
<?php /* @var $this Controller */ ?>
<html>
	<head>
	</head>
<body>
	<div id="content">
	<?php echo $content; ?>
	</div><!-- content -->
</body>
</html>
```

SmGump //Validation
-----

``` php
require("vendor/autoload.php");

use \SmGump\SmGump;

$validator = new SmGump();
$rules = array(
			'missing'   	=> 'required',
			'email'     	=> 'valid_email',
			'max_len'   	=> 'max_len,1',
			'min_len'   	=> 'min_len,4',
			'exact_len' 	=> 'exact_len,10',
			'alpha'	       	=> 'alpha',
			'alpha_numeric' => 'alpha_numeric',
			'numeric'		=> 'numeric',
			'integer'		=> 'integer',
			'boolean'		=> 'boolean',
			'float'			=> 'float',
			'valid_url'		=> 'valid_url',
			'url_exists'	=> 'url_exists',
			'valid_ip'		=> 'valid_ip',
			'valid_ipv4'	=> 'valid_ipv4',
			'valid_ipv6'	=> 'valid_ipv6',
			'valid_name'    => 'valid_name',
			'contains'		=> 'contains,free pro basic'
		);
		
		
		$invalid_data = array(
			'missing'   	=> '',
			'email'     	=> "not a valid email\r\n",
			'max_len'   	=> "1234567890",
			'min_len'   	=> "1",
			'exact_len' 	=> "123456",
			'alpha'	       	=> "*(^*^*&",
			'alpha_numeric' => "abcdefg12345+\r\n\r\n\r\n",
			'numeric'		=> "one, two\r\n",
			'integer'		=> "1,003\r\n\r\n\r\n\r\n",
			'boolean'		=> "this is not a boolean\r\n\r\n\r\n\r\n",
			'float'			=> "not a float\r\n",
			'valid_url'		=> "\r\n\r\nhttp://add",
			'url_exists'	=> "http://asdasdasd354.gov",
			'valid_ip'		=> "google.com",
			'valid_ipv4'    => "google.com",
			'valid_ipv6'    => "google.com",
			'valid_name' 	=> '*&((*S))(*09890uiadaiusyd)',
			'contains'		=> 'premium'
		);
		
		$valid_data = array(
			'missing'   	=> 'This is not missing',
			'email'     	=> 'sean@wixel.net',
			'max_len'   	=> '1',
			'min_len'   	=> '1234',
			'exact_len' 	=> '1234567890',
			'alpha'	       	=> 'ÈÉÊËÌÍÎÏÒÓÔasdasdasd',
			'alpha_numeric' => 'abcdefg12345',
			'numeric'		=> 2.00,
			'integer'		=> 3,
			'boolean'		=> FALSE,
			'float'			=> 10.10,
			'valid_url'		=> 'http://wixel.net',
			'url_exists'	=> 'http://wixel.net',
			'valid_ip'		=> '69.163.138.23',
			'valid_ipv4'    => "255.255.255.255",
			'valid_ipv6'    => "2001:0db8:85a3:08d3:1319:8a2e:0370:7334",
			'valid_name' 	=> 'Sean Nieuwoudt',
			'contains'		=> 'free'
		);


echo "\nBEFORE SANITIZE:\n\n";

echo "<pre>";
print_r($invalid_data);

echo "\nAFTER SANITIZE:\n\n";
print_r($validator->sanitize($invalid_data));

echo "\nTHESE ALL FAIL:\n\n";
$validator->validate($invalid_data, $rules);

// Print out the errors using the new get_readable_errors() method:
print_r($validator->get_readable_errors());

if($validator->validate($valid_data, $rules)) {
	echo "\nTHESE ALL SUCCEED:\n\n";
	print_r($valid_data);
}

echo "\nDONE\n\n";

echo "</pre>";
?>

```


- 

## Tartismalar

PHP-TR gurubu https://www.facebook.com/groups/tr.developers/
Php-ist grubu https://www.facebook.com/groups/istanbul.developers/

## Gelistirici Hakkinda
Samed Ceylan
