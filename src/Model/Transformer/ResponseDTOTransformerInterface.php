<?php

namespace App\Model\Transformer;

interface ResponseDTOTransformerInterface
{
    public static function transformFromObject($object);
}