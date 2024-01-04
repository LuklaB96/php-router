<?php
namespace App\Controller;

use App\Entity\Person;
use App\Lib\Controller\BaseController;
use App\Lib\Routing\Response;

class TestController extends BaseController
{
    public function getPersonById($id)
    {
        $person = new Person();
        $res = new Response();
        $person->find($id);
    }
    /**
     * Csrf auth example
     * @return void
     */
    public function csrfValidationExample()
    {
        if (!$this->csrfAuth()) {
            echo 'csrf token expired';
            return;
        }
        echo 'csrf token is valid';
    }

    public function standardQueryExample()
    {
        //if we dont want to use entity standard query functions
        $query = 'SELECT * FROM main_db.person LIMIT 1';
        $result = $this->db->execute($query);
        echo $result[0]['login'];
    }
}
?>