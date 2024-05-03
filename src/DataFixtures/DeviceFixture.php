<?php

// Create a data fixture for the Device entity
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
            $this->addReference($brand->getName(), $brand);
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
        ];

        foreach ($brandsNames as $brandName) {
            yield (new Brand)->setName($brandName);
        }
    }

    private function generateDevices(): \Generator
    {
        $devicesData = [
            [
                'brand' => 'Samsung',
                'model' => 'Galaxy S21',
                'type' => 'phone',
                'dateFirstCommercialized' => '2021-01-29',
                'isSold' => true,
            ],
            [
                'brand' => 'Apple',
                'model' => 'iPhone 12',
                'type' => 'phone',
                'dateFirstCommercialized' => '2020-10-23',
                'isSold' => true,
            ],
            [
                'brand' => 'Google',
                'model' => 'Pixel 5',
                'type' => 'phone',
                'dateFirstCommercialized' => '2020-10-15',
                'isSold' => true,
            ],
            [
                'brand' => 'Huawei',
                'model' => 'Mate 40 Pro',
                'type' => 'phone',
                'dateFirstCommercialized' => '2020-10-22',
                'isSold' => true,
            ],
            [
                'brand' => 'Xiaomi',
                'model' => 'Mi 11',
                'type' => 'phone',
                'dateFirstCommercialized' => '2020-12-28',
                'isSold' => true,
            ],
            [
                'brand' => 'Sony',
                'model' => 'Xperia 1 III',
                'type' => 'phone',
                'dateFirstCommercialized' => '2021-06-28',
                'isSold' => true,
            ],
            [
                'brand' => 'Motorola',
                'model' => 'Moto G Power',
                'type' => 'phone',
                'dateFirstCommercialized' => '2020-04-16',
                'isSold' => true,
            ],
            [
                'brand' => 'LG',
                'model' => 'Wing',
                'type' => 'phone',
                'dateFirstCommercialized' => '2020-10-15',
                'isSold' => true,
            ],
            [
                'brand' => 'Nokia',
                'model' => '8.3',
                'type' => 'phone',
                'dateFirstCommercialized' => '2020-09-22',
                'isSold' => true,
            ],
            [
                'brand' => 'OnePlus',
                'model' => '8T',
                'type' => 'phone',
                'dateFirstCommercialized' => '2020-10-23',
                'isSold' => true,
            ],
            [
                'brand' => 'BlackBerry',
                'model' => 'Key2',
                'type' => 'phone',
                'dateFirstCommercialized' => '2018-06-28',
                'isSold' => true,
            ],
            [
                'brand' => 'HTC',
                'model' => 'U12+',
                'type' => 'phone',
                'dateFirstCommercialized' => '2018-06-28',
                'isSold' => true,
            ],
            [
                'brand' => 'Oppo',
                'model' => 'Find X3 Pro',
                'type' => 'phone',
                'dateFirstCommercialized' => '2021-03-11',
                'isSold' => true,
            ],
            [
                'brand' => 'Vivo',
                'model' => 'X60 Pro+',
                'type' => 'phone',
                'dateFirstCommercialized' => '2021-01-21',
                'isSold' => true,
            ],
            [
                'brand' => 'Samsung',
                'model' => 'Galaxy S10',
                'type' => 'phone',
                'dateFirstCommercialized' => '2019-03-08',
                'isSold' => false,
            ],
            [
                'brand' => 'Apple',
                'model' => 'iPhone 6',
                'type' => 'phone',
                'dateFirstCommercialized' => '2014-09-19',
                'isSold' => false,
            ],
            [
                'brand' => 'Google',
                'model' => 'Nexus 5',
                'type' => 'phone',
                'dateFirstCommercialized' => '2013-10-31',
                'isSold' => false,
            ],
            [
                'brand' => 'Huawei',
                'model' => 'P20 Pro',
                'type' => 'phone',
                'dateFirstCommercialized' => '2018-03-27',
                'isSold' => false,
            ],
            [
                'brand' => 'Xiaomi',
                'model' => 'Mi 9',
                'type' => 'phone',
                'dateFirstCommercialized' => '2019-02-20',
                'isSold' => false,
            ],
            [
                'brand' => 'Sony',
                'model' => 'Xperia XZ2',
                'type' => 'phone',
                'dateFirstCommercialized' => '2018-04-05',
                'isSold' => false,
            ],
            [
                'brand' => 'Motorola',
                'model' => 'Moto G7',
                'type' => 'phone',
                'dateFirstCommercialized' => '2019-02-07',
                'isSold' => false,
            ],
            [
                'brand' => 'LG',
                'model' => 'G8 ThinQ',
                'type' => 'phone',
                'dateFirstCommercialized' => '2019-03-29',
                'isSold' => false,
            ],
            [
                'brand' => 'Nokia',
                'model' => '7 Plus',
                'type' => 'phone',
                'dateFirstCommercialized' => '2018-02-25',
                'isSold' => false,
            ],
            [
                'brand' => 'OnePlus',
                'model' => '6T',
                'type' => 'phone',
                'dateFirstCommercialized' => '2018-10-29',
                'isSold' => false,
            ],
            [
                'brand' => 'BlackBerry',
                'model' => 'KeyOne',
                'type' => 'phone',
                'dateFirstCommercialized' => '2017-04-27',
                'isSold' => false,
            ],
            [
                'brand' => 'HTC',
                'model' => 'U11',
                'type' => 'phone',
                'dateFirstCommercialized' => '2017-06-09',
                'isSold' => false,
            ],
            [
                'brand' => 'Oppo',
                'model' => 'Find X2 Pro',
                'type' => 'phone',
                'dateFirstCommercialized' => '2020-03-06',
                'isSold' => false,
            ],
            [
                'brand' => 'Vivo',
                'model' => 'X50 Pro+',
                'type' => 'phone',
                'dateFirstCommercialized' => '2020-06-12',
                'isSold' => false,
            ],
            [
                'brand' => 'Samsung',
                'model' => 'Galaxy Tab S7',
                'type' => 'tablet',
                'dateFirstCommercialized' => '2020-08-05',
                'isSold' => true,
            ],
            [
                'brand' => 'Apple',
                'model' => 'iPad Pro',
                'type' => 'tablet',
                'dateFirstCommercialized' => '2020-03-25',
                'isSold' => true,
            ],
            [
                'brand' => 'Samsung',
                'model' => 'Galaxy Tab S6',
                'type' => 'tablet',
                'dateFirstCommercialized' => '2019-08-23',
                'isSold' => true,
            ],
            [
                'brand' => 'Google',
                'model' => 'Pixel Slate',
                'type' => 'tablet',
                'dateFirstCommercialized' => '2018-11-22',
                'isSold' => true,
            ],
            [
                'brand' => 'Huawei',
                'model' => 'MatePad Pro',
                'type' => 'tablet',
                'dateFirstCommercialized' => '2019-12-12',
                'isSold' => true,
            ],
            [
                'brand' => 'Microsoft',
                'model' => 'Surface Pro 7',
                'type' => 'tablet',
                'dateFirstCommercialized' => '2019-10-22',
                'isSold' => true,
            ],
        ];

        foreach ($devicesData as $deviceData) {
            $device = (new Device())
                ->setModel($deviceData['model'])
                ->setDateFirstCommercialized(new \DateTimeImmutable($deviceData['dateFirstCommercialized']))
                ->setIsSold($deviceData['isSold'])
                ->setBrand($this->getReference($deviceData['brand']))
                ->setDescription(Faker\Factory::create()->text(200));

            yield $device;
        }
    }
}
