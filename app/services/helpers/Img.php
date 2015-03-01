<?php
/*
The MIT License (MIT)

Copyright (c) 2014 eve-seat

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/

namespace Seat\Services\Helpers;

/**
 * Class Img
 *
 * @package Seat\Services\Helpers
 */
class Img
{

    const Character = 0;
    const Corporation = 1;
    const Alliance = 2;
    const Type = 3;

    /**
     * @var array
     */
    private static $types = array(
        0 => 'Character',
        1 => 'Corporation',
        2 => 'Alliance',
        3 => 'Type'
    );


    /*
    |--------------------------------------------------------------------------
    | Generate image HTML
    |--------------------------------------------------------------------------
    |
    | Return the HTML for a image. Based on the $id argument, the correct
    | character/corporation/alliance image will be returned. If no type
    | could be determined, a character type is assumed.
    |
    */
    public static function html($id, $size, $attrs, $lazy = true)
    {

        if ($id > 90000000 && $id < 98000000) {

            return self::character($id, $size, $attrs, $lazy);
        }
        elseif (($id > 98000000 && $id < 99000000) || ($id > 1000000 && $id < 2000000)) {

            return self::corporation($id, $size, $attrs, $lazy);
        }
        elseif (($id > 99000000 && $id < 100000000) || ($id > 0 && $id < 1000000)) {

            return self::alliance($id, $size, $attrs, $lazy);
        }

        return self::character($id, $size, $attrs, $lazy);
    }

    /*
    |--------------------------------------------------------------------------
    | Generate character image HTML
    |--------------------------------------------------------------------------
    |
    | Generates a HTML response based on the characterID. If a size of smaller
    | than 32 is received, then the is overwritten with 32.
    |
    */
    public static function character($id, $size, $attrs, $lazy = true)
    {

        if ($size < 32)
            return self::_renderHtml($id, 32, self::Character, $attrs, 32, $lazy);

        return self::_renderHtml($id, $size, self::Character, $attrs, 0, $lazy);
    }

    /*
    |--------------------------------------------------------------------------
    | Generate corporation image HTML
    |--------------------------------------------------------------------------
    |
    | Generates a HTML response based on the corporationID. If a size of smaller
    | than 32 is received, then the is overwritten with 32.
    |
    */
    public static function corporation($id, $size, $attrs, $lazy = true)
    {

        if ($size < 32)
            return self::_renderHtml($id, 32, self::Corporation, $attrs, 32, $lazy);

        return self::_renderHtml($id, $size, self::Corporation, $attrs, 0, $lazy);
    }

    /*
    |--------------------------------------------------------------------------
    | Generate alliance image HTML
    |--------------------------------------------------------------------------
    |
    | Generates a HTML response based on the allianceID. If a size of smaller
    | than 32 is received, then the is overwritten with 32.
    |
    */
    public static function alliance($id, $size, $attrs, $lazy = true)
    {

        if ($size < 32)
            return self::_renderHtml($id, 32, self::Alliance, $attrs, 32, $lazy);

        return self::_renderHtml($id, $size, self::Alliance, $attrs, 0, $lazy);
    }

    /*
    |--------------------------------------------------------------------------
    | Generate type image HTML
    |--------------------------------------------------------------------------
    |
    | Generates a HTML response based on the typeID. If a size of smaller
    | than 32 is received, then the is overwritten with 32.
    |
    */
    public static function type($id, $size, $attrs, $lazy = true)
    {

        if ($size < 32)
            return self::_renderHtml($id, 32, self::Type, $attrs, 32, $lazy);

        return self::_renderHtml($id, $size, self::Type, $attrs, 0, $lazy);
    }

    /*
    |--------------------------------------------------------------------------
    | Generate a image URL that is lazy load ready
    |--------------------------------------------------------------------------
    |
    | Generates a HTML response that will put a placeholder instead of the
    | actual image. This image however is later lazy loaded client side
    | based on the data-src attribute.
    |
    */
    public static function _renderHtml($id, $size, $type, $attrs, $retina_size = 0, $lazy = true)
    {

        // fix default retina image size
        if ($retina_size === 0)
            $retina_size = $size * 2;

        // make new IMG tag
        $html = '<img ';

        if ($lazy) {

            // images are lazy loaded. prepare the the data-src attributes with the
            // location for the image.
            $html .= 'src="' . \URL::asset('assets/img/bg.png') . '" ';
            $html .= 'data-src="' . self::_renderUrl($id, $size, $type) . '" ';
            $html .= 'data-src-retina="' . self::_renderUrl($id, $retina_size, $type) . '" ';

            // put class on images to lazy load them
            if (!isset($attrs['class']))
                $attrs['class'] = '';

            $attrs['class'] .= ' img-lazy-load';

        } else {

            // no lazy loaded image
            $html .= 'src="' . self::_renderUrl($id, $size, $type) . '" ';
        }

        // unset already built attributes
        unset($attrs['src'], $attrs['data-src='], $attrs['data-src-retina']);

        // render other attributes
        foreach ($attrs as $name => $value)
            $html .= "{$name}=\"{$value}\" ";

        // close IMG tag
        $html .= ' />';

        // return completed img tag
        return $html;
    }

    /*
    |--------------------------------------------------------------------------
    | Generate a image URL
    |--------------------------------------------------------------------------
    |
    | Generates a URL from the CCP image server based on the recieved
    | size and image type.
    |
    */
    public static function _renderUrl($id, $size, $type)
    {

        // Base Eve Online Image CDN url
        $url = '//image.eveonline.com/';

        // construct ending bit of URL
        switch ($type) {
            case self::Corporation:
                $url .= self::$types[self::Corporation] . '/' . $id . '_' . $size . '.png';
                break;

            case self::Alliance:
                $url .= self::$types[self::Alliance] . '/' . $id . '_' . $size . '.png';
                break;

            case self::Type:
                $url .= self::$types[self::Type] . '/' . $id . '_' . $size . '.png';
                break;

            case self::Character:
            default:
                $url .= self::$types[self::Character] . '/' . $id . '_' . $size . '.jpg';
        }

        // return full URL
        return $url;
    }

}
