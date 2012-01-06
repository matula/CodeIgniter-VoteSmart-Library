# VoteSmart API CodeIgniter Library

## Usage

+ Register for an API key: http://votesmart.org/share/api/register
+ Add your key to the votesmart config file.
+ load the library:  $this->load->library('votesmart');

## Methods

You can use any of the API calls referenced here - http://api.votesmart.org/docs/index.html 
by using the generic 'query' method.  Where the first value is the method to call, and the second is an array of parameters.

    $this->votesmart->query('CandidateBio.getBio',array('candidateId' => 9026));

There are a few methods that you can call directly from library, to make things a bit easier:

+ Candidate by zip code: $this->votesmart->get_candidates_by_zip($zip)

+ Currently elected officials by zip: $this->votesmart->get_officials_by_zip($zip)

+ Get a single candidate/official by id (with option extended info): $this->votesmart->get_candidate_by_id($candidate_id,$extended)

+ Get a candidate/official photo: $this->votesmart->get_candidate_photo($candidate_id)

+ Get a list of states and their ids: $this->votesmart->get_states()

+ Get just the Presidential Candidates (only those listed as 'Running' and 2012 election as default): 
$this->votesmart->get_presidential_candidates($running=true,$election_id='2323')