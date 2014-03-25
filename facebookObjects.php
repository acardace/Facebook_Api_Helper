<?php
require_once('facebook/facebook_login.php');

/* Making some "standard" wrappers for facebook's objects, as to incapsulate all the
 * Facebook API mechanism

 * Every Class has an ID protected property, which corresponds to its facebook ID
 */

/* Return value of the php script, to be set accordingly */
global $return_array;
$return_array = array();

Class Comment{
   public $ID;
   public $message;
   public $createdTime;
   public $likes;
   public $canComment;
   public $canRemove;
   public $fromID;
   public $fromName;
   public $tags; //Array of tagged IDs

   function __construct(){
      $this->tags = array();
   }

   public function __get($property) {
      if (property_exists($this, $property))
         return $this->$property;
   }

   public function __set($property, $value) {
      if (property_exists($this, $property))
         $this->$property = $value;
   }

}
Class Location{
   public $street;
   public $city;
   public $state;
   public $country;
   public $zip;
   public $latitude;
   public $longitude;

   public function __get($property) {
      if (property_exists($this, $property))
         return $this->$property;
   }

   public function __set($property, $value) {
      if (property_exists($this, $property))
         $this->$property = $value;
   }
}

Class FacebookObject{
   //array of parameters to be passed in the facebook APIs
   protected $params;

   //Constructor to init object field
   function __construct(){
      $this->params = array();
   }

   //Getter and setter functions
   public function __get($property) {
      if (property_exists($this, $property))
         return $this->$property;
   }

   public function __set($property, $value) {
      if (property_exists($this, $property))
         $this->$property = $value;
   }

   // Both functions are defined to manipulate the list of params
   // needed by the api facebook function, both take an array as a parameter
   public function addParams($paramArray){
      foreach($paramArray as $key=>$value){
         $this->params[$key] = $value;
      }
   }

   public function removeParams($paramArray){
      foreach($paramArray as $key=>$value){
         unset($this->params[$key]);
      }
   }

}

Class FacebookPage Extends FacebookObject{
   // Object Properties => Facebook fields
   protected $ID;
   protected $nameID;
   protected $about;
   protected $birthday;
   protected $category;
   protected $companyOverview;
   protected $checkins;
   protected $coverID;
   protected $coverSource;
   protected $currentLocation;
   protected $description;
   protected $directedBy;
   protected $link;
   protected $founded;
   protected $generalInfo;
   protected $generalManager;
   protected $likes;
   protected $mission;
   protected $name;
   protected $phone;
   protected $plotOutline;
   protected $products;
   protected $picture;
   protected $talkingAboutCount;
   protected $username;
   protected $website;
   protected $wereHereCount;
   protected $location; // Location Object!!

   //Overriding the Constructor
   function __construct($pageID){
      parent::__construct();
      $this->nameID = $pageID;
      $this->location = new Location();
   }

   //This is a wrapper function for the facebook API calls, getData will set all the
   //object properties to the return values of the API calls
   public function getData( $con = null ){
      //Making the real facebook API Call
      try{
      	$ret = $GLOBALS['facebook']->api($this->nameID,'GET');
      }
      catch(FacebookApiException $e){
      	$GLOBALS['return_array']['debug_page'] = "Nothing with the supplied ID";
      	return -1; //Error! Probably Did not find anything in Facebook with the supplied pageID
      }

      try{
      	$page_pic = $GLOBALS['facebook']->api($this->nameID.'?fields=picture.type(normal)','GET'); //separate call needed to get the profile picture
      }
      catch(FacebookApiException $e){
      	$GLOBALS['return_array']['debug_page_picture'] = "No profile picture for this ID";
      }

      //Filling up the fields

      if( is_null($con) ){
      	  $this->ID = $ret['id'];
	      $this->about = $ret['about'];
	      $this->birthday = $ret['birthday'];
	      $this->category = $ret['category'];
	      $this->companyOverview = $ret['company_overview'];
	      $this->coverID = $ret['cover']['cover_id'];
	      $this->coverSource = $ret['cover']['source'];
	      $this->currentLocation = $ret['current_location'];
	      $this->description = $ret['description'];
	      $this->directedBy = $ret['directed_by'];
	      $this->founded = $ret['founded'];
	      $this->generalInfo = $ret['general_info'];
	      $this->generalManager = $ret['general_manager'];
	      $this->link = $ret['link'];
	      $this->likes = $ret['likes'];
	      $this->mission = $ret['mission'];
	      $this->name = $ret['name'];
	      $this->phone = $ret['phone'];
	      $this->plotOutline = $ret['plot_outline'];
	      $this->picture = $page_pic['picture']['data']['url'];
	      $this->products = $ret['products'];
	      $this->talkingAboutCount = $ret['talking_about_count'];
	      $this->username = $ret['username'];
	      $this->website = $ret['website'];
	      $this->wereHereCount = $ret['were_here_count'];
	      $this->location->__set(street, $ret['location']['street']);
	      $this->location->__set(city, $ret['location']['city']);
	      $this->location->__set(state, $ret['location']['state']);
	      $this->location->__set(country, $ret['location']['country']);
	      $this->location->__set(zip,$ret['location']['zip'] );
	      $this->location->__set(latitude, $ret['location']['latitude']);
	      $this->location->__set(longitude, $ret['location']['longitude']);
      }
      else{ //if $con is defined we're making all the fields "Sql Friendly"
      	  $this->ID = $ret['id'];
	      $this->about = mysqli_real_escape_string( $con, $ret['about'] );
	      $this->birthday = $ret['birthday'];
	      $this->category = mysqli_real_escape_string( $con, $ret['category']);
	      $this->companyOverview = mysqli_real_escape_string( $con, $ret['company_overview'] );
	      $this->chekins = $ret['checkins'];
	      $this->coverID = $ret['cover']['cover_id'];
	      $this->coverSource = $ret['cover']['source'];
	      $this->currentLocation = $ret['current_location'];
	      $this->description = mysqli_real_escape_string( $con, $ret['description'] );
	      $this->directedBy = mysqli_real_escape_string( $con, $ret['directed_by'] );
	      $this->founded = mysqli_real_escape_string( $con, $ret['founded'] );
	      $this->generalInfo = mysqli_real_escape_string( $con, $ret['general_info'] );
	      $this->generalManager = mysqli_real_escape_string( $con, $ret['general_manager'] );
	      $this->link = mysqli_real_escape_string( $con, $ret['link'] );
	      $this->likes = $ret['likes'];
	      $this->mission = mysqli_real_escape_string( $con, $ret['mission'] );
	      $this->name = mysqli_real_escape_string( $con, $ret['name'] );
	      $this->phone = $ret['phone'];
	      $this->plotOutline = mysqli_real_escape_string( $con, $ret['plot_outline'] );
	      $this->picture = $page_pic['picture']['data']['url'];
	      $this->products = mysqli_real_escape_string( $con, $ret['products'] );
	      $this->talkingAboutCount = $ret['talking_about_count'];
	      $this->username = mysqli_real_escape_string( $con, $ret['username'] );
	      $this->website = mysqli_real_escape_string( $con, $ret['website'] );
	      $this->wereHereCount = $ret['were_here_count'];
	      $this->location->__set(street, mysqli_real_escape_string( $con, $ret['location']['street'] ) );
	      $this->location->__set(city, mysqli_real_escape_string( $con, $ret['location']['city'] ) );
	      $this->location->__set(state, mysqli_real_escape_string( $con, $ret['location']['state']) ) ;
	      $this->location->__set(country, mysqli_real_escape_string( $con, $ret['location']['country'] ) );
	      $this->location->__set(zip,$ret['location']['zip'] );
	      $this->location->__set(latitude, $ret['location']['latitude']);
	      $this->location->__set(longitude, $ret['location']['longitude']);
      }
      return 0;
   }

}

