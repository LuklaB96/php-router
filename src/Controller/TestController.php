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
        $found = $person->find($id);
        if ($found) {
            $this->response->setStatusCode(200);
            $this->response->toJSON([
                "message" => "ok",
                "persons" => [
                    $id => [
                        "firstName" => $person->getFirstName(),
                        "lastName" => $person->getLastName()
                    ]
                ]
            ]);
            return;
        }
        $this->response->setStatusCode(404);
        $this->response->toJSON([
            "message" => "not found",
        ]);
    }
    /**
     * POST route should always return a valid response
     * @return void
     */
    public function csrfValidationExample()
    {
        if (!$this->csrfAuth()) {
            //postResponse helper function, if custom data need to be sent use $this->response->toJSON() instead.
            $this->postResponse(401, 'Unauthorized');
            return;
        }
        //do stuff

        //send response
        $this->postResponse(200, 'ok');
    }

    public function standardQueryExample()
    {
        //if we dont want to use entity standard query functions
        $query = 'SELECT * FROM app_db.person LIMIT 1';
        $result = $this->db->execute($query);
        echo $result[0]['login'];
    }
    public function createPost()
    {

    }
}
?>