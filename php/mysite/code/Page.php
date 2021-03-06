<?php
class Page extends DataObject {

	private static $db = array(
	);

	private static $has_one = array(
	);

	public function getCMSFields() {
		$fields = parent::getCMSFields();
		
		return $fields;
	}

}

class Project_Controller extends Controller{

	private static $allowed_actions = array (
		'challenges','projects',"icons"
	);
	 private static $url_handlers = array(
    );

	public function init() {
		parent::init();
	}

	public function normalize($foo){
		$foo = preg_replace('/\s+/', ' ', $foo);
		$foo = strtolower(trim($foo));
		return str_replace(' ','-',$foo);
	}

	public  function projects($arguments){
		$params = $arguments->params();
		$vars = $arguments->requestVars();
		if(!array_key_exists('title', $vars)){
			// print_r('Missing project title!'.PHP_EOL);exit;
			$title = 'default';
		}else{
			$title = $vars['title'];
		}
		var_dump($params);
		if($arguments->param('ID') != NULL){
			$action = $arguments->param('ID');
			$c = $arguments->param('OtherID');
			$path = $this->normalize($title);
			$fire = new Firebase('https://challengepost.firebaseio.com/','FjQ5I3J8gkpMNeNqvmcSBtglq7qQnSc0wvjSgPgz');
			if($action == 'add'){
				//Add a project
				$d = $fire->get('challenges/'.$c.'/id');
				if($d == 'null'){
					print_r('Missing project id!'.PHP_EOL);
				}else{
					// Set project info
					$fire->set('challenges/'.$c.'/projects/'.$path.'/title',$title);
					print_r('New Project added to '.$action.' :'.$title.PHP_EOL);
					//return $this->render();
				}
			}else if ($action == 'list') {
				# code...
				$a = new ArrayList();
				$fire = new Firebase('https://challengepost.firebaseio.com/','FjQ5I3J8gkpMNeNqvmcSBtglq7qQnSc0wvjSgPgz');
				$all = $fire->get('challenges/'.$arguments->param('OtherID').'/projects');
				$all = json_decode($all,true);
				foreach ($all as $d) {
					# code...
					$a->push(new ArrayData($d));
				}
				var_dump($a->First());
			}
		}else{
			
		}
	}

	public  function icons($arguments){
		$params = $arguments->params();
		$vars = $arguments->requestVars();
		// var_dump($params);
		if(!array_key_exists('value', $vars)){
			// print_r('Missing project value!'.PHP_EOL);
		}else{
			$value = $vars['value'];
		}
		if($arguments->param('OtherID') != NULL){
			$action = $arguments->param('ID');
			$name = $arguments->param('OtherID');
			$name = $this->normalize($name);
			$fire = new Firebase('https://challengepost.firebaseio.com/','FjQ5I3J8gkpMNeNqvmcSBtglq7qQnSc0wvjSgPgz');
			if($action == 'add'){
				//Add a project
				$d = $fire->get('_icons/'.$name);
				// Set project info
				$fire->set('_icons/'.$name,$value);
				print_r('New Project added to '.$action.' :'.$name.PHP_EOL);
				//return $this->render();
			}
		}else{
			$a = new ArrayList();
			$fire = new Firebase('https://challengepost.firebaseio.com/','FjQ5I3J8gkpMNeNqvmcSBtglq7qQnSc0wvjSgPgz');
			$data = $fire->get('_icons');
			$data = json_decode($data,true);
			foreach ($data as $icon => $iconClass) {
				$a->push(new ArrayData(array('icon'=>$icon,'iconClass'=>$iconClass)));
			}
			var_dump($a->First());
		}
	}
}
class Page_Controller extends Controller {

	/**
	 * An array of actions that can be accessed via a request. Each array element should be an action name, and the
	 * permissions or conditions required to allow the user to access it.
	 *
	 * <code>
	 * array (
	 *     'action', // anyone can access this action
	 *     'action' => true, // same as above
	 *     'action' => 'ADMIN', // you must have ADMIN permissions to access this action
	 *     'action' => '->checkAction' // you can only access this action if $this->checkAction() returns true
	 * );
	 * </code>
	 *
	 * @var array
	 */
	private static $allowed_actions = array (
		'challenges','Icons'
	);
	 private static $url_handlers = array(
	 	'$ID!/$ChallengeItem/$ItemAction/$ItemValue' => 'challenges'
    );
	public function test(){
		// $f = Folder::get()->filter(array('Title'=>'backgrounds'))->First();
		// $i = Injector::inst()->get('Image');
		// $i->inject($f->myChildren()->sort('RAND()')->First());
		// var_dump($i);exit;
		// return $f->myChildren()->sort('RAND()')->First()->Link();
	}
	public function backgroundimage(){
		return SiteConfig::get()->First()->BackgroundImages()->sort('RAND()')->First()->Image();
	}

