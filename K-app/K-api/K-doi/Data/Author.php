<?php

/**
 * Description of Author
 *
 * @author bruno.morandiere
 */

namespace src\Data;

class Author
{
    const int TYPE_PERSONAL = 1;
    const int TYPE_ORGANIZATION = 2;

    private bool $isOrganization;
    private string $familyName;
    private string $givenName;
    private string $orgName;

    /**
     * Constructs the Author
     */
    public function __construct(int $type = 1,string $name = "", ?string $givenName = null)
    {
        if ($type == self::TYPE_PERSONAL)
        {
            $this->givenName = $givenName;
            $this->familyName = $name;
            $this->isOrganization = false;
        }
        else
        {
            $this->orgName = $name;
            $this->isOrganization = true;
        }
    }

    public function getFamilyName(): string
    {
        return $this->familyName;
    }

    public function getGivenName(): string
    {
        return $this->givenName;
    }

    public function setFamilyName(string $familyName): void
    {
        $this->familyName = $familyName;
    }

    public function setGivenName(string $givenName): void
    {
        $this->givenName = $givenName;
    }

    public function getOrgName(): string
    {
        return $this->orgName;
    }

    public function setOrgName(string $orgName): void
    {
        $this->orgName = $orgName;
    }

    public function isOrganization(): bool
    {
        return $this->isOrganization;
    }

    public function setIsOrganization(bool $isOrganization): void
    {
        $this->isOrganization = $isOrganization;
    }
}