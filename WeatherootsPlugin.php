<?php
namespace Craft;

class WeatherootsPlugin extends BasePlugin
{
	function getName()
	{
		return 'Weather OOTS';
	}

	function getVersion()
	{
		return '1.0';
	}

	function getDeveloper()
	{
		return 'Out Of The Square Media - OOTS';
	}

	function getDeveloperUrl()
	{
		return 'http://outofthesquare.com.au';
	}

	protected function defineSettings()
	{
		return array(
			'cacheFile'  => array(AttributeType::String, 'required' => true),
			'rssFeedUrl'  => array(AttributeType::String, 'required' => true),
			'linkBackUrl'  => array(AttributeType::String, 'required' => true),
		);
	}

	public function getSettingsHtml()
	{
		return craft()->templates->render('weatheroots/_settings', array(
			'settings' => $this->getSettings()
		));
	}

	public function hasCpSection()
    {
        return true;
    }

    /**
     * Register control panel routes
     */
    public function registerCpRoutes()
	{
  		return array(
  			'weatheroots/index' => array('action' => 'weatheroots'),
  			'weatheroots/new' => array('action' => 'weatheroots/new'),
    		'weatheroots/add' => array('action' => 'weatheroots/add'),
    		'weatheroots/edit/(?P<requestId>\d+)' => array('action' => 'weatheroots/edit'), //edit route
    		'weatheroots/delete' => array('action' => 'weatheroots/delete'),
    		'weatheroots/townRSS' => array('action' => 'weatheroots/townRSS'),
  		);
	} 

}