<?php 

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class RecordProgramsMigration_100
 */
class RecordProgramsMigration_100 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('record_programs', [
                'columns' => [
                    new Column(
                        'record_id',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull' => true,
                            'size' => 10,
                            'first' => true
                        ]
                    ),
                    new Column(
                        'program_id',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'unsigned' => true,
                            'notNull' => true,
                            'size' => 10,
                            'after' => 'record_id'
                        ]
                    ),
                    new Column(
                        'position',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'default' => "0",
                            'unsigned' => true,
                            'notNull' => true,
                            'size' => 3,
                            'after' => 'program_id'
                        ]
                    )
                ],
                'indexes' => [
                    new Index('PRIMARY', ['record_id', 'program_id', 'position'], 'PRIMARY'),
                    new Index('program_fk', ['program_id'], null)
                ],
                'references' => [
                    new Reference(
                        'program_fk',
                        [
                            'referencedTable' => 'programs',
                            'columns' => ['program_id'],
                            'referencedColumns' => ['id'],
                            'onUpdate' => 'RESTRICT',
                            'onDelete' => 'CASCADE'
                        ]
                    ),
                    new Reference(
                        'record_fk',
                        [
                            'referencedTable' => 'records',
                            'columns' => ['record_id'],
                            'referencedColumns' => ['id'],
                            'onUpdate' => 'RESTRICT',
                            'onDelete' => 'CASCADE'
                        ]
                    )
                ],
                'options' => [
                    'TABLE_TYPE' => 'BASE TABLE',
                    'AUTO_INCREMENT' => '',
                    'ENGINE' => 'InnoDB',
                    'TABLE_COLLATION' => 'utf8_general_ci'
                ],
            ]
        );
    }

    /**
     * Run the migrations
     *
     * @return void
     */
    public function up()
    {

    }

    /**
     * Reverse the migrations
     *
     * @return void
     */
    public function down()
    {

    }

}
