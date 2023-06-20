<?php

namespace App\Libs\Meta;

class CustomerMetaConfig extends MetaConfig
{
    const SALES_ID = 'sales_id';
    const COA_ID = 'coa_id';
    const INDUSTRY_CATEGORY_ID = 'industri_category_id';
    const BILLING_ADDRESS = 'billing_address';
    const FACTORY = 'factory';
    const COLLECTOR_NAME = 'collector_name';

    public function __construct($list = [])
    {
        if(empty($list)) {
            $list = $this->generateList();
        }

        parent::__construct($list);
    }

    public static function getIgnoreKeys()
    {
        return [];
    }

    private function generateList()
    {
        return [
            self::SALES_ID,
            self::COA_ID,
            self::INDUSTRY_CATEGORY_ID,
            self::BILLING_ADDRESS,
            self::FACTORY,
            self::COLLECTOR_NAME
        ];
    }
}
