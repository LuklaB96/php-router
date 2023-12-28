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
        var_dump($person->getImie());
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
                        'imie' => $person->getImie(),
                        'nazwisko' => $person->getNazwisko()
                    ],
                    'status' => 'ok'
                ]
            );
        }
    }
}
?>