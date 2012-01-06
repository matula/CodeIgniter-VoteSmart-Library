<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * CodeIgniter VoteSmart API Library (http://github.com/matula/CodeIgniter-VoteSmart-Library)
 * 
 * VERSION: 1 (2010-10-06)
 * LICENSE: GNU GENERAL PUBLIC LICENSE - Version 2, June 1991
 * 
 * */
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
        $this->ci = & get_instance();
        $this->ci->load->config('votesmart');
        $this->apiserver = $this->ci->config->item('vs_apiserver');
        $this->output = $this->ci->config->item('vs_output');
        $this->key = $this->ci->config->item('vs_key');
    }

    /**
     * Set output type
     * @param type $type XML or JSON
     */
    public function set_output($type = "XML") {
        $this->output = $type;
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
        if (!empty($args)) {
            foreach ($args as $n => $v) {
                $terms .= '&' . $n . '=' . $v;
            }
        }
        $this->iface = $this->apiserver . "/" . $method . "?key=" . $this->key . "&o=" . $this->output . $terms;

        if (!$this->xml = file_get_contents($this->iface)) {
            return false;
        } else {
            if ($this->output == "JSON") {
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

    public function get_candidates_by_zip($zip) {
        $args = array('zip5' => $zip);
        return $this->query('Candidates.getByZip', $args);
    }

    public function get_officials_by_zip($zip) {
        $args = array('zip5' => $zip);
        return $this->query('Officials.getByZip', $args);
    }

    /**
     * Get a Single Candidate, with optionaql extended info, if available
     * @param int $candidate_id
     * @param bool $extended
     * @return obj 
     */
    public function get_candidate_by_id($candidate_id, $extended = false) {
        $args = array('candidateId' => $candidate_id);
        $return = $this->query('CandidateBio.getBio', $args);

        if ($return->errorMessage) {
            return FALSE;
        } else {
            if ($extended) {
                $return2 = $this->query('CandidateBio.getAddlBio', $args);
                $return->candidate->additional = $return2->additional->item;
            }
            return $return->candidate;
        }
    }

    public function get_candidate_photo($candidateId = false) {
        if ($candidateId) {
            $cand = $this->query('CandidateBio.getBio', array('candidateId' => $candidateId));
            if ($cand->errorMessage) {
                return FALSE;
            } else {
                return $cand->candidate->photo != "" ? $cand->candidate->photo : FALSE;
            }
        }
        return FALSE;
    }

    public function get_states() {
        $states = $this->query('State.getStateIDs');
        return $states->list->state;
    }

    public function get_candidates_by_office_state() {
        $candidates = $this->query('Candidates.getByOfficeState', array('officeId' => 1));
        return $candidates;
    }

    public function get_election_by_year_state($year = '2012', $stateId = 'NA') {
        $election = $this->query('Election.getElectionByYearState', array('year' => $year, 'stateId' => $stateId));
        return $election;
    }

    public function get_candidates_by_election($election_id) {
        $candidates = $this->query('Candidates.getByElection', array('electionId' => $election_id));
        return $candidates->candidate;
    }

    /**
     * Get Presidential Candidates.
     * @param bool $running - only return with status as 'Running'
     * @param string $election_id
     * @return array 
     */
    public function get_presidential_candidates($running = true, $election_id = '2323') {
        $candidates = $this->get_candidates_by_election($election_id);
        $return_candidates = array();
        $i = 0;
        foreach ($candidates as $c) {
            if ($running) {
                if ($c->electionStatus == 'Running') {
                    $return_candidates[$i] = $c;
                    $i++;
                }
            } else {
                $return_candidates[$i] = $c;
                $i++;
            }
        }
        return $return_candidates;
    }

}

/* End of file votesmart.php */
/* Location: ./application/libraries/votesmart.php */
