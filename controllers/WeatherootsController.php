<?php
namespace Craft;

/**
 * Weatheroots controller
 */
class WeatherootsController extends BaseController
{
	/**
	 * @var Allows anonymous access to this controller's actions.
	 * @access protected
	 */
	protected $allowAnonymous = true;

	public function actionIndex()
	{
        // Load index template
    	$this->renderTemplate('weatheroots/index');
	}

	//action for Town RSS Feed
	public function actionTownRss()
	{
		$town = "Newcastle";
        craft()->weatheroots->townRSS($town);
	}

    
	public function actionNew()
	{
        // Load a particular template and with all of the variables you've created
    	$this->renderTemplate('weatheroots/_new');
	}

	public function actionShow()
	{
	  	// Load show template
    	$this->renderTemplate('weatheroots/_show');
	}

	//edit will need to pull data from the db and display on the template
	//once the data is changed and user click save, data is saved to the db
	public function actionEdit()
	{
	  	// render the template, pass model on render template
    	$this->renderTemplate('weatheroots/_edit');
	}

   
	public function actionUpdate()
	{
	     //update gallery details
		$id = craft()->request->getPost('id'); // get id

		$weather = new WeatherootsModel();  //get model by id

		$weather->town  = craft()->request->getPost('town');
		$weather->state = craft()->request->getPost('state');
		$weather->status  = craft()->request->getPost('status');

		craft()->weatheroots->update($weather,$id); //calls the update service, pass model and id
		$this->returnSuccess();
	}

	/**
	 * Add Weather based on posted params.
	 *
	 * @throws Exception
	 */
	public function actionAdd()
	{
		//call the service to add this to db
		$weather = new WeatherootsModel();

		$weather->town  = craft()->request->getPost('town');
		$weather->state = craft()->request->getPost('state');
		$weather->status  = craft()->request->getPost('status');

		if( craft()->weatheroots->add($weather) ){ //calls the add service to do the saving here

			$this->returnSuccess();
		
		}else{
			craft()->userSession->setNotice(Craft::t('City Not saved, Please fill all details...')); //notice on user session
		}  
	}

	/**
	 * Returns a 'success' response.
	 *
	 * @return void
	 */
	protected function returnSuccess()
	{
		if (craft()->request->isAjaxRequest())
		{
			$this->returnJson(array('success' => true));
		}
		else
		{
			// Deprecated. Use 'redirect' instead.
			$successRedirectUrl = craft()->request->getPost('successRedirectUrl');

			if ($successRedirectUrl)
			{
				$_POST['redirect'] = $successRedirectUrl;
			}

			craft()->userSession->setNotice('City has been updated.');
			$this->redirectToPostedUrl();
		}
	}
}