<?php
namespace Craft;

class WeatherootsRecord extends BaseRecord
{
    public function getTableName()
    {
        return 'weatheroots_records';
    }

    public function defineAttributes()
    {
        return array(

            'town' => array(AttributeType::String, 'required' => true),
            'state' => array(AttributeType::String, 'required' => true),
            'min_temp' => array(AttributeType::String),
            'max_temp' => array(AttributeType::String),
            'status' => array(AttributeType::String, 'required' => true),  //status(Active/Inactive)
        );
    }    

    /**
     * Create a new instance of the current class. This allows us to
     * properly unit test our service layer.
     *
     * @return BaseRecord
     */
    public function create()
    {
        $class = get_class($this);
        $record = new $class();

        return $record;
    }     
}