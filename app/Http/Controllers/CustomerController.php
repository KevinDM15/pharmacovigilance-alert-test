<?php

namespace App\Http\Controllers;

use App\Http\Resources\CustomerResource;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function show(Customer $customer): CustomerResource
    {
        return new CustomerResource($customer);
    }
}
