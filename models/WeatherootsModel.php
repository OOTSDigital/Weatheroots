<?php
namespace Craft;


class WeatherootsModel extends BaseModel
{
	protected function defineAttributes()
	{
		return array(

			'id'    => AttributeType::Number,
			'dateCreated'=>AttributeType::DateTime,  //add this if you want to query record using dateCreated
			'town' => array(AttributeType::String, 'required' => true),
			'state' => array(AttributeType::String, 'required' => true),
            'min_temp' => array(AttributeType::String),
            'max_temp' => array(AttributeType::String),
            'status' => array(AttributeType::String, 'required' => true),  //status(Active/Inactive)
		);
	}
}