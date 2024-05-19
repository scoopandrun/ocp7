<?php

// Utility functions for handling requests.
// Path: src/Service/RequestService.php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class RequestService
{
    private Request $request;

    public function __construct(RequestStack $requestStack)
    {
        $this->request = $requestStack->getCurrentRequest();
    }

    /**
     * Gets the value of a query parameter as an array.
     *
     * @param string  $name The name of the query parameter.
     * @param boolean $toLowerCase Whether to convert the values to lowercase.
     * 
     * @return array The value of the query parameter as an array.
     */
    public function getQueryParameterAsArray(string $name, bool $toLowerCase = true): array
    {
        $parameterValue = $this->request->query->get($name, null) ?? '';

        // Convert the value to an array.
        $valueAsArray = explode(',', $parameterValue);

        // Trim the values and remove empty values.
        $valueAsArray = array_map('trim', $valueAsArray);
        $valueAsArray = array_filter($valueAsArray);

        // If the values should be converted to lowercase.
        if ($toLowerCase) {
            $valueAsArray = array_map('strtolower', $valueAsArray);
        }

        return $valueAsArray;
    }
}