Class FacebookEvent Extends FacebookObject{
   //Properties
   protected $eventArray; //container array to have many associative ones
   protected $pageID;
   protected $nodeParams;
   protected $arrayLength;

  /* structure of every array contained in eventArray
    'ID' => $ID,
    'description' => $description,
    'startTime' => $startTime,
    'endTime' => $endTime,
    'updatedTime' => $updatedTime,
    'name' => $name,
    'ownerName' => $ownerName,
    'ownerID' => $ownerID,
    'picture' => $picture,
    'privacy' => $privacy,
    'ticketURI' => $ticketURI,
    'locationName' => $locationName,
    'location' => $location
   */

   //Overriding the Constructor
   function __construct($pageid){
      //params needed to get all the fields
      parent::__construct();
      $this->nodeParams = '?fields=events.fields(privacy,end_time,description,owner,name,start_time,id,ticket_uri,updated_time,venue,location,picture)';
      $this->eventArray = array();
      $this->pageID = $pageid;
      $this->arrayLength = 0;
   }

   function __get( $property=null ){ //call with no arguments to get the array
      if( property_exists( $this,$property) )
         return $this->$property;
      else
         return $this->eventArray;
   }

   public function getData( $con=null ){
      $this->arrayLength = 0; //to clear out
      $node = $this->pageID.$this->nodeParams;

      try{
      	$ret = $GLOBALS['facebook']->api($node,'GET',$this->params);
      }
      catch(FacebookApiException $e){
      	return -1;
      }


      if( is_null($con) && is_array($ret['events']['data'] )){
	      foreach( $ret['events']['data'] as $iter ){
	         $fields = array(); //inner array

	         $fields['ID'] = $iter['id'];
	         $fields['description'] = $iter['description'];
	         $fields['startTime'] = date( 'Y-m-d H:i:s', strtotime( $iter['start_time'] ) );
	         $fields['endTime'] = date( 'Y-m-d H:i:s', strtotime( $iter['end_time'] ) );
	         $fields['updatedTime'] = date( 'Y-m-d H:i:s', strtotime( $iter['updated_time'] ) );
	         $fields['name'] = $iter['name'];
	         $fields['ownerName'] = $iter['owner']['name'];
	         $fields['ownerID'] = $iter['owner']['id'];
	         $fields['picture'] = $iter['picture']['data']['url'];
	         $fields['privacy'] = $iter['privacy'];
	         $fields['ticketURI'] = $iter['ticket_uri'];
	         $fields['locationName'] = $iter['location'];
	         $fields['location'] = new Location();
	         $fields['location']->__set(street, $iter['venue']['street']);
	         $fields['location']->__set(city, $iter['venue']['city']);
	         $fields['location']->__set(state, $iter['venue']['state']);
	         $fields['location']->__set(country, $iter['venue']['country']);
	         $fields['location']->__set(zip,$iter['venue']['zip'] );
	         $fields['location']->__set(latitude, $iter['venue']['latitude']);
	         $fields['location']->__set(longitude, $iter['venue']['longitude']);

	         $this->eventArray[$this->arrayLength] = $fields;
	         $this->arrayLength++;
	      }
      }elseif( is_array($ret['events']['data']) ){
		  foreach( $ret['events']['data'] as $iter ){
	         $fields = array(); //inner array

	         $fields['ID'] = $iter['id'];
	         $fields['description'] = mysqli_real_escape_string( $con, $iter['description']  );
	         $fields['startTime'] = date( 'Y-m-d H:i:s', strtotime( $iter['start_time'] ) );
	         $fields['endTime'] = date( 'Y-m-d H:i:s', strtotime( $iter['end_time'] ) );
	         $fields['updatedTime'] = date( 'Y-m-d H:i:s', strtotime( $iter['updated_time'] ) );
	         $fields['name'] = mysqli_real_escape_string( $con, $iter['name'] );
	         $fields['ownerName'] = mysqli_real_escape_string( $con, $iter['owner']['name'] );
	         $fields['ownerID'] = $iter['owner']['id'];
	         $fields['picture'] = $iter['picture']['data']['url'];
	         $fields['privacy'] = $iter['privacy'];
	         $fields['ticketURI'] = $iter['ticket_uri'];
	         $fields['locationName'] = mysqli_real_escape_string( $con, $iter['location'] );
	         $fields['location'] = new Location();
	         $fields['location']->__set(street, mysqli_real_escape_string( $con, $iter['venue']['street'] ) );
	         $fields['location']->__set(city, mysqli_real_escape_string( $con, $iter['venue']['city']) );
	         $fields['location']->__set(state, mysqli_real_escape_string( $con, $iter['venue']['state'] ));
	         $fields['location']->__set(country, mysqli_real_escape_string( $con, $iter['venue']['country'] ));
	         $fields['location']->__set(zip,$iter['venue']['zip'] );
	         $fields['location']->__set(latitude, $iter['venue']['latitude']);
	         $fields['location']->__set(longitude, $iter['venue']['longitude']);

	         $this->eventArray[$this->arrayLength] = $fields;
	         $this->arrayLength++;
		  }
      }else{
      	$GLOBALS['return_array']['debug_event'] = "No Events for this page";
      	return -1; //nothing was found
      }

     // everything was fine enough
     return 0;
   }
}

