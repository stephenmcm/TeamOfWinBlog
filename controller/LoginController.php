<?php

require_once 'model/User.php';
require_once 'controller/VerifyController.php';
class LoginController
{
	private $model;
	private $template;
	private $footer;
	private $nav;
	private $conn;

	private $loggedOutView = 'loginView';
	private $loggedInView = 'loggedInView';
	private $forgottenPassword = 'ResetPassword';

	public function __construct(PDO $conn)
	{
		$this->footer = 'includes/footer.php';
		include_once("controller/HeadController.php");
		$this->nav = new HeadController();
		$this->nav ->invoke();
		$this->conn = $conn;

	} //end constructor
	public function invoke()
	{
		if (!isset($_GET['action']))
		{
			echo "NO ACTION SET";
			$this->template = 'view/'.$this->loggedOutView.'Template.php';
			include_once('view/'.$this->loggedOutView.'.php');
			//create a new view and pass it our template
			$view = new LoginView($this->template,$this->footer);
			$content ="";
			$view->assign('title' , 'Loggged in');
			$view->assign('content' , $content);
		}elseif (isset($_GET['action'])){
			/*
			 * Forgot Password Screen
			*/
			if($_GET['action'] == 'forgotPassword')
			{
				if(!isset($_POST['action']) && !isset($_POST['email'])){
					$this->template = 'view/ResetPasswordTemplate.php';
					include_once('view/'.$this->loggedOutView.'.php');
					$view = new LoginView($this->template,$this->footer);
					$content ="";
					$view->assign('title' , 'Loggged in');
					$view->assign('content' , $content);
				}else{
					$get = array("action" => "mailSent");
					$verify = new VerifyController($get, $_POST);
					$verify->setDatabase($this->conn);
					$verify->invoke();
				}
				
			}
			else if($_GET['action'] == 'login')
			{
				$user = new User($this->conn);
				if(isset($_POST['username']) && isset($_POST['pass']))
				{
					$user = $user->loginUser($_POST["username"], $_POST["pass"]);
					if(!is_a($user, 'User')){
						//NOt logged In
						if($user = "verify"){
							echo "This account has not been verified. Please follow the instructions in your email to validate your account ";
							$get = array("action" => "mailSent");
							$verify = new VerifyController($get, $_POST);
							$verify->setDatabase($this->conn);
							$verify->invoke();
						}else{
							echo $user;
							$this->template = 'view/'.$this->loggedOutView.'Template.php';
							include_once('view/'.$this->loggedOutView.'.php');
							//create a new view and pass it our template
							$view = new LoginView($this->template,$this->footer);
							$content ="";
							$view->assign('title' , 'Loggged in');
							$view->assign('content' , $content);
						}
						
					}else
					{
						echo "testing";
						$user->sessionCreate();
						echo $user->getEmail();
					}
				}else
				{
					//if $_POSTs arn't set
					//NOt logged In
					echo "Post not set";
					$this->template = 'view/'.$this->$loggedOutView.'Template.php';
					include_once('view/'.$this->$loggedOutView.'.php');
					//create a new view and pass it our template
					$view = new LoginView($this->template,$this->header,$this->footer,$this->nav);
					$content ="";
					$view->assign('title' , 'Loggged in');
					$view->assign('content' , $content);
				}

			}else if($_GET['action'] == 'register')
			{
				echo "attempting to register<br/>";
				$user = new User($this->conn);
				if(isset($_POST['username']) && isset($_POST['pass']) && isset($_POST['email']))
				{
					$user = $user->createUser($_POST["username"], $_POST["pass"], $_POST["email"], "1");
					if(!is_a($user, 'User')){
						//NOt logged In
						echo $user;
						$this->template = 'view/'.$this->$loggedOutView.'Template.php';
						include_once('view/'.$this->$loggedOutView.'.php');
						//create a new view and pass it our template
						$view = new LoginView($this->template,$this->header,$this->footer,$this->nav);
						$content ="";
						$view->assign('title' , 'Loggged in');
						$view->assign('content' , $content);
					}else
					{
						echo $user->save();

					}
				}
			}else{
				echo "failed";
			}
		}

	} // end function
} //end class