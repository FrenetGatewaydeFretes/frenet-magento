<?php

class Frenet_Shipping_Model_DependencyFinder
{
    public static function includeDependency()
    {
        $path = implode(DS, [
            Mage::getBaseDir('lib'),
            'Frenet',
            'Shipping',
            'vendor',
            'autoload.php',
        ]);

        if (!file_exists($path)) {
            /** Maybe here an Exception should be thrown */
            return;
        }

        include_once $path;
    }
}
