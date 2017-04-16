<?php
declare(strict_types=1);

class Program extends \Phalcon\Mvc\Model
{
    /**
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=10, nullable=false)
     */
    public $id;

    /**
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    public $name;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->hasMany('id', 'RecordProgram', 'program_id', ['alias' => 'recordPrograms']);
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return 'programs';
    }

    /**
     * @param mixed $parameters
     *
     * @return Program[]|Program
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Program
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    /**
     * @param string $name
     *
     * @return Program|bool
     */
    public static function getByName(string $name)
    {
        return static::findFirst([
            'name = :name:',
            'bind' => [
                'name' => $name,
            ]
        ]);
    }
}
