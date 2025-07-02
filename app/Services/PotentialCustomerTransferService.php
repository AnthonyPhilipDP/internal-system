<?php
namespace App\Services;

use App\Models\PotentialCustomer;
use App\Models\Customer;
use App\Models\ContactPerson;

class PotentialCustomerTransferService
{
    public function transfer(PotentialCustomer $potentialCustomer): Customer
    {
        // Copy all fields except id and timestamps
        $customerData = $potentialCustomer->toArray();
        unset($customerData['id'], $customerData['created_at'], $customerData['updated_at'], $customerData['deleted_at']);

        // Create new Customer (customer_id will be set by model logic)
        $customer = Customer::create($customerData);

        // Transfer contact people
        foreach ($potentialCustomer->contactPerson as $potentialContact) {
            $contactData = $potentialContact->toArray();
            unset($contactData['id'], $contactData['potential_customer_id'], $contactData['created_at'], $contactData['updated_at']);
            $contactData['customer_id'] = $customer->customer_id;
            ContactPerson::create($contactData);
        }

        // Mark as transferred (before delete)
        $potentialCustomer->transferred_at = now();
        $potentialCustomer->save();

        // Delete potential customer
        $potentialCustomer->delete();

        return $customer;
    }
}