<?php

class RecordProgram extends \Phalcon\Mvc\Model
{
    /**
     *
     * @var integer
     * @Primary
     * @Column(type="integer", length=10, nullable=false)
     */
    public $record_id;

    /**
     *
     * @var integer
     * @Primary
     * @Column(type="integer", length=10, nullable=false)
     */
    public $program_id;

    /**
     *
     * @var integer
     * @Primary
     * @Column(type="integer", length=3, nullable=false)
     */
    public $position;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->belongsTo('program_id', 'Program', 'id', ['alias' => 'program']);
        $this->belongsTo('record_id', 'Record', 'id', ['alias' => 'record']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'record_programs';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return RecordProgram[]|RecordProgram
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return RecordProgram
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }
}
