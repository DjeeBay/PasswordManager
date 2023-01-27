<?php


namespace App\Interfaces;


use App\Models\User;

interface KeepassRepositoryInterface extends BaseRepositoryInterface
{
    public function getStructuredItems($category_id, bool $isPrivate);

    public function processXml(\SimpleXMLElement $xml, $categoryName, bool $withIcons) : bool;

    public function getHistoric(array $parameters = []);

    public function createMultiple(array $keepasses, $category_id, bool $isPrivate) : array;
    public function encryptPrivatePasswordsWithNewPassphrase(User $user, ?string $oldPassphrase, string $newPassphrase) : void;
}
