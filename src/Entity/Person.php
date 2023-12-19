<?php
namespace App\Entity;

use App\Lib\Database\Enums\ColumnType;
use App\Lib\Entity\Entity;
use App\Lib\Database\Mapping\Column;

class Person extends Entity
{
    /**
     * This is an example class that is extending Entity parent class
     * Every property should be protected/private
     * Every property should have getters and setters to read/write from database.
     * Attributes are used to tell parent class which properties should be used as column names and definitions.
     * Use #[Column] attribute to define them, examples are below.
     */

    /**
     * Default primary key for our table
     * @var 
     */
    #[Column(type: ColumnType::INT, primaryKey: true, autoIncrement: true)]
    protected $id;

    #[Column(type: ColumnType::VARCHAR, nullable: true, length: 32)]
    protected $imie;
    #[Column(type: ColumnType::VARCHAR, nullable: true, length: 32)]
    protected $nazwisko;

    public function getId()
    {
        return $this->id;
    }
    public function getImie()
    {
        return $this->imie;
    }
    public function getNazwisko()
    {
        return $this->nazwisko;
    }
    public function setImie($imie)
    {
        $this->imie = $imie;
    }
    public function setNazwisko($nazwisko)
    {
        $this->nazwisko = $nazwisko;
    }
}