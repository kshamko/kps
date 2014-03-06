<?php
class Kps_Tool_Provider_Manifest implements Zend_Tool_Framework_Manifest_ProviderManifestable
{
     public function getProviders()
    {
        return array(
            new Kps_Tool_Provider_Forms()
        );
    }
}