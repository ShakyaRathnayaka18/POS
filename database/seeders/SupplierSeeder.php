<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            [
                'company_name' => 'Metro Cash & Carry',
                'business_type' => 'Wholesale',
                'tax_id' => 'TAX-METRO-001',
                'contact_person' => 'Rajesh Kumar',
                'email' => 'rajesh@metrocash.com',
                'phone' => '011-2345678',
                'mobile' => '9876543210',
                'payment_terms' => 'Net 30',
                'credit_limit' => 500000.00,
            ],
            [
                'company_name' => 'Reliance Wholesale',
                'business_type' => 'Distributor',
                'tax_id' => 'TAX-RELIANCE-002',
                'contact_person' => 'Priya Sharma',
                'email' => 'priya@reliancewholesale.com',
                'phone' => '011-3456789',
                'mobile' => '9876543211',
                'payment_terms' => 'Net 15',
                'credit_limit' => 750000.00,
            ],
            [
                'company_name' => 'Local Food Distributors',
                'business_type' => 'Wholesale',
                'tax_id' => 'TAX-LOCAL-003',
                'contact_person' => 'Amit Patel',
                'email' => 'amit@localfood.com',
                'phone' => '011-4567890',
                'mobile' => '9876543212',
                'payment_terms' => 'Net 7',
                'credit_limit' => 300000.00,
            ],
            [
                'company_name' => 'Beverage Solutions Inc',
                'business_type' => 'Distributor',
                'tax_id' => 'TAX-BEV-004',
                'contact_person' => 'Sarah Johnson',
                'email' => 'sarah@beveragesolutions.com',
                'phone' => '011-5678901',
                'mobile' => '9876543213',
                'payment_terms' => 'Net 21',
                'credit_limit' => 400000.00,
            ],
            [
                'company_name' => 'Office Mart Wholesale',
                'business_type' => 'Wholesale',
                'tax_id' => 'TAX-OFFICE-005',
                'contact_person' => 'Michael Chen',
                'email' => 'michael@officemart.com',
                'phone' => '011-6789012',
                'mobile' => '9876543214',
                'payment_terms' => 'Net 30',
                'credit_limit' => 200000.00,
            ],
        ];

        foreach ($suppliers as $supplierData) {
            $supplier = Supplier::firstOrNew(['company_name' => $supplierData['company_name']]);
            if (!$supplier->exists) {
                $supplier->fill($supplierData)->save();
            }
        }
    }
}
