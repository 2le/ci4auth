<?php namespace App\Controllers;
			use ZxcvbnPhp\Zxcvbn;

class Secure extends Application
{
	/**
	 * Constructor
     */
	public function __construct(...$params)
    {
		$this->isSecure = true;
        parent::init();
    }
	
	public function index()
	{
	if (!$this->getCurrentUser())
	return redirect()->to('/secure/login');
	
	$this->data = array();
	$this->data['title'] = 'Demo';
	
}

//--------------------------------------------------------------------
public function login()
{
	$email = false;
	$password = false;
	$rememberme = false;
	if (isset($_POST['username'])) {
		// Tip: do validation here!
		$email = filter_var($_POST["username"], FILTER_SANITIZE_STRING);
		$password = filter_var($_POST["password"], FILTER_SANITIZE_STRING);
		$rememberme = filter_var(isset($_POST["remember"]));
		
		$result = $this->auth->login($email, $password, $rememberme);
		if($result['error']) {
			// Something went wrong, display error message
			$this->data['viewdata'] = array();
			$this->data['viewdata']['msg'] = $result['message'];
			$this->data['pagetitle'] = 'CodeIgniter 4 Demo Login<br>id demo pw test1';
			return view('secure/login', $this->data);				
		} else {
			// Logged in successfully, set cookie, display success message
			setcookie($this->authconfig->cookie_name, $result['hash'], $result['expire'], 
			$this->authconfig->cookie_path, $this->authconfig->cookie_domain, $this->authconfig->cookie_secure, $this->authconfig->cookie_http);
			$this->data['pagetitle'] = 'CodeIgniter 4 Demo Secure Area';
			$this->data['pagebody'] = 'welcome_secure';
			$this->render();
		}				
			}
			$this->data['pagetitle'] = 'CodeIgniter 4 Demo Secure Area';
			return view('secure/login', $this->data);
		}
		public function register()
		{
			$this->data = array();
			$email = false;
			$password = false;
			$confirm_password = false;
        if (isset($_POST['password'])) {
			$rules = [
				'first_name' => 'trim|required|max_length[50]', 
				'last_name' => 'trim|required|max_length[50]', 
				'email'    => 'trim|required|valid_email',
				'password' => 'trim|required|min_length[5]',
				'confirm_password' => 'trim|required|matches[password]'
			];
			if (! $this->validate($rules))
			{
				return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
			}
	
			$firstname = filter_var($_POST["first_name"], FILTER_SANITIZE_STRING);
            $lastname = filter_var($_POST["last_name"], FILTER_SANITIZE_STRING);
            //$username = filter_var($_POST["username"], FILTER_SANITIZE_STRING);
			$email = $this->request->getPost('email');
			$password = $this->request->getPost('password');
			$terms = filter_var(isset($_POST["terms"]));
            //$key = $_POST['g-recaptcha-response'];
            //$params = array("firstName" => "{$firstname}", "lastName" => "{$lastname}", "username" => "{$username}", "type" => '1');
            $params = array("firstName" => "{$firstname}", "lastName" => "{$lastname}");
            $result = $this->auth->register($email, $password, $confirm_password, $params, $sendmail = true);
            if ($result['error']) {
                // if registration not complete
                $output = json_encode(array("type" => 1, "result" => $result['message']));
                $this->data['viewdata'] = array();
				$this->data['viewdata']['msg'] = $result['message'];
				

//$userData = [$firstname,$lastname,$email];

//$zxcvbn = new Zxcvbn();
//$strength = $zxcvbn->passwordStrength($password, $userData);
//$this->data['viewdata']['msg'] .= ' '.$strength['score'];

            } else {
                $uid = $this->auth->getUID($email);
                $output = json_encode(array("type" => 0, "result" => $result['message']));
                redirect()->to('/secure/login');
            }
            $registration = false;
        }
		
		$this->data['pagetitle'] = 'Register - CodeIgniter 4 Demo Secure Area';
        return view('secure/register', $this->data);
//    secure/register';

    }
    public function forgot()
    {
		$this->data = array();
		$email = false;
		if (isset($_POST['username'])) {		
			$email = filter_var($_POST["username"], FILTER_SANITIZE_STRING);	
			$result = $this->auth->requestReset($email);			
			if($result['error']) {
				// Something went wrong, display error message
				$this->data['viewdata'] = array();
				$this->data['viewdata']['msg'] = $result['message'];
				$this->data['pagetitle'] = 'CodeIgniter 4 Demo Login<br>id demo pw test1';
				return view('secure/forgot', $this->data);	
			}
			else
			{
				redirect()->to('/secure/login');
			}
		}
	   $this->data['pagetitle'] = 'CodeIgniter 4 Demo Secure Area';
        return view('secure/forgot', $this->data);
    }
}
