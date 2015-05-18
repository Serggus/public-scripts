<?php

namespace ChrisRNutanix\APIDemo\Utilities;

class Formatting
{

    /**
     * Take a number and format it as human-readable 'disk space'
     *
     * @param $size
     * @param string $unit
     * @return string
     */
    public static function humanFileSize( $size, $unit = "" )
    {
        if ((!$unit && $size >= 1 << 30) || $unit == 'GB')
        {
            return number_format($size / (1 << 30), 2) . 'GB';
        }
        if ((!$unit && $size >= 1 << 20) || $unit == 'MB')
        {
            return number_format($size / (1 << 20), 2) . 'MB';
        }
        if ((!$unit && $size >= 1 << 10) || $unit == 'KB')
        {
            return number_format($size / (1 << 10), 2) . 'KB';
        }
        return number_format( $size ) . ' bytes';
    }

}