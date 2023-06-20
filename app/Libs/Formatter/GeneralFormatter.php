<?php

namespace App\Libs\Formatter;

use App\Libs\Helpers\ResourceUrl;
use App\Libs\Repository\Finder\GiftCardCashFinder;
use App\Libs\Repository\Finder\OfferwallFinder;

class GeneralFormatter
{
    public static function getGiftCardList(GiftCardCashFinder $finder)
    {
        $paginator = $finder->get();

        $data = [];
        foreach ($paginator as $x) {
            $data[] = [
                'id' => $x->id,
                'category' => $x->category,
                'name' => $x->name,
                'price' => round($x->price),
                'notes' => $x->notes,
                'image' => ResourceUrl::item($x->image)
            ];
        }

        return $data;
    }

    public static function getOfferwallList(OfferwallFinder $finder, $personId)
    {
        $paginator = $finder->get();

        $data = [];
        foreach ($paginator as $x) {
            $model = $x->toArray();

            $model['url'] = str_replace('{user_id}', $personId, $model['url']);

            $data[] = $model;
        }

        return $data;
    }
}
