<?php
namespace App\Controller;

use App\Entity\Person;
use App\Lib\Controller\BaseController;
use App\Lib\Routing\Response;

class PersonController extends BaseController
{
    public function getPersonById($id)
    {
        $person = new Person();
        $res = new Response();
        $person->find($id);
        if ($person->getId() == null) {
            $res->toJSON(
                [
                    'status' => 'not found'
                ]
            );
        } else {
            $res->toJSON(
                [
                    'person' => [
                        'id' => $person->getId(),
                        'firstName' => $person->getFirstName(),
                        'lastName' => $person->getLastName()
                    ],
                    'status' => 'ok'
                ]
            );
        }
    }
}
?>