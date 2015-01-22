<?php
namespace Craft;

/**
 * Weatheroots service
 */
class WeatherootsService extends BaseApplicationComponent
{
     protected $weatherootsRecord;
     protected $settings;       //settings in the plugin setup form
     protected $cacheFilePath; //rssFeedUrl
     protected $rssFeedUrl;   //rssFeedUrl
     protected $linkBackUrl; //linkBackUrl

    /**
     * Create a new instance of the Weatheroots Service.
     * Constructor allows WeatherootsRecord dependency to be injected to assist with unit testing.
     *
     * @param @weatherootsRecord to access the database
     */
    public function __construct($weatherootsRecord= null)
    {
        $this->weatherootsRecord = $weatherootsRecord;
        if (is_null($this->weatherootsRecord)) {

            $this->weatherootsRecord = WeatherootsRecord::model();
        }

        $settings = craft()->plugins->getPlugin('weatheroots')->getSettings(); //get settings
        $this->settings = $settings;

        $this->cacheFilePath = $this->settings->cacheFile; //cacheFilePath for storing xml files
        $this->rssFeedUrl = $this->settings->rssFeedUrl;       //rssFeedUrl 
        $this->linkBackUrl = $this->settings->linkBackUrl;     //linkBackUrl to link to http://www.weather.com.au/nsw/<city>
    }
    
    //get RSS Feed for a particular town from local file
    //no need for fetching from live site here
    public function townRSS($town) {
        
        $this_town = str_replace(" ", "+",$town) ;

        $town_file_name = strtolower(str_replace("+", "_",$this_town)); //for example replace + in perisher+valley

        $cache_file = $this->cacheFilePath.$town_file_name.".xml";

        $x = @simplexml_load_file($cache_file);  //do this to suppress errors, otherwise catch error here  

        echo "<ul>";
         
        $i = 0;
        //foreach($x->channel->item as $entry) {
        foreach($x->channel->item as $entry) {
            
            $forecast = $entry->xpath('w:forecast'); //specify xpath

            if($i%2 != 0){
                echo "<li>".$entry->title . "</li>";
                echo "<li>".$entry->pubDate . "</li>";
                echo "Weather Conditions";
                echo "<li>".$forecast[0]['day'] . "</li>";
                echo "<li>".$forecast[0]['min'] . "</li>";
                echo "<li>".$forecast[0]['max'] . "</li>";
                echo "<li>".$forecast[0]['description'] . "</li>";
            }
            $i++;
        }

        echo "</ul>";
    }

    //get RSS Feed for all Towns
    public function allTownsFeed() {
        
        //foreach town set feed_url
        //loop through and produce the <li> details for that town
        //end foreach loop
        $towns = $this->weatherootsRecord->findAll(array('order'=>'t.dateCreated'));
        $townsModel = WeatherootsModel::populateModels($towns, 'id'); //populate models into models array by id ascending, it will return array of all models

        echo "<ul class='weather-report-list'>";

        foreach($townsModel as $model){  //start foreach town
            
            $this_town = strtolower(str_replace(" ", "+",$model->town));

            //cache weather file, download it every 20 mins

            $town_file_name = strtolower(str_replace("+", "_",$this_town)); //replace + in perisher+valley

            $cache_file = $this->cacheFilePath.$town_file_name.".xml";

            if (file_exists($cache_file) && (filemtime($cache_file) > (time() - 60 * 20 ))) {
                // Cache file is less than 20 minutes old. 
                // Don't bother refreshing, just use the file as-is.
                $file = @file_get_contents($cache_file);
                
            } else {
           // Our cache is out-of-date, so load the data from our remote server,
           // and also save it over our cache for next time.

                //$rss_feed_url = $this->settings->cacheFile; // "http://rss.weather.com.au/nsw/"

                $url = $this->rssFeedUrl .$this_town;
                $file = @file_get_contents($url); //suppress error message

                if (strpos($http_response_header[0], "200")) { 
                       file_put_contents($cache_file, $file, LOCK_EX); 
                } else { 
                }
                   
            }      

            //end cache weather file

            //start processing local file here
            $x = @simplexml_load_file($cache_file);  //do this to suppress errors, otherwise catch error here   

            if($x){ //check if valid
                $i = 0;
                foreach($x->channel->item as $entry) { //start foreach channel
                    
                        $title = str_replace("Forecast", "",$entry->title);
                        echo $title;
                        $forecast = $entry->xpath('w:forecast'); //specify xpath

                        //$link_back_path = $this->settings->linkBackUrl; // "http://www.weather.com.au/nsw/"

                        $linkback_url = $this->linkBackUrl.$this_town;

                        if($i%2 != 0){ //limit only to entries from second entry element
                            echo "<li>";
                                echo "<a href='$linkback_url' target='_blank'>";
                                echo "<span class='wrl-town'>".$title."</span>";
                                echo "<span class='wrl-low'>".$forecast[0]['min']." &deg; C</span>";
                                echo "<span class='wrl-high'>".$forecast[0]['max']." &deg; C</span>";
                                echo "</a>";
                            echo "</li>";
                        }
                        $i++;
                } //end foreach channel
            }
        } //end foreach town   
        echo "</ul>";
    }

