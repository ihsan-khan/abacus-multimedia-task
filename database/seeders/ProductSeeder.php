<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Wireless Headphones',
                'description' => 'High-quality wireless headphones with noise cancellation',
                'price' => 129.99,
                'stock' => 50,
                'image' => 'headphones.jpg',
                'is_active' => true,
            ],
            [
                'name' => 'Smartphone',
                'description' => 'Latest smartphone with high-resolution camera',
                'price' => 699.99,
                'stock' => 30,
                'image' => 'smartphone.jpg',
                'is_active' => true,
            ],
            [
                'name' => 'Laptop',
                'description' => 'Powerful laptop for work and gaming',
                'price' => 1299.99,
                'stock' => 20,
                'image' => 'laptop.jpg',
                'is_active' => true,
            ],
            [
                'name' => 'Smart Watch',
                'description' => 'Fitness tracker and smartwatch with heart rate monitor',
                'price' => 199.99,
                'stock' => 40,
                'image' => 'smartwatch.jpg',
                'is_active' => true,
            ],
            [
                'name' => 'Wireless Earbuds',
                'description' => 'Compact wireless earbuds with charging case',
                'price' => 79.99,
                'stock' => 60,
                'image' => 'earbuds.jpg',
                'is_active' => true,
            ],
            [
                'name' => 'Tablet',
                'description' => '10-inch tablet with high-resolution display',
                'price' => 399.99,
                'stock' => 25,
                'image' => 'tablet.jpg',
                'is_active' => true,
            ],
            [
                'name' => 'Bluetooth Speaker',
                'description' => 'Portable Bluetooth speaker with excellent sound quality',
                'price' => 89.99,
                'stock' => 35,
                'image' => 'speaker.jpg',
                'is_active' => true,
            ],
            [
                'name' => 'Gaming Console',
                'description' => 'Next-generation gaming console',
                'price' => 499.99,
                'stock' => 15,
                'image' => 'console.jpg',
                'is_active' => true,
            ],
            [
                'name' => 'Digital Camera',
                'description' => 'Professional digital camera with 4K video',
                'price' => 899.99,
                'stock' => 10,
                'image' => 'camera.jpg',
                'is_active' => true,
            ],
            [
                'name' => 'External Hard Drive',
                'description' => '1TB external hard drive for data storage',
                'price' => 69.99,
                'stock' => 45,
                'image' => 'harddrive.jpg',
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