Class FacebookAlbum Extends FacebookObject{
   //Properties
   protected $albumsArray; //container array to have many associative ones
   protected $arrayLength;
   protected $pageID;
   protected $nodeParams;

   /* structure of every array contained in eventArray
    'ID' => $ID,
    'description' => $description,
    'createdTime' => $createdTime,
    'updatedTime' => $updatedTime,
    'name' => $name,
    'photosNumber' => $photosNumber,
    'coverPhoto' => $coverPhoto,
    'link' => $link,
    'likes' => $likes,
    'privacy' => $privacy,
    'albumtType' => $albumType,
    'locationName' => $locationName,
    */

   //Overriding the Constructor
   function __construct($pageid){
      parent::__construct();
      //params needed to get all the fields
      $this->nodeParams = '?fields=albums.fields(id,count,created_time,cover_photo,description,type,updated_time,likes,link,name,location,privacy)';
      $this->albumsArray = array();
      $this->pageID = $pageid;
      $this->arrayLength = 0;
   }

   function __get( $property=null ){ //call with no arguments to get the array
      if( property_exists( $this,$property) )
         return $this->$property;
      else
         return $this->albumsArray;
   }

   public function getData( $con = null ){
      $this->arrayLength = 0; //to clear out
      $node = $this->pageID.$this->nodeParams;

      try{
      	$ret = $GLOBALS['facebook']->api($node,'GET');
      }
      catch(FacebookApiException $e){
      	return -1;
      }

      if( is_null($con) && is_array($ret['albums']['data'] )){
	      foreach( $ret['albums']['data'] as $iter ){
	         $fields = array(); //inner array

	         $fields['ID'] = $iter['id'];
	         $fields['description'] = $iter['description'];
	         $fields['createdTime'] = date( 'Y-m-d H:i:s', strtotime( $iter['created_time'] ) );
	         $fields['updatedTime'] = date( 'Y-m-d H:i:s', strtotime( $iter['updated_time'] ) );
	         $fields['name'] = $iter['name'];
	         $fields['photosNumber'] = $iter['count'];
	         $fields['coverPhoto'] = $iter['cover_photo'];
	         $fields['link'] = $iter['link'];
	         $fields['likes'] = count( $iter['likes'] );
	         $fields['privacy'] = $iter['privacy'];
	         $fields['albumType'] = $iter['type'];
	         $fields['locationName'] = $iter['location'];

	         $this->albumsArray[$this->arrayLength] = $fields;
	         $this->arrayLength++;
	      }
      }elseif( is_array($ret['albums']['data'] ) ){
	      foreach( $ret['albums']['data'] as $iter ){
	         $fields = array(); //inner array

	         $fields['ID'] = $iter['id'];
	         $fields['description'] = mysqli_real_escape_string( $con, $iter['description'] );
	         $fields['createdTime'] = date( 'Y-m-d H:i:s', strtotime( $iter['created_time'] ) );
	         $fields['updatedTime'] = date( 'Y-m-d H:i:s', strtotime( $iter['updated_time'] ) );
	         $fields['name'] = mysqli_real_escape_string( $con, $iter['name'] );
	         $fields['photosNumber'] = $iter['count'];
	         $fields['coverPhoto'] = $iter['cover_photo'];
	         $fields['link'] = $iter['link'];
	         $fields['likes'] = count( $iter['likes'] );
	         $fields['privacy'] = $iter['privacy'];
	         $fields['albumType'] = $iter['type'];
	         $fields['locationName'] = mysqli_real_escape_string( $con, $iter['location'] );

	         $this->albumsArray[$this->arrayLength] = $fields;
	         $this->arrayLength++;
	      }
      }else{
	    $GLOBALS['return_array']['debug_album'] = "No Albums for this page";
      	return -1; //nothing was found
      }
      return 0;
   }

}
Class FacebookPhoto Extends FacebookObject{
   //Properties
   protected $albumID;
   protected $photosArray; //container array to have many associative ones
   protected $arrayLength;

   /* structure of every array contained in photosArray
    'ID' => $ID,
    'name' => $name,
    'createdTime' => $createdTime,
    'updatedTime' => $updatedTime,
    'name' => $name,
    'source' => $source,
    'height' => $height,
    'width' => $width,
    'icon' => $icon,
    'link' => $link,
    'small/middle/largeSource' => $small/middle/largeSource,
    'small/middle/largeWidth' => $small/middle/largeWidth,
    'small/middle/largeHeight' => $small/middle/largeHeight,
    'placeID' => $placeID,
    'placeName' => $placeName,
    'Location' => $location (Location Object),
    'tags' => $tags (array of tagged IDs),
    'comments' => $comments ( array of comments)
    */

   //Overriding the Constructor
   function __construct($albumid=null){
      parent::__construct();
      //params needed to get all the fields
      $this->photosArray = array();
      if( !is_null($albumid) )
      	$this->albumID = $albumid;
      else
      	$this->albumID = null;
      $this->arrayLength = 0;
   }

   function __get( $property=null ){ //call with no arguments to get the array
      if( property_exists( $this,$property) )
         return $this->$property;
      else
         return $this->photosArray;
   }

   public function getData($con = null, $limit = null){
   	  if( is_null($this->albumID) ){
   	  	$GLOBALS['return_array']['debug_album_not_defined']= 'No albumID was defined, first call the __set() method';
   	  	return -1;
   	  }

      if(!is_null( $limit ) ){
         $opt = array(
            'limit' => $limit
         );
         $this->addParams($opt);
      }
      $this->arrayLength = 0; //to clear out
      $node = $this->albumID.'/photos';

      try{
      	$ret = $GLOBALS['facebook']->api($node,'GET',$this->params);
      }
      catch(FacebookApiException $e){
      	$GLOBALS['return_array']['debug_photos'] = 'Error occured looking for photos';
      	return -1;
      }

      if( is_null($con) && is_array($ret['data']) ){
	      foreach( $ret['data'] as $iter ){
	         $fields = array(); //inner array

	         $fields['ID'] = $iter['id'];
	         $fields['createdTime'] = date( 'Y-m-d H:i:s', strtotime( $iter['created_time'] ));
	         $fields['updatedTime'] = date( 'Y-m-d H:i:s', strtotime( $iter['updated_time'] ));
	         $fields['icon'] = $iter['icon'];
	         $fields['name'] = $iter['name'];
	         $fields['height'] = $iter['height'];
	         $fields['width'] = $iter['width'];
	         $fields['link'] = $iter['link'];
	         $fields['source'] = $iter['source'];
	         $fields['likes'] = count( $iter['likes']['data'] );
	         $fields['placeID'] = $iter['place']['id'];
	         $fields['placeName'] = $iter['place']['name'];

	         $fields['location'] = new Location();
	         $fields['location']->__set(street, $iter['place']['location']['street']);
	         $fields['location']->__set(city, $iter['place']['location']['city']);
	         $fields['location']->__set(state, $iter['place']['location']['state']);
	         $fields['location']->__set(country, $iter['place']['location']['country']);
	         $fields['location']->__set(zip,$iter['place']['location']['zip'] );
	         $fields['location']->__set(latitude, $iter['place']['location']['latitude']);
	         $fields['location']->__set(longitude, $iter['place']['location']['longitude']);

	         $fields['largeSource'] = $iter['images'][0]['source'];
	         $fields['largeHeight'] = $iter['images'][0]['height'];
	         $fields['largeWidth'] = $iter['images'][0]['width'];
	         $fields['middleSource'] = $iter['images'][1]['source'];
	         $fields['middleHeight'] = $iter['images'][1]['height'];
	         $fields['middleWidth'] = $iter['images'][1]['width'];
	         $fields['smallSource'] = $iter['images'][2]['source'];
	         $fields['smallHeight'] = $iter['images'][2]['height'];
	         $fields['smallWidth'] = $iter['images'][2]['width'];

	         //Collecting Tags in an array
	         if( is_array( $iter['tags']['data'] ) ){
	            $fields['tags'] = array();
	            $i = 0; //array length
	            foreach( $iter['tags']['data'] as $tagArray ){
	               $fields['tags'][$i] = $tagArray['id'];
	               $i++;
	            }
	         }

	         //Collecting Comments
	         if( is_array( $iter['comments']['data'] ) ){
	            $fields['comments'] = array(); //We will create an array of Comment objects
	            $i = 0;
	            foreach( $iter['comments']['data'] as $commArray ){
	               $comment  = new Comment();

	               $comment->__set(ID, $commArray['id'] );
	               $comment->__set(message, $commArray['message'] );
	               $comment->__set(createdTime, date( 'Y-m-d H:i:s', strtotime( $commArray['created_time'] ) ) );
	               $comment->__set(likes, $commArray['like_count'] );
	               if( $commArray['can_comment'] )
	                  $comment->__set(canComment, 0 );
	               else
	                  $comment->__set(canComment, 1 );
	               if( $commArray['can_remove'])
	                  $comment->__set(canRemove, 0 );
	               else
	                  $comment->__set(canRemove, 1  );
	               $comment->__set(fromID, $commArray['from']['id'] );
	               $comment->__set(fromName, $commArray['from']['name'] );

	               //$Comment->tags is an array as well
	               if( is_array( $comArray['message_tags'] ) ){
	                  $tags = array();
	                  $k = 0;
	                  foreach( $comArray['message_tags'] as $tagIter ){
	                     $tags[$k] = $tagIter['id'];
	                     $k++;
	                  }
	                  $comment->__set(tags, $tags );
	               }

	               $fields['comments'][$i] = $comment;
	               $i++;
	            }
	         }

	         $this->photosArray[$this->arrayLength] = $fields;
	         $this->arrayLength++;
	    }
      }elseif( is_array($ret['data']) ){ //$con is defined so we make the fields Sql friendly!
	  	 foreach( $ret['data'] as $iter ){
	         $fields = array(); //inner array

	         $fields['ID'] = $iter['id'];
	         $fields['createdTime'] = date( 'Y-m-d H:i:s', strtotime( $iter['created_time'] ));
	         $fields['updatedTime'] = date( 'Y-m-d H:i:s', strtotime( $iter['updated_time'] ));
	         $fields['icon'] = $iter['icon'];
	         $fields['name'] = mysqli_real_escape_string( $con, $iter['name'] );
	         $fields['height'] = $iter['height'];
	         $fields['width'] = $iter['width'];
	         $fields['link'] = $iter['link'];
	         $fields['source'] = $iter['source'];
	         $fields['likes'] = count( $iter['likes']['data'] );
	         $fields['placeID'] = $iter['place']['id'];
	         $fields['placeName'] = mysqli_real_escape_string( $con, $iter['place']['name'] );

	         $fields['location'] = new Location();
	         $fields['location']->__set(street, mysqli_real_escape_string( $con, $iter['place']['location']['street'] ) );
	         $fields['location']->__set(city, mysqli_real_escape_string( $con, $iter['place']['location']['city']) );
	         $fields['location']->__set(state, mysqli_real_escape_string( $con, $iter['place']['location']['state'] ) );
	         $fields['location']->__set(country, mysqli_real_escape_string( $con, $iter['place']['location']['country']) );
	         $fields['location']->__set(zip,$iter['place']['location']['zip'] );
	         $fields['location']->__set(latitude, $iter['place']['location']['latitude']);
	         $fields['location']->__set(longitude, $iter['place']['location']['longitude']);

	         $fields['largeSource'] = $iter['images'][0]['source'];
	         $fields['largeHeight'] = $iter['images'][0]['height'];
	         $fields['largeWidth'] = $iter['images'][0]['width'];
	         $fields['middleSource'] = $iter['images'][1]['source'];
	         $fields['middleHeight'] = $iter['images'][1]['height'];
	         $fields['middleWidth'] = $iter['images'][1]['width'];
	         $fields['smallSource'] = $iter['images'][2]['source'];
	         $fields['smallHeight'] = $iter['images'][2]['height'];
	         $fields['smallWidth'] = $iter['images'][2]['width'];

	         //Collecting Tags in an array
	         if( is_array( $iter['name_tags'] ) ){
	            $fields['tags'] = array();
	            $i = 0; //array length
	            foreach( $iter['name_tags'] as $tagArray ){
	               $fields['tags'][$i] = $tagArray[0]['id'];
	               $i++;
	            }
	         }

	         //Collecting Comments
	         if( is_array( $iter['comments']['data'] ) ){
	            $fields['comments'] = array(); //We will create an array of Comment objects
	            $i = 0;
	            foreach( $iter['comments']['data'] as $commArray ){
	               $comment  = new Comment();

	               $comment->__set(ID, $commArray['id'] );
	               $comment->__set(message, mysqli_real_escape_string( $con, $commArray['message'] ) );
	               $comment->__set(createdTime, date( 'Y-m-d H:i:s', strtotime( $commArray['created_time'] ) ) );
	               $comment->__set(likes, $commArray['like_count'] );
	               if( $commArray['can_comment'] )
	                  $comment->__set(canComment, 0 );
	               else
	                  $comment->__set(canComment, 1 );
	               if( $commArray['can_remove'])
	                  $comment->__set(canRemove, 0 );
	               else
	                  $comment->__set(canRemove, 1  );
	               $comment->__set(fromID, $commArray['from']['id'] );
	               $comment->__set(fromName, mysqli_real_escape_string( $con, $commArray['from']['name'] ) );

	               //$Comment->tags is an array as well
	               if( is_array( $comArray['message_tags'] ) ){
	                  $tags = array();
	                  $k = 0;
	                  foreach( $comArray['message_tags'] as $tagIter ){
	                     $tags[$k] = $tagIter['id'];
	                     $k++;
	                  }
	                  $comment->__set(tags, $tags );
	               }

	               $fields['comments'][$i] = $comment;
	               $i++;
	            }
	         }

	         $this->photosArray[$this->arrayLength] = $fields;
	         $this->arrayLength++;
	   }
      }else{
	      $GLOBALS['return_array']['debug_photos_empty_album'] = 'array of photos not present, maybe empty Album?';
	      return -1;
      }
      return 0;
   }
}

