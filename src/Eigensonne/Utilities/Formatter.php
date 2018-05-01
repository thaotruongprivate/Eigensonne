<?php
/**
 * Created by PhpStorm.
 * User: thao.truong
 * Date: 01.05.18
 * Time: 21:30
 */

namespace Eigensonne\Utilities;


class Formatter {

    static function timeIntervalRelativeToNow(int $timestamp): string {

        $diff = time() - $timestamp;

        $minutes = floor($diff / 60);
        if ($minutes < 60) {
            return sprintf('%d minute' . ($minutes > 1 ? 's' : '') . ' ago', $minutes);
        }

        $hours = floor($minutes / 60);
        if ($hours < 24) {
            return sprintf('%d hour' . ($hours > 1 ? 's' : '') . ' ago', $hours);
        }

        $days = floor($hours / 24);
        if ($days < 30) {
            return sprintf('%d day' . ($days > 1 ? 's' : '') . ' ago', $days);
        }

        $months = floor($days / 30);
        if ($months < 12) {
            return sprintf('%d month' . ($months > 1 ? 's' : '') . ' ago', $months);
        }

        $years = floor($days / 365);
        return sprintf('%d year' . ($years > 1 ? 's' : '') . ' ago', $years);
    }

}