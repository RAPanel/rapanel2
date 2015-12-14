<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace ra\admin\helpers;

use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;
use Imagine\Image\Point;
use Yii;
use yii\base\InvalidConfigException;

/**
 * Image implements most commonly used image manipulation functions using the [Imagine library](http://imagine.readthedocs.org/).
 *
 * Example of use:
 *
 * ~~~php
 * // generate a thumbnail image
 * Image::thumbnail('@webroot/img/test-image.jpg', 120, 120)
 *     ->save(Yii::getAlias('@runtime/thumb-test-image.jpg'), ['quality' => 50]);
 * ~~~
 *
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Image
{
    /**
     * GD2 driver definition for Imagine implementation using the GD library.
     */
    const DRIVER_GD2 = 'gd2';
    /**
     * imagick driver definition.
     */
    const DRIVER_IMAGICK = 'imagick';
    /**
     * gmagick driver definition.
     */
    const DRIVER_GMAGICK = 'gmagick';

    const IMAGE_RESIZE = 0;
    const IMAGE_CROP = 1;
    const IMAGE_BOX = 2;

    /**
     * @var array|string the driver to use. This can be either a single driver name or an array of driver names.
     * If the latter, the first available driver will be used.
     */
    public static $driver = [self::DRIVER_GMAGICK, self::DRIVER_IMAGICK, self::DRIVER_GD2];

    /**
     * @var ImagineInterface instance.
     */
    private static $_imagine;

    public static function thumbnail($filename, $width = 0, $height = 0, $crop = self::IMAGE_RESIZE)
    {
        $image = self::getImagine()->open($filename);

        // Уменьшаем размеры
        $k = $image->getSize()->getWidth() / $image->getSize()->getHeight();

        if (!$width) $width = $height * $k;
        if (!$height) $height = $width / $k;

        // Считаем насколько больше ли новые размеры
        $resize = max($width / $image->getSize()->getWidth(), $height / $image->getSize()->getHeight());
        if ($resize < 1) $resize = 1;

        $box = new Box($width, $height);

        // Запрещаем увеличение
        $newWidth = $width = $width / $resize;
        $newHeight = $height = $height / $resize;

        if ($k != $width / $height) {
            if (!$crop) {
                if ($k > 0)
                    $newWidth = $newHeight * $k;
                else
                    $newHeight = $newWidth / $k;
            } else {
                if ($k > $width / $height) {
                    if ($crop == 1)
                        $newWidth = $k > 1 ? $newHeight * $k : $newHeight / $k;
                    else
                        $newHeight = $k > 1 ? $newWidth * $k : $newWidth / $k;
                } else {
                    if ($crop == 2)
                        $newWidth = $k > 1 ? $newHeight * $k : $newHeight / $k;
                    else
                        $newHeight = $newWidth / $k;
                }
            }

        } else $crop = false;

        $image->resize(new Box($newWidth, $newHeight), ImageInterface::FILTER_LANCZOS);

        if ($crop) {

            // Обрезаем лишнее
            $startX = 0;
            $startY = 0;
            $size = $image->getSize();
            if ($size->getWidth() > $width) {
                $startX = ceil($size->getWidth() - $width) / 2;
            }
            if ($size->getHeight() > $height) {
                $startY = ceil($size->getHeight() - $height) / 2;
            }

            $image->crop(new Point($startX, $startY), $box);

            // Делаем белый фон
            if (($size->getWidth() < round($width) || $size->getHeight() < round($height))) {
                $thumb = Image::getImagine()->create($box);

                $size = $image->getSize();

                $startX = 0;
                $startY = 0;
                if ($size->getWidth() < $box->getWidth()) {
                    $startX = ceil($box->getWidth() - $size->getWidth()) / 2;
                }
                if ($size->getHeight() < $box->getHeight()) {
                    $startY = ceil($box->getHeight() - $size->getHeight()) / 2;
                }
                $thumb->paste($image, new Point($startX, $startY));
            } else
                $thumb = $image;
        } else
            $thumb = $image;

        $thumb->interlace(ImageInterface::INTERLACE_PARTITION);

        return $thumb;
    }

    public static function getImagine()
    {
        if (self::$_imagine === null) {
            self::$_imagine = static::createImagine();
        }

        return self::$_imagine;
    }

    /**
     * @param ImagineInterface $imagine the `Imagine` object.
     */
    public static function setImagine($imagine)
    {
        self::$_imagine = $imagine;
    }

    /**
     * Creates an `Imagine` object based on the specified [[driver]].
     * @return ImagineInterface the new `Imagine` object
     * @throws InvalidConfigException if [[driver]] is unknown or the system doesn't support any [[driver]].
     */
    protected static function createImagine()
    {
        foreach ((array)static::$driver as $driver) {
            switch ($driver) {
                case self::DRIVER_GMAGICK:
                    if (class_exists('Gmagick', false)) {
                        return new \Imagine\Gmagick\Imagine();
                    }
                    break;
                case self::DRIVER_IMAGICK:
                    if (class_exists('Imagick', false)) {
                        return new \Imagine\Imagick\Imagine();
                    }
                    break;
                case self::DRIVER_GD2:
                    if (function_exists('gd_info')) {
                        return new \Imagine\Gd\Imagine();
                    }
                    break;
                default:
                    throw new InvalidConfigException("Unknown driver: $driver");
            }
        }
        throw new InvalidConfigException("Your system does not support any of these drivers: " . implode(',', (array)static::$driver));
    }
}
