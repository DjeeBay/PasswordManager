<?php


namespace App\Interfaces;


interface KeepassRepositoryInterface extends BaseRepositoryInterface
{
    public function getStructuredItems($category_id);

    public function processXml(\SimpleXMLElement $xml, $categoryName) : bool;

    public function getHistoric(array $parameters = []);
}
