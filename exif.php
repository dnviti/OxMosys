<?php

$exif = exif_read_data('assets/img/upd/43.jpg'); //parameter should be image path

if (isset($exif['Orientation'])) {

    if ($exif['Orientation'] === 1) print 'rotated clockwise by 0 deg (nothing)';

    if ($exif['Orientation'] === 8) print 'rotated clockwise by 90 deg';

    if ($exif['Orientation'] === 3) print 'rotated clockwise by 180 deg';

    if ($exif['Orientation'] === 6) print 'rotated clockwise by 270 deg';

    if ($exif['Orientation'] === 2) print 'vertical flip, rotated clockwise by 0 deg';

    if ($exif['Orientation'] === 7) print 'vertical flip, rotated clockwise by 90 deg';

    if ($exif['Orientation'] === 4) print 'vertical flip, rotated clockwise by 180 deg';

    if ($exif['Orientation'] === 5) print 'vertical flip, rotated clockwise by 270 deg';
}

var_dump($exif);
