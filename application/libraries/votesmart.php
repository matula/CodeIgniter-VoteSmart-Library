<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter VoteSmart API Library (http://github.com/matula/CodeIgniter-VoteSmart-Library)
 * 
 * VERSION: 1 (2010-10-06)
 * LICENSE: GNU GENERAL PUBLIC LICENSE - Version 2, June 1991
 * 
 **/
class Votesmart {
        
        protected $iface;          // Interface(URL) used to gain the data
        protected $xml;            // Raw XML
        protected $xmlObj;         // SimpleXML object
        protected $ci;
        protected $apiserver;
        protected $output;
        protected $key;
        
        /**
         * function __construct
         */
        public function __construct() {
                $this->ci =& get_instance();
                $this->ci->load->config('votesmart');
                $this->apiserver = $this->ci->config->item('vs_apiserver');
                $this->output = $this->ci->config->item('vs_output');
                $this->key = $this->ci->config->item('vs_key');
        }

        /**
         * function query
         * 
         * Query API backend and return SimpleXML or JSON object.  This
         * function can be reused repeatedly
         * 
         * @param string $method CandidateBio.getBio'
         * @param array $args Array('candidateId' => '54321')
         * @return object SimpleXMLElement
         */
        public function query($method, $args = Array()) {
                $terms = "";
                if(!empty($args)) {
			foreach($args as $n => $v) {
				$terms .= '&' . $n . '=' . $v; 
			}
		}
		$this->iface = $this->apiserver . "/" . $method . "?key=" . $this->key . "&o=" . $this->output  . $terms;
		
                if (!$this->xml = file_get_contents($this->iface)) {
			return false;		
                } else {
                	if($this->output == "JSON") {
                		// If using JSON, decode it and put it into an object
                		$this->xmlObj = json_decode($this->xml); 
                        	return $this->xmlObj;	
                	} else {
                        	// Default: Use  SimpleXML to drop the whole XML
                       	 	// output into an object we can later interact with easily
                        	$this->xmlObj = new SimpleXMLElement($this->xml, LIBXML_NOCDATA); 
                        	return $this->xmlObj;	
                	}	
                }      
        }
}
/* End of file votesmart.php */
/* Location: ./application/libraries/votesmart.php */
