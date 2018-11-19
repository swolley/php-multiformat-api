<?php
namespace Api\Local;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './php-curl/CurlExtended.php';

//logger handlers
define('FILE', 1 << 0);
define('EMAIL', 1 << 1);
define('HTTP', 1 << 2);

//logger levels
define('INFO', 'INFO');
define('WARN', 'WARN');
define('ERROR', 'ERROR');

class Logger {
	public function __construct(){
		error_reporting(E_ALL | E_STRICT);
		ini_set('display_errors', false);
		ini_set('log_errors', true);
		ini_set('error_log', './php_errors.log');
		ini_set('ignore_repeated_errors', true);
		ini_set('ignore_repeated_source', false);
	}

	/**
	 * main configs
	 */
	private $configs = [
		'filePath' => null,
		'httpOptions' => null,
		'emailOptions' => null,
		'autoTrigger' => 0
	];

	/**
	 * register handler's parameters
	 * @param int $handler choosen LogHandler
	 * @param mixed $configs file path
	 */
	public function register (int $handler, $configs) {
		switch ($handler) {
			case FILE:
				$this->setFile($configs);
				break;
			
			case HTTP:
				$this->setHttp($configs);
				break;
			
			case EMAIL:
				$this->setEmail($configs);
				break;
		}
	}

	/**
	 * sets php set_error_handler function to log automatically errors
	 * @param int $handlers choosen handlers for autotriggering logs
	 */
	public function enableAutoTrigger (int $handlers) {
		$this->configs['autoTrigger'] = $handlers;

		set_error_handler(function($errno, $errstr, $errfile, $errline/*, $backtrace*/){
			$content = [
				'message' => $errstr . ' in ' . $errfile . ' on line ' . $errline
			];

			$this->create(intl_error_name($errno), $content, $this->configs['autoTrigger']);
		});
	}

	/**
	 * restore php default set_error_handler function
	 */
	public function disableAutoTrigger () {
		$this->configs['autoTrigger'] = 0;
		restore_error_handler();
	}
	
	/**
	 * Logger main method
	 * @param string $level log level
	 * @param mixed $content data or message to log
	 * @param int $mode type of log method
	 */
	public function create (string $level, $content, int $mode = 0) {
		$now = date(DATE_RSS);

		$local_host = [
			'hostname' => $_SERVER['SERVER_NAME'],
			'platform' => $_SERVER['HTTP_USER_AGENT'],
			'release' => php_uname('a'),
			'userInfo' => posix_getpwuid(posix_geteuid())['name'],
			'networkInterfaces' => $_SERVER['SERVER_ADDR']
		];
	
		if ($mode & FILE && $this->configs['filePath']) { 
			$this->file($level, $content, $now);
		}
	
		if ($mode & EMAIL && $this->configs['emailOptions']) { 
			$this->email($level, $content, $now, $local_host);
		}
	
		if ($mode & HTTP && $this->configs['httpOptions']) {
			$this->http($level, $content, $now, $local_host);
		}
	}

	/**
	 * sets parameters for file writer handler
	 * @param string $path file path
	 */
	private function setFile (string $path) { 
		$this->configs['filePath'] = $path;
	}

	/**
	 * sets parameters for email sender handler
	 * @param array $configs email service configs
	 */
	private function setEmail (array $configs) {
		if (gettype($configs['port']) !== 'integer') {
			die('port must be a number');
		}

		if (gettype($configs['secure']) !== 'boolean') { 
			die('secure must be a boolean');
		}

		if (gettype($configs['user']) !== 'string') { 
			die('user must be a string');
		}

		if (gettype($configs['pass']) !== 'string') { 
			die('pass must be a string');
		}

		if (gettype($configs['from']) !== 'string') { 
			die('from must be a string');
		}

		if (gettype($configs['to']) !== 'string') { 
			die('to must be a string');
		}

		if (gettype($configs['subject']) !== 'string') { 
			die('subject must be a string');
		}
		
		$this->configs['emailOptions'] = [
			'configs' => $configs['host'],
			'port' => $configs['port'],
			'secure' => $configs['secure'],
			'user' => $configs['user'],
			'pass' => $configs['pass'],
			'from' => $configs['from'],
			'to' => $configs['to'],
			'subject' => $configs['subject']
		];
	}