Class FacebookVideo Extends FacebookObject{
   protected $pageID;
   protected $videosArray;
   protected $arrayLength;

   /* structure of every array contained in videosArray
      'ID' => $ID,
      'name' => $name,
      'createdTime' => $createdTime,
      'updatedTime' => $updatedTime,
      'source' => $source,
      'picture' => $picture,
      'embedHtml' => $embedHtml,
      'likes' => $likes,
      'icon' => $icon,
      'tags' => $tags (array of tagged IDs),
      'comments' => $comments ( array of comments)
    */

   function __construct($pageid){
      parent::__construct();
      $this->pageID = $pageid;
      $this->videosArray = array();
      $this->arrayLength = 0;
   }

   function __get( $property=null ){ //call with no arguments to get the array
      if( property_exists( $this,$property) )
         return $this->$property;
      else
         return $this->videosArray;
   }

   public function getData( $con=null, $limit = null ){
      if(!is_null( $limit ) ){
         $opt = array(
            'limit' => $limit
         );
         $this->addParams($opt);
      }
      $this->arrayLength = 0; //to clear out
      $node = $this->pageID.'/videos';

      try{
      	$ret = $GLOBALS['facebook']->api($node,'GET',$this->params);
      }
      catch(FacebookApiException $e){
      	$GLOBALS['return_array']['debug_video'] ="Error occured while looking for videos uploaded by this page";
      	//return -1;
      }

      if( is_null($con) && is_array($ret['data']) ){
	      foreach( $ret['data'] as $iter ){
	         $fields = array(); //inner array

	         $fields['ID'] = $iter['id'];
	         $fields['createdTime'] = date( 'Y-m-d H:i:s', strtotime( $iter['created_time'] ) );
	         $fields['updatedTime'] = date( 'Y-m-d H:i:s', strtotime( $iter['updated_time'] ) );
	         $fields['icon'] = $iter['icon'];
	         $fields['name'] = $iter['name'];
	         $fields['description'] = $iter['description'];
	         $fields['picture'] = $iter['picture'];
	         $fields['source'] = $iter['source'];
	         $fields['likes'] = count( $iter['likes']['data'] );
	         $fields['embedHtml'] = $iter['embed_html'];

	         //Collecting Tags in an array
	         if( is_array( $iter['tags']['data'] ) ){
	            $fields['tags'] = array();
	            $i = 0; //array length
	            foreach( $iter['tags']['data'] as $tagArray ){
	               $fields['tags'][$i] = $tagArray['id'];
	               $i++;
	            }
	         }

	         //Collecting Comments
	         if( is_array( $iter['comments']['data'] ) ){
	            $fields['comments'] = array(); //We will create an array of Comment objects
	            $i = 0;
	            foreach( $iter['comments']['data'] as $commArray ){
	               $comment  = new Comment();

	               $comment->__set(ID, $commArray['id'] );
	               $comment->__set(message, $commArray['message'] );
	               $comment->__set(createdTime, date( 'Y-m-d H:i:s', strtotime( $commArray['created_time'] ) ) );
	               $comment->__set(likes, $commArray['like_count'] );
	               if( $commArray['can_comment'] )
	                  $comment->__set(canComment, 0 );
	               else
	                  $comment->__set(canComment, 1 );
	               if( $commArray['can_remove'])
	                  $comment->__set(canRemove, 0 );
	               else
	                  $comment->__set(canRemove, 1  );
	               $comment->__set(fromID, $commArray['from']['id'] );
	               $comment->__set(fromName, $commArray['from']['name'] );

	               //$Comment->tags is an array as well
	               if( is_array( $comArray['message_tags'] ) ){
	                  $tags = array();
	                  $k = 0;
	                  foreach( $comArray['message_tags'] as $tagIter ){
	                     $tags[$k] = $tagIter['id'];
	                     $k++;
	                  }
	                  $comment->__set(tags, $tags );
	               }
	               $fields['comments'][$i] = $comment;
	               $i++;
	            }
	         }
	         $this->videosArray[$this->arrayLength] = $fields;
	         $this->arrayLength++;
	      }
      }elseif( is_array($ret['data']) ){ //$con is defined so we make the fields Sql friendly!
      	foreach( $ret['data'] as $iter ){
	         $fields = array(); //inner array

	         $fields['ID'] = $iter['id'];
	         $fields['createdTime'] = date( 'Y-m-d H:i:s', strtotime( $iter['created_time'] ) );
	         $fields['updatedTime'] = date( 'Y-m-d H:i:s', strtotime( $iter['updated_time'] ) );
	         $fields['icon'] = $iter['icon'];
	         $fields['name'] = mysqli_real_escape_string( $con, $iter['name'] );
	         $fields['description'] = mysqli_real_escape_string( $con, $iter['description'] );
	         $fields['picture'] = $iter['picture'];
	         $fields['source'] = $iter['source'];
	         $fields['likes'] = count( $iter['likes']['data'] );
	         $fields['embedHtml'] = $iter['embed_html'];

	         //Collecting Tags in an array
	         if( is_array( $iter['tags']['data'] ) ){
	            $fields['tags'] = array();
	            $i = 0; //array length
	            foreach( $iter['tags']['data'] as $tagArray ){
	               $fields['tags'][$i] = $tagArray['id'];
	               $i++;
	            }
	         }

	         //Collecting Comments
	         if( is_array( $iter['comments']['data'] ) ){
	            $fields['comments'] = array(); //We will create an array of Comment objects
	            $i = 0;
	            foreach( $iter['comments']['data'] as $commArray ){
	               $comment  = new Comment();

	               $comment->__set(ID, $commArray['id'] );
	               $comment->__set(message, mysqli_real_escape_string( $con, $commArray['message'] ) );
	               $comment->__set(createdTime, date( 'Y-m-d H:i:s', strtotime( $commArray['created_time'] ) ) );
	               $comment->__set(likes, $commArray['like_count'] );
	               if( $commArray['can_comment'] )
	                  $comment->__set(canComment, 0 );
	               else
	                  $comment->__set(canComment, 1 );
	               if( $commArray['can_remove'])
	                  $comment->__set(canRemove, 0 );
	               else
	                  $comment->__set(canRemove, 1  );
	               $comment->__set(fromID, $commArray['from']['id'] );
	               $comment->__set(fromName, mysqli_real_escape_string( $con, $commArray['from']['name'] ) );

	               //$Comment->tags is an array as well
	               if( is_array( $comArray['message_tags'] ) ){
	                  $tags = array();
	                  $k = 0;
	                  foreach( $comArray['message_tags'] as $tagIter ){
	                     $tags[$k] = $tagIter['id'];
	                     $k++;
	                  }
	                  $comment->__set(tags, $tags );
	               }
	               $fields['comments'][$i] = $comment;
	               $i++;
	            }
	         }
	         $this->videosArray[$this->arrayLength] = $fields;
	         $this->arrayLength++;
	     }
      }else{
	      $GLOBALS['return_array']['debug_video_array'] = 'Array of videos not present';
	      return -1;
      }
      return 0;
   }
}

