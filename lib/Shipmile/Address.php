<?php

namespace Shipmile;


class Address extends ApiResource
{
    /**
     * @param string $id The ID of the address to retrieve.
     * @param array|string|null $opts
     *
     * @return Address
     */
    public static function get($id, $opts = null)
    {
        return self::_get($id, $opts);
    }

    /**
     * @param array|null $params
     * @param array|string|null $opts
     *
     * @return Address The created Address.
     */
    public static function create($params = null, $opts = null)
    {
        return self::_create($params, $opts);
    }
}