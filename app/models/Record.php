<?php

class Record extends \Phalcon\Mvc\Model
{
    /**
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=10, nullable=false)
     */
    public $id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    public $file_name;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $record_from;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $record_to;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->hasMany('id', 'RecordProgram', 'record_id', ['alias' => 'recordPrograms']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'records';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Record[]|Record
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Record
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public function onConstruct()
    {
        $this->setRecordFrom(new DateTime());
    }

    public function setRecordFrom(DateTimeInterface $dateTime)
    {
        $this->record_from = $dateTime->format('Y-m-d H:i:s');
    }

    public function setRecordToByMovieLength(int $seconds)
    {
        $timestamp = strtotime($this->record_from);

        if (false !== $timestamp) {
            $this->record_to = date('Y-m-d H:i:s', $timestamp + $seconds);
        }
    }

    public function generateFileName(string $extension = null): string
    {
        $this->file_name = bin2hex(random_bytes(16)) . ($extension ? ".{$extension}" : '');

        return $this->file_name;
    }

    public function getRecordFrom(): DateTime
    {
        return new DateTime($this->record_from);
    }

    public function getRecordTo(): DateTime
    {
        return new DateTime($this->record_to);
    }

    /**
     * @param int    $page
     * @param int    $perPage
     * @param string $orderField
     * @param string $orderSort
     *
     * @return array
     *
     */
    public static function getDetailedList(
        int $page = 1,
        int $perPage = 20,
        string $orderField = 'id',
        string $orderSort = 'ASC'
    ): array
    {
        if ($page < 1 || $perPage < 1) {
            throw new InvalidArgumentException('Invalid page or perPage value');
        }

        $allowedField = ['id', 'file_name', 'record_from', 'record_to', 'programs'];

        if (!in_array($orderField, $allowedField)) {
            $orderField = 'id';
        }

        $orderSort = strtoupper($orderSort) == 'DESC' ? 'DESC' : 'ASC';

        $record = new static();

        $sql = <<<SQL
SELECT
  r.*,
  GROUP_CONCAT(p.name ORDER BY rp.position SEPARATOR ';') programs
FROM records r
INNER JOIN record_programs rp ON rp.record_id = r.id
INNER JOIN programs p ON p.id = rp.program_id
GROUP BY r.id
ORDER BY {$orderField} {$orderSort}
SQL;

        /** @var \Phalcon\Db\AdapterInterface $conn */
        $conn   = $record->getReadConnection();
        $result = $conn->query($sql);

        return array_map(function(array $item) {
            $item['programs'] = explode(';', $item['programs']);

            return $item;
        }, $result->fetchAll(PDO::FETCH_ASSOC));
    }
}