Class FacebookWall Extends FacebookObject{
   protected $pageID;
   protected $postsArray;
   protected $arrayLength;

   /* structure of every array contained in postsArray
      'ID' => $ID,
      'name' => $name,
      'createdTime' => $createdTime,
      'updatedTime' => $updatedTime,
      'shares' => $shares,
      'picture' => $picture,
      'likes' => $likes,
      'icon' => $icon,
      'tags' => $tags (array of tagged IDs),
      'comments' => $comments ( array of comments)
      'appID' => $appid, //ID of the app which posted
      'appName' => $appName,
      'message' => $message,
      'caption' => $caption,
      'description' => $description,
      'privacy' => $privacy,
      'placeID' => $placeID,
      'placeName' => $placeName,
      'location' => $location
      'shares' => $shares,
      'statusType' => $statusType,
      'Story' => $story,
      'postType' => $postType
    */

   function __construct($pageid){
      parent::__construct();
      $this->pageID = $pageid;
      $this->postsArray = array();
      $this->arrayLength = 0;
   }

   function __get( $property=null ){ //call with no arguments to get the array
      if( property_exists( $this,$property) )
         return $this->$property;
      else
         return $this->postsArray;
   }

   public function getData( $con=null, $limit = null ){
      if(!is_null( $limit ) ){
         $opt = array(
            'limit' => $limit
         );
         $this->addParams($opt);
      }
      $this->arrayLength = 0; //to clear out
      $node = $this->pageID.'/posts';

      try{
      	$ret = $GLOBALS['facebook']->api($node,'GET',$this->params);
      }
      catch(FacebookApiException $e){
      	$GLOBALS['return_array']['debug_wall'] = "Error occured while looking for the Wall of the page";
      	return -1;
      }


      if( is_null($con) && is_array($ret['data']) ){
	      foreach( $ret['data'] as $iter ){
	         $fields = array(); //inner array

	         $fields['ID'] = $iter['id'];
	         $fields['appName'] = $iter['application']['name'];
	         $fields['appID'] = $iter['application']['id'];
	         $fields['createdTime'] = date( 'Y-m-d H:i:s', strtotime( $iter['created_time'] ) );
	         $fields['updatedTime'] = date( 'Y-m-d H:i:s', strtotime( $iter['updated_time'] ) );
	         $fields['icon'] = $iter['icon'];
	         $fields['name'] = $iter['name'];
	         $fields['caption'] = $iter['caption'];
	         $fields['description'] = $iter['description'];
	         $fields['picture'] = $iter['picture'];
	         $fields['likes'] = count( $iter['likes']['data'] );
	         $fields['message'] = $iter['message'];
	         $fields['shares'] = $iter['shares']['count'];
	         $fields['statusType'] = $iter['status_type'];
	         $fields['story'] = $iter['story'];
	         $fields['postType'] = $iter['type'];
	         $fields['privacy'] = $iter['privacy']['value'];
	         $fields['link'] = $iter['link'];

	         $fields['placeID'] = $iter['place']['id'];
	         $fields['placeName'] = $iter['place']['name'];
	         $fields['location'] = new Location();
	         $fields['location']->__set(street, $iter['place']['location']['street']);
	         $fields['location']->__set(city, $iter['place']['location']['city']);
	         $fields['location']->__set(state, $iter['place']['location']['state']);
	         $fields['location']->__set(country, $iter['place']['location']['country']);
	         $fields['location']->__set(zip,$iter['place']['location']['zip'] );
	         $fields['location']->__set(latitude, $iter['place']['location']['latitude']);
	         $fields['location']->__set(longitude, $iter['place']['location']['longitude']);

	         //Collecting Comments
	         if( is_array( $iter['comments']['data'] ) ){
	            $fields['comments'] = array(); //We will create an array of Comment objects
	            $i = 0;
	            foreach( $iter['comments']['data'] as $commArray ){
	               $comment  = new Comment();

	               $comment->__set(ID, $commArray['id'] );
	               $comment->__set(message, $commArray['message'] );
	               $comment->__set(createdTime, date( 'Y-m-d H:i:s', strtotime( $commArray['created_time']) ) );
	               $comment->__set(likes, $commArray['like_count'] );
	               if( $commArray['can_comment'] )
	                  $comment->__set(canComment, 0 );
	               else
	                  $comment->__set(canComment, 1 );
	               if( $commArray['can_remove'])
	                  $comment->__set(canRemove, 0 );
	               else
	                  $comment->__set(canRemove, 1  );
	               $comment->__set(fromID, $commArray['from']['id'] );
	               $comment->__set(fromName, $commArray['from']['name'] );

	               //$Comment->tags is an array as well
	               if( is_array( $comArray['message_tags'] ) ){
	                  $tags = array();
	                  $k = 0;
	                  foreach( $comArray['message_tags'] as $tagIter ){
	                     $tags[$k] = $tagIter['id'];
	                     $k++;
	                  }
	                  $comment->__set(tags, $tags );
	               }
	               $fields['comments'][$i] = $comment;
	               $i++;
	            }
	         }

	         //Collecting Tags in an array
	         if( is_array( $iter['to']['data'] ) ){
	            $fields['tags'] = array();
	            $i = 0; //array length
	            foreach( $iter['to']['data'] as $tagArray ){
	               $fields['tags'][$i] = $tagArray['id'];
	               $i++;
	            }
	         }


	         $this->postsArray[$this->arrayLength] = $fields;
	         $this->arrayLength++;
	      }
	  }elseif( is_array($ret['data']) ){ //$con is defined so we make the fields Sql friendly!
	  	foreach( $ret['data'] as $iter ){
	         $fields = array(); //inner array

	         $fields['ID'] = $iter['id'];
	         $fields['appName'] = $iter['application']['name'];
	         $fields['appID'] = $iter['application']['id'];
	         $fields['createdTime'] = date( 'Y-m-d H:i:s', strtotime( $iter['created_time'] ) );
	         $fields['updatedTime'] = date( 'Y-m-d H:i:s', strtotime( $iter['updated_time'] ) );
	         $fields['icon'] = $iter['icon'];
	         $fields['name'] = mysqli_real_escape_string( $con, $iter['name']);
	         $fields['caption'] = mysqli_real_escape_string( $con, $iter['caption']);
	         $fields['description'] = mysqli_real_escape_string( $con, $iter['description']);
	         $fields['picture'] = $iter['picture'];
	         $fields['likes'] = count( $iter['likes']['data'] );
	         $fields['message'] = mysqli_real_escape_string( $con, $iter['message']);
	         $fields['shares'] = ($iter['shares']['count'] == ""  ? 0 : $iter['shares']['count']);
	         $fields['statusType'] = $iter['status_type'];
	         $fields['story'] = mysqli_real_escape_string( $con, $iter['story'] );
	         $fields['postType'] = $iter['type'];
	         $fields['privacy'] = $iter['privacy']['value'];
	         $fields['link'] = $iter['link'];
	         $fields['placeID'] = $iter['place']['id'];
	         $fields['placeName'] = mysqli_real_escape_string( $con, $iter['place']['name']);
	         $fields['location'] = new Location();
	         $fields['location']->__set(street, mysqli_real_escape_string( $con, $iter['place']['location']['street'] ) );
	         $fields['location']->__set(city, mysqli_real_escape_string( $con, $iter['place']['location']['city'] ));
	         $fields['location']->__set(state, mysqli_real_escape_string( $con, $iter['place']['location']['state'] ));
	         $fields['location']->__set(country, mysqli_real_escape_string( $con, $iter['place']['location']['country'] ));
	         $fields['location']->__set(zip,$iter['place']['location']['zip'] );
	         $fields['location']->__set(latitude, $iter['place']['location']['latitude']);
	         $fields['location']->__set(longitude, $iter['place']['location']['longitude']);

	         //Collecting Comments
	         if( is_array( $iter['comments']['data'] ) ){
	            $fields['comments'] = array(); //We will create an array of Comment objects
	            $i = 0;
	            foreach( $iter['comments']['data'] as $commArray ){
	               $comment  = new Comment();

	               $comment->__set(ID, $commArray['id'] );
	               $comment->__set(message, mysqli_real_escape_string( $con, $commArray['message'] ) );
	               $comment->__set(createdTime, date( 'Y-m-d H:i:s', strtotime( $commArray['created_time']) ) );
	               $comment->__set(likes, $commArray['like_count'] );
	               if( $commArray['can_comment'] )
	                  $comment->__set(canComment, 0 );
	               else
	                  $comment->__set(canComment, 1 );
	               if( $commArray['can_remove'])
	                  $comment->__set(canRemove, 0 );
	               else
	                  $comment->__set(canRemove, 1  );
	               $comment->__set(fromID, $commArray['from']['id'] );
	               $comment->__set(fromName, mysqli_real_escape_string( $con, $commArray['from']['name'] ) );

	               //$Comment->tags is an array as well
	               if( is_array( $comArray['message_tags'] ) ){
	                  $tags = array();
	                  $k = 0;
	                  foreach( $comArray['message_tags'] as $tagIter ){
	                     $tags[$k] = $tagIter['id'];
	                     $k++;
	                  }
	                  $comment->__set(tags, $tags );
	               }
	               $fields['comments'][$i] = $comment;
	               $i++;
	            }
	         }

	         //Collecting Tags in an array
	         if( is_array( $iter['to']['data'] ) ){
	            $fields['tags'] = array();
	            $i = 0; //array length
	            foreach( $iter['to']['data'] as $tagArray ){
	               $fields['tags'][$i] = $tagArray['id'];
	               $i++;
	            }
	         }


	         $this->postsArray[$this->arrayLength] = $fields;
	         $this->arrayLength++;
	      }
	  }else{
	      $GLOBALS['return_array']['debug_wall_array'] = 'array of wall not present!';
	      return -1;
      }
      return 0;
   }

}

?>
