<?php
declare(strict_types=1);

use Phalcon\Mvc\Controller;
use TvArchive\Exception\NotFoundException;

class RecordController extends Controller
{
    public function listAction()
    {
        $this->view->field = $field = $this->request->getQuery('field', 'string', 'id');
        $this->view->sort  = $sort  = $this->request->getQuery('sort', 'string', 'ASC');
        $this->view->page  = $page  = $this->request->getQuery('page', 'int', 1);

        $this->view->gridFields = [
            'id' => 'ID',
            'record_from' => 'Record From',
            'record_to' => 'Record To',
            'programs' => 'Programs',
        ];

        $this->view->records = Record::getDetailedList($page, 20, $field, $sort);
    }

    public function viewRecordAction($recordId)
    {
        $record = Record::findFirst($recordId);

        if (!$record) {
            throw new NotFoundException("Record id='{$recordId}' not found");
        }

        $this->view->recordUrl = "/storage/{$record->file_name}";
    }
}