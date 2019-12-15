<?php

namespace App\EntityTransform;

use App\Entity\Dm\Users;

class UserWithOptionValue
{

    /** @var Users $user */
    private $user;

    /** @var string[] */
    private $optionValue;

    /**
     * @param Users $user
     * @param string[] $optionValue
     */
    public function __construct(Users $user, array $optionValue)
    {
        $this->user = $user;
        $this->optionValue = $optionValue;
    }

    /**
     * @return Users
     */
    public function getUser(): Users
    {
        return $this->user;
    }

    /**
     * @param Users $user
     */
    public function setUser(Users $user): void
    {
        $this->user = $user;
    }

    /**
     * @return string[]
     */
    public function getValue(): array
    {
        return $this->optionValue;
    }

    /**
     * @param string[] $optionValue
     */
    public function setOptionValue(array $optionValue): void
    {
        $this->optionValue = $optionValue;
    }

    public function addValue(string $value) : void
    {
        $this->optionValue[]= $value;
    }
}