     //get RSS Feed for all Towns, return array of towns with their weather details
    
    public function allTownsWeather() {
        
        //foreach town set feed_url
        //loop through and produce the <li> details for that town
        //end foreach loop
        $towns = $this->weatherootsRecord->findAll(array('order'=>'t.dateCreated'));
        $townsModel = WeatherootsModel::populateModels($towns, 'id'); //populate models into models array by id ascending, it will return array of all models

        $all_towns_weather = array();

        foreach($townsModel as $model){  //start foreach town
            
            $this_town = str_replace(" ", "+",$model->town) ;

            $town_file_name = strtolower(str_replace("+", "_",$this_town)); //replace + in perisher+valley 

            $cache_file = $this->cacheFilePath.$town_file_name.".xml";

            $x = @simplexml_load_file($cache_file);  //do this to suppress errors, otherwise catch error here  

            
            $townWeather = array(); //declare array for town weather

            if($x){ //check if valid
                $i = 0;
                foreach($x->channel->item as $entry) { //start foreach channel
                      
                        $title = str_replace("Forecast", "",$entry->title);

                        $forecast = $entry->xpath('w:forecast'); //specify xpath

                        if($i%2 != 0){ //limit only to entries from second entry element

                            $townWeather = array("title"=>$title, "min"=>$forecast[0]['min'],"max"=>$forecast[0]['max'],"description"=>$forecast[0]['description']);
                            array_push($all_towns_weather,$townWeather); //push town weather to all town weather
                        }

                        $i++;
                } //end foreach channel
            } 
        } //end foreach town   
        return $all_towns_weather;
    }

    /**
     * Save a new record to the database.
     *
     * @param  WeatherootsModel $model
     * @return bool
     */
    public function add(WeatherootsModel &$model)
    {

       $record = $this->weatherootsRecord->create();

       $attributes = array(
       						'town' =>$model->town,
       						'state'=>$model->state, 
       						'min_temp'=>$model->min_temp, 
       						'max_temp'=>$model->max_temp, 
       						'status'=>$model->status
       					   );
       
       $record->setAttributes($attributes,false);  //if you don't put false it won't save for non-mandatory field.
       $record->save();
    }

    //update service
    public function update(WeatherootsModel &$model, $id)
    {

        $record = $this->weatherootsRecord->findById($id); //get record by Id

        $attributes = array(
                            'town' =>$model->town,
                            'state'=>$model->state, 
                            'min_temp'=>$model->min_temp, 
                            'max_temp'=>$model->max_temp, 
                            'status'=>$model->status
                           );

       $record->setAttributes($attributes,false);  //if you don't put false it won't save for non-mandatory field.
       $record->save();
    }

    /**
     * GetAll.
     * @param $id = ad id 
     * @throws Exception
     * @return bool
     */
    public function getAll()
    {
        $records = $this->weatherootsRecord->findAll(array('order'=>'t.dateCreated'));
        return WeatherootsModel::populateModels($records, 'id'); //populate models into models array by id ascending, it will return array of all models
    }

     //Get town by Id
      public function getTownById($id)
    {
        $record = $this->weatherootsRecord->findById($id); //search record by id from the galleryoots_records table

        return $record;
    }

	/**
	 * Fires an 'onBeforeSend' event.
	 *
	 * @param WeatherootsEvent $event
	 */
	public function onBeforeSend(WeatherootsEvent $event)
	{
		$this->raiseEvent('onBeforeSend', $event);
	}
}