	public function init() {
		parent::init();
		$f = Folder::get()->filter(array('Title'=>'backgrounds'))->First();
		if($f){
			$this->BackgroundImages = $f->myChildren()->sort('RAND()');
			$this->TopImage = $f->myChildren()->sort('RAND()')->First();
		}
		// Note: you should use SS template require tags inside your templates 
		// instead of putting Requirements calls here.  However these are 
		// included so that our older themes still work
		Requirements::combine_files(
	    'main.challenge.js',
	    array(
	        'https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js',
	    	'https://ajax.googleapis.com/ajax/libs/angularjs/1.3.0-rc.4/angular.min.js',
	        'https://cdn.embed.ly/jquery.embedly-3.1.1.min.js',
	        'https://cdn.firebase.com/js/client/1.1.0/firebase.js',
	        'https://cdn.firebase.com/libs/angularfire/0.8.2/angularfire.min.js',
	        'http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js',
	        'themes/feedo/js/jquery.backstretch.min.js',
	        'themes/feedo/js/ripples.min.js',
	        'themes/feedo/js/material.min.js',
	        'http://fezvrasta.github.io/snackbarjs/dist/snackbar.min.js',
	        'http://cdnjs.cloudflare.com/ajax/libs/noUiSlider/6.2.0/jquery.nouislider.min.js',
	        'themes/feedo/js/main.js'
	    )
		);
	}

	public function Projects(){
		$a = new ArrayList();
		$fire = new Firebase('https://challengepost.firebaseio.com/','FjQ5I3J8gkpMNeNqvmcSBtglq7qQnSc0wvjSgPgz');
		$all = $fire->get('challenges/'.$this->PageID.'/projects');
		$all = json_decode($all,true);
		foreach ($all as $d) {
			# code...
			$a->push(new ArrayData($d));
		}
		return $a;
	}

	public function Responses(){
		$a = new ArrayList();
		$fire = new Firebase('https://challengepost.firebaseio.com/','FjQ5I3J8gkpMNeNqvmcSBtglq7qQnSc0wvjSgPgz');
		$all = $fire->get('challenges/'.$this->PageID.'/responses');
		$all = json_decode($all,true);
		foreach ($all as $d) {
			# code...
			$a->push(new ArrayData($d));
		}
		return $a;
	}

	public function Icons(){
		$a = new ArrayList();
		$fire = new Firebase('https://challengepost.firebaseio.com/','FjQ5I3J8gkpMNeNqvmcSBtglq7qQnSc0wvjSgPgz');
		$data = $fire->get('_icons');
		$data = json_decode($data,true);
		foreach ($data as $icon => $iconClass) {
			$a->push(new ArrayData(array('icon'=>$icon,'iconClass'=>$iconClass)));
		}
		return $a;
	}

	public  function challenges($arguments){
		$params = $arguments->params();
		$this->PageID = $arguments->param('ID');
		$this->ProjectID = $arguments->param('ID');
		if($arguments->param('ID') != NULL){
			//check firebase for challenge
			$id = $arguments->param('ID');
			$c = $arguments->param('OtherID');
			$fire = new Firebase('https://challengepost.firebaseio.com/','FjQ5I3J8gkpMNeNqvmcSBtglq7qQnSc0wvjSgPgz');
			if($id == 'create'){
				$fire->set('challenges/'.$c.'/id',$c);
				print_r('New Challenge '.$c.' created!'.PHP_EOL);
				return $this->render();
			}
			//Check to see if challenge exists
			$d = $fire->get('challenges/'.$id);
			// var_dump(json_decode($d));
			if($d != 'null'){
				
				$data = json_decode($d);
				//Get Challenge Details
				$this->Title = $data->title;
				$this->Tag = $data->tag;
				$this->Description = $data->description;

				//Check challenge action
				switch ($arguments->param('ChallengeItem')) {
					case 'p':
						# projects...
						Requirements::combine_files(
					    'projects.challenge.js',
					    array(
					        'themes/feedo/js/projects.js'
					    )
						);
						if($arguments->param('ItemAction')== 'add'){
							$this->AddProject = true;
						}
						return $this->renderWith(array('ChallengeProject','Page'));
						break;
					case 'r':
						# responses...
						if($arguments->param('ItemAction')){
							$a = new ArrayList();
							$this->ViewResponse = true;
							$d = $fire->get('challenges/'.$c.'/responses/'.$arguments->param('ItemAction'));
							$data = json_decode($d,true);
							foreach ($data as $g) {
								# code...
								$a->push(new ArrayData($g));
							}
							$this->Response = $a->First();
						}
						return $this->renderWith(array('ChallengeResponse','Page'));
						break;
					case 'c':
						# chats...
						if($arguments->param('ItemAction')== 'add'){
							$this->AddProject = true;

						}
						return $this->renderWith(array('ChallengeChat','Page'));
						break;
					
					default:
						# code...
						
						return $this->renderWith(array('Challenge','Page'));
						break;
				}
				
			}else{
				// Challenge not found. Inform user
				$this->Content = 'Challenge Not Found!';
				return $this->renderWith(array('Page','Page'));
			}
			//return $this->renderWith(array('Challenge','Page'));
		}
		// var_dump($params);
	}

	public function index($arguments){
		$params = $arguments->params();
		if($arguments->param('ID') != NULL){
			$this->PageID = $arguments->param('ID');
			$c = $arguments->param('ID');
			//check for page
			$fire = new Firebase('https://challengepost.firebaseio.com/','FjQ5I3J8gkpMNeNqvmcSBtglq7qQnSc0wvjSgPgz');
			$d = $fire->get('challenges/'.$c.'/id');
			// var_dump($d);

			//return $this->renderWith(array('Challenge','Page'));
		}
		// var_dump($arguments->params());
		$this->Title = 'Code for Philly Challenges';
		return $this->renderWith(array('Home','Page'));
	}

}
