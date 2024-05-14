<?php

// Create a data fixture for the Device and Brand entities
// Path: src/DataFixtures/DeviceFixture.php

namespace App\DataFixtures;

use App\Entity\Brand;
use App\Entity\Device;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class DeviceFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Brands
        foreach ($this->generateBrands() as $brand) {
            $manager->persist($brand);
        }

        // Devices
        foreach ($this->generateDevices() as $device) {
            $manager->persist($device);
        }

        $manager->flush();
    }

    /**
     * @return \Generator<Brand> 
     */
    private function generateBrands(): \Generator
    {
        $brandsNames = [
            'Samsung',
            'Apple',
            'Google',
            'Huawei',
            'Xiaomi',
            'Sony',
            'Motorola',
            'LG',
            'Nokia',
            'OnePlus',
            'BlackBerry',
            'HTC',
            'Oppo',
            'Vivo',
            'Microsoft',
        ];

        foreach ($brandsNames as $brandName) {
            $brand = (new Brand)->setName($brandName);

            $this->addReference("brand-" . $brand->getName(), $brand);

            yield $brand;
        }
    }

    private function generateDevices(): \Generator
    {
        $devicesData = [
            [
                'brand' => 'Samsung',
                'model' => 'Galaxy S21',
                'type' => 'phone',
            ],
            [
                'brand' => 'Apple',
                'model' => 'iPhone 12',
                'type' => 'phone',
            ],
            [
                'brand' => 'Google',
                'model' => 'Pixel 5',
                'type' => 'phone',
            ],
            [
                'brand' => 'Huawei',
                'model' => 'Mate 40 Pro',
                'type' => 'phone',
            ],
            [
                'brand' => 'Xiaomi',
                'model' => 'Mi 11',
                'type' => 'phone',
            ],
            [
                'brand' => 'Sony',
                'model' => 'Xperia 1 III',
                'type' => 'phone',
            ],
            [
                'brand' => 'Motorola',
                'model' => 'Moto G Power',
                'type' => 'phone',
            ],
            [
                'brand' => 'LG',
                'model' => 'Wing',
                'type' => 'phone',
            ],
            [
                'brand' => 'Nokia',
                'model' => '8.3',
                'type' => 'phone',
            ],
            [
                'brand' => 'OnePlus',
                'model' => '8T',
                'type' => 'phone',
            ],
            [
                'brand' => 'BlackBerry',
                'model' => 'Key2',
                'type' => 'phone',
            ],
            [
                'brand' => 'HTC',
                'model' => 'U12+',
                'type' => 'phone',
            ],
            [
                'brand' => 'Oppo',
                'model' => 'Find X3 Pro',
                'type' => 'phone',
            ],
            [
                'brand' => 'Vivo',
                'model' => 'X60 Pro+',
                'type' => 'phone',
            ],
            [
                'brand' => 'Samsung',
                'model' => 'Galaxy S10',
                'type' => 'phone',
            ],
            [
                'brand' => 'Apple',
                'model' => 'iPhone 6',
                'type' => 'phone',
            ],
            [
                'brand' => 'Google',
                'model' => 'Nexus 5',
                'type' => 'phone',
            ],
            [
                'brand' => 'Huawei',
                'model' => 'P20 Pro',
                'type' => 'phone',
            ],
            [
                'brand' => 'Xiaomi',
                'model' => 'Mi 9',
                'type' => 'phone',
            ],
            [
                'brand' => 'Sony',
                'model' => 'Xperia XZ2',
                'type' => 'phone',
            ],
            [
                'brand' => 'Motorola',
                'model' => 'Moto G7',
                'type' => 'phone',
            ],
            [
                'brand' => 'LG',
                'model' => 'G8 ThinQ',
                'type' => 'phone',
            ],
            [
                'brand' => 'Nokia',
                'model' => '7 Plus',
                'type' => 'phone',
            ],
            [
                'brand' => 'OnePlus',
                'model' => '6T',
                'type' => 'phone',
            ],
            [
                'brand' => 'BlackBerry',
                'model' => 'KeyOne',
                'type' => 'phone',
            ],
            [
                'brand' => 'HTC',
                'model' => 'U11',
                'type' => 'phone',
            ],
            [
                'brand' => 'Oppo',
                'model' => 'Find X2 Pro',
                'type' => 'phone',
            ],
            [
                'brand' => 'Vivo',
                'model' => 'X50 Pro+',
                'type' => 'phone',
            ],
            [
                'brand' => 'Samsung',
                'model' => 'Galaxy Tab S7',
                'type' => 'tablet',
            ],
            [
                'brand' => 'Apple',
                'model' => 'iPad Pro',
                'type' => 'tablet',
            ],
            [
                'brand' => 'Samsung',
                'model' => 'Galaxy Tab S6',
                'type' => 'tablet',
            ],
            [
                'brand' => 'Google',
                'model' => 'Pixel Slate',
                'type' => 'tablet',
            ],
            [
                'brand' => 'Huawei',
                'model' => 'MatePad Pro',
                'type' => 'tablet',
            ],
            [
                'brand' => 'Microsoft',
                'model' => 'Surface Pro 7',
                'type' => 'tablet',
            ],
        ];

        foreach ($devicesData as $deviceData) {
            $device = (new Device())
                ->setModel($deviceData['model'])
                ->setType($deviceData['type'])
                ->setBrand($this->getReference("brand-" . $deviceData['brand']))
                ->setDescription(Faker\Factory::create()->text(200));

            yield $device;
        }
    }
}