	/**
	 * sets parameters for http sender handler
	 * @param array $configs host url
	 */
	private function setHttp (array $configs) {
		if (gettype($configs['host']) !== 'string') {
			die('url parameter must be a string');
		}

		if (gettype($configs['port']) !== 'number') {
			die('port parameter must be a number');
		}

		if (gettype($configs['path']) !== 'string') {
			die('path parameter must be a string');
		}

		$this->configs['httpOptions'] = [
			'host' => $configs['host'],
			'port' => $configs['port'],
			'path' => $configs['path']
		];
	}

	/**
	 * write log to file
	 * @param string $level log level
	 * @param mixed $content data or message to log
	 * @param string $now datetime to locale string
	 */
	private function file (string $level, $content, string $now) {
		//try {
			$parsed_content = json_encode($content, JSON_NUMERIC_CHECK);
			$result = file_put_contents($this->configs['filePath'], "[{$now}] {$level}: {$parsed_content}" . PHP_EOL, FILE_APPEND);
		//} catch (Exception $e){
		//	console.log("[{$now}] FILE HANDLER ERROR: {$e}");
		//}
	}

	/**
	 * send http post request to specified api
	 * @param string $level log level
	 * @param mixed $content data or message to log
	 * @param string $now datetime to locale string
	 * @param array $hostInfo local host info
	 */
	private function http (string $level, $content, string $now, array $hostInfo) {
		$curl = new CurlExtended();
		$response = $curl->postData(
			"{$this->configs['httpOptions']['host']}:{$this->configs['httpOptions']['port']}{$this->configs['httpOptions']['path']}",
			[
				'datetime' => $now,
				'level' => $level,
				'content' => $content,
				'host' => $hostInfo,
				'backtrace' => debug_backtrace()
			]
		);
	}

	/**
	 * send log via email
	 * @param string $level log level
	 * @param mixed $content data or message to log
	 * @param string $now datetime to locale string
	 * @param array $hostInfo local host info
	 */
	private function email (string $level, $content, string $now, array $hostInfo) { 
		$mail = new PHPMailer(true);                              		// Passing `true` enables exceptions
		//try {
			//Server settings
			$mail->SMTPDebug = 0;                                 		// Enable verbose debug output
			$mail->isSMTP();                                      		// Set mailer to use SMTP
			$mail->Host = $this->configs['emailOptions']['host']; 		// Specify main and backup SMTP servers
			$mail->SMTPAuth = true;                               		// Enable SMTP authentication
			$mail->Username = $this->configs['emailOptions']['user'];	// SMTP username
			$mail->Password = $this->configs['emailOptions']['pass'];	// SMTP password
			$mail->SMTPSecure = 'tls';                            		// Enable TLS encryption, `ssl` also accepted
			$mail->Port = $this->configs['emailOptions']['port'];  		// TCP port to connect to

			//Recipients
			$mail->setFrom($this->configs['emailOptions']['from']);
			$mail->addAddress($this->configs['emailOptions']['to']);     // Add a recipient

			//Attachments
			//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
			//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

			//Content
			$mail->isHTML(false);                                  // Set email format to HTML
			$mail->Subject = 'Here is the subject';
			//$mail->Body    = 'This is the HTML message body <b>in bold!</b>';
			$mail->AltBody = '--------------------------- INFO -------------------------------' . PHP_EOL
				. $now . PHP_EOL
				. '---------------------------- HOST ------------------------------' . PHP_EOL
				. json_encode($hostInfo, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT) . PHP_EOL
				. '---------------------------- ERROR ------------------------------' . PHP_EOL
				. json_encode($content, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT) . PHP_EOL
				. '---------------------------- BACKTRACE ------------------------------' . PHP_EOL
				. json_encode(debug_backtrace(), JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT);

			$mail->send();
			//echo 'Message has been sent';
		// } catch (Exception $e) {
		// 	echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
		// }
	}
}
