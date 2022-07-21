<?php
namespace Apie\CommonValueObjects;

enum Gender: string
{
    case MALE = 'M';
    case FEMALE = 'F';

    public function getSalutation(): string
    {
        return self::MALE === $this ? 'Mr.' : 'Mrs.';
    }
}
