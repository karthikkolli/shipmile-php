<?php

namespace Shipmile\Util;

use Shipmile\Object;

abstract class Util
{
    /**
     * Whether the provided array (or other) is a list rather than a dictionary.
     *
     * @param array|mixed $array
     * @return boolean True if the given object is a list.
     */
    public static function isList($array)
    {
        if (!is_array($array)) {
            return false;
        }

        // TODO: generally incorrect, but it's correct given Shipmile's response
        foreach (array_keys($array) as $k) {
            if (!is_numeric($k)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Recursively converts the PHP Shipmile object to an array.
     *
     * @param array $values The PHP Shipmile object to convert.
     * @return array
     */
    public static function convertShipmileObjectToArray($values)
    {
        $results = array();
        foreach ($values as $k => $v) {
            // FIXME: this is an encapsulation violation
            if ($k[0] == '_') {
                continue;
            }
            if ($v instanceof Object) {
                $results[$k] = $v->__toArray(true);
            } elseif (is_array($v)) {
                $results[$k] = self::convertShipmileObjectToArray($v);
            } else {
                $results[$k] = $v;
            }
        }
        return $results;
    }

    /**
     * Converts a response from the Shipmile API to the corresponding PHP object.
     *
     * @param array $resp The response from the Shipmile API.
     * @param array $opts
     * @return Object|array
     */
    public static function convertToShipmileObject($resp, $opts)
    {
        $types = array(
            'account' => 'Shipmile\\Account',
            'card' => 'Shipmile\\Card',
            'charge' => 'Shipmile\\Charge',
            'coupon' => 'Shipmile\\Coupon',
            'customer' => 'Shipmile\\Customer',
            'list' => 'Shipmile\\Collection',
            'invoice' => 'Shipmile\\Invoice',
            'invoiceitem' => 'Shipmile\\InvoiceItem',
            'event' => 'Shipmile\\Event',
            'file' => 'Shipmile\\FileUpload',
            'token' => 'Shipmile\\Token',
            'transfer' => 'Shipmile\\Transfer',
            'plan' => 'Shipmile\\Plan',
            'recipient' => 'Shipmile\\Recipient',
            'refund' => 'Shipmile\\Refund',
            'subscription' => 'Shipmile\\Subscription',
            'fee_refund' => 'Shipmile\\ApplicationFeeRefund',
            'bitcoin_receiver' => 'Shipmile\\BitcoinReceiver',
            'bitcoin_transaction' => 'Shipmile\\BitcoinTransaction',
        );
        if (self::isList($resp)) {
            $mapped = array();
            foreach ($resp as $i) {
                array_push($mapped, self::convertToShipmileObject($i, $opts));
            }
            return $mapped;
        } elseif (is_array($resp)) {
            if (isset($resp['object']) && is_string($resp['object']) && isset($types[$resp['object']])) {
                $class = $types[$resp['object']];
            } else {
                $class = 'Shipmile\\Object';
            }
            return $class::constructFrom($resp, $opts);
        } else {
            return $resp;
        }
    }
}