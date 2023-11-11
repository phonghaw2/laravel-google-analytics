<?php

namespace Phonghaw2\Analytics\Exceptions;

use Exception;

class InvalidConfiguration extends Exception
{
    public static function propertyIdNotSpecified()
    {
        return new static('There was no property ID specified. You must provide a valid property ID to execute queries on Google Analytics.');
    }

    public static function credentialsJsonDoesNotExist(string $path)
    {
        return new static("Could not find a credentials file at `{$path}`.");
    }

}
