<?php

// Path: src/Service/CustomerService.php

namespace App\Service;

use App\DTO\PaginationDTO;
use App\Entity\Customer;
use App\Repository\CustomerRepository;

class CustomerService
{
    private CustomerRepository $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }
}
