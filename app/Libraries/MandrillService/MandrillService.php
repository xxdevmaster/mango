<?php
namespace App\Libraries\MandrillService;

use App\Libraries\MandrillService\Mandrill;

class MandrillService {
	
    private $apiKey = 'zrVZzzehpLYYFcnHkvegGw';
	
	protected $Mandrill;
	
    protected $message;
	
    protected $template_name;
	
    protected $template_content;
	
	function __construct()
	{
		return $this->Mandrill = new Mandrill($this->apiKey);
	}
	
	public function sendMail($template_name, $template_content, $message)
	{
		return $MandrillStatus = $this->Mandrill->messages->sendTemplate($template_name, $template_content, $message)[0]['status'];
	}
}


