<?php

// Create a data fixture for the Device entity
// Path: src/DataFixtures/DeviceFixture.php
namespace App\DataFixtures;

use App\Entity\Brand;
use App\Entity\Device;
use DateTime;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class DeviceFixture extends Fixture
{
    public function load(ObjectManager $manager)
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
                'model' => 'Galaxy S21',
                'dateFirstCommercialized' => '2021-01-29',
                'isSold' => true,
                'brand' => 'Samsung',
            ],
            [
                'model' => 'iPhone 12',
                'dateFirstCommercialized' => '2020-10-23',
                'isSold' => true,
                'brand' => 'Apple',
            ],
            [
                'model' => 'Pixel 5',
                'dateFirstCommercialized' => '2020-10-15',
                'isSold' => true,
                'brand' => 'Google',
            ],
            [
                'model' => 'Mate 40 Pro',
                'dateFirstCommercialized' => '2020-10-22',
                'isSold' => true,
                'brand' => 'Huawei',
            ],
            [
                'model' => 'Mi 11',
                'dateFirstCommercialized' => '2020-12-28',
                'isSold' => true,
                'brand' => 'Xiaomi',
            ],
            [
                'model' => 'Xperia 1 III',
                'dateFirstCommercialized' => '2021-06-28',
                'isSold' => true,
                'brand' => 'Sony',
            ],
            [
                'model' => 'Moto G Power',
                'dateFirstCommercialized' => '2020-04-16',
                'isSold' => true,
                'brand' => 'Motorola',
            ],
            [
                'model' => 'Wing',
                'dateFirstCommercialized' => '2020-10-15',
                'isSold' => true,
                'brand' => 'LG',
            ],
            [
                'model' => '8.3',
                'dateFirstCommercialized' => '2020-09-22',
                'isSold' => true,
                'brand' => 'Nokia',
            ],
            [
                'model' => '8T',
                'dateFirstCommercialized' => '2020-10-23',
                'isSold' => true,
                'brand' => 'OnePlus',
            ],
            [
                'model' => 'Key2',
                'dateFirstCommercialized' => '2018-06-28',
                'isSold' => true,
                'brand' => 'BlackBerry',
            ],
            [
                'model' => 'U12+',
                'dateFirstCommercialized' => '2018-06-28',
                'isSold' => true,
                'brand' => 'HTC',
            ],
            [
                'model' => 'Find X3 Pro',
                'dateFirstCommercialized' => '2021-03-11',
                'isSold' => true,
                'brand' => 'Oppo',
            ],
            [
                'model' => 'X60 Pro+',
                'dateFirstCommercialized' => '2021-01-21',
                'isSold' => true,
                'brand' => 'Vivo',
            ],
            [
                'model' => 'Galaxy S10',
                'dateFirstCommercialized' => '2019-03-08',
                'isSold' => false,
                'brand' => 'Samsung',
            ],
            [
                'model' => 'iPhone 6',
                'dateFirstCommercialized' => '2014-09-19',
                'isSold' => false,
                'brand' => 'Apple',
            ],
            [
                'model' => 'Nexus 5',
                'dateFirstCommercialized' => '2013-10-31',
                'isSold' => false,
                'brand' => 'Google',
            ],
            [
                'model' => 'P20 Pro',
                'dateFirstCommercialized' => '2018-03-27',
                'isSold' => false,
                'brand' => 'Huawei',
            ],
            [
                'model' => 'Mi 9',
                'dateFirstCommercialized' => '2019-02-20',
                'isSold' => false,
                'brand' => 'Xiaomi',
            ],
            [
                'model' => 'Xperia XZ2',
                'dateFirstCommercialized' => '2018-04-05',
                'isSold' => false,
                'brand' => 'Sony',
            ],
            [
                'model' => 'Moto G7',
                'dateFirstCommercialized' => '2019-02-07',
                'isSold' => false,
                'brand' => 'Motorola',
            ],
            [
                'model' => 'G8 ThinQ',
                'dateFirstCommercialized' => '2019-03-29',
                'isSold' => false,
                'brand' => 'LG',
            ],
            [
                'model' => '7 Plus',
                'dateFirstCommercialized' => '2018-02-25',
                'isSold' => false,
                'brand' => 'Nokia',
            ],
            [
                'model' => '6T',
                'dateFirstCommercialized' => '2018-10-29',
                'isSold' => false,
                'brand' => 'OnePlus',
            ],
            [
                'model' => 'KeyOne',
                'dateFirstCommercialized' => '2017-04-27',
                'isSold' => false,
                'brand' => 'BlackBerry',
            ],
            [
                'model' => 'U11',
                'dateFirstCommercialized' => '2017-06-09',
                'isSold' => false,
                'brand' => 'HTC',
            ],
            [
                'model' => 'Find X2 Pro',
                'dateFirstCommercialized' => '2020-03-06',
                'isSold' => false,
                'brand' => 'Oppo',
            ],
            [
                'model' => 'X50 Pro+',
                'dateFirstCommercialized' => '2020-06-12',
                'isSold' => false,
                'brand' => 'Vivo',
            ],
        ];

        foreach ($devicesData as $deviceData) {
            $device = new Device();
            $device->setModel($deviceData['model']);
            $device->setDateFirstCommercialized(new DateTimeImmutable($deviceData['dateFirstCommercialized']));
            $device->setIsSold($deviceData['isSold']);
            $device->setBrand($this->getReference($deviceData['brand']));

            yield $device;
        }
    }
}
