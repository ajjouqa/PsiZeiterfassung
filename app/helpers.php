<?php

if (! function_exists('roundToQuarter')) {
    function roundToQuarter($start, $end)
    {
        if (!$start || !$end)
            return '00:00:00';
        $seconds = $start->diffInSeconds($end);
        $rounded = round($seconds / (15 * 60)) * (15 * 60);
        $h = floor($rounded / 3600);
        $m = floor(($rounded % 3600) / 60);
        $s = $rounded % 60;
        return sprintf('%02d:%02d:%02d', $h, $m, $s);
    }
}




