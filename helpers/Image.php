<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\admin\helpers;

use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;
use Imagine\Image\Palette\Color\RGB;
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

    /**
     * @var array|string the driver to use. This can be either a single driver name or an array of driver names.
     * If the latter, the first available driver will be used.
     */
    public static $driver = [self::DRIVER_GMAGICK, self::DRIVER_IMAGICK, self::DRIVER_GD2];

    /**
     * @var ImagineInterface instance.
     */
    private static $_imagine;

    public static function getImagine()
    {
        if (self::$_imagine === null) {
            self::$_imagine = static::createImagine();
        }

        return self::$_imagine;
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

    /**
     * @param ImagineInterface $imagine the `Imagine` object.
     */
    public static function setImagine($imagine)
    {
        self::$_imagine = $imagine;
    }

    public static function thumbnail($filename, $width = 0, $height = 0, $inside = true)
    {

        $image = self::getImagine()->open($filename);

        // Уменьшаем размеры
        $k = $image->getSize()->getWidth() / $image->getSize()->getHeight();

        if (!$width) $width = $height * $k;
        if (!$height) $height = $width / $k;

        $newWidth = $width;
        $newHeight = $height;

        if ($inside && $k != $width / $height) {
            $newWidth = $k > 1 ? $newWidth * $k : $newWidth / $k;
            $newHeight = $k > 1 ? $newHeight * $k : $newHeight / $k;
        }

        if ($newWidth / $newHeight > $k) $newWidth = round($newHeight * $k);
        else $newHeight = round($newWidth / $k);

        $image->resize(new Box($newWidth, $newHeight), ImageInterface::FILTER_LANCZOS);

        $box = new Box($width, $height);

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
        if ($size->getWidth() < round($width) || $size->getHeight() < round($height)) {
            $thumb = Image::getImagine()->create(new Box($width, $height));

            $size = $image->getSize();

            $startX = 0;
            $startY = 0;
            if ($size->getWidth() < $width) {
                $startX = ceil($width - $size->getWidth()) / 2;
            }
            if ($size->getHeight() < $height) {
                $startY = ceil($height - $size->getHeight()) / 2;
            }
            $thumb->paste($image, new Point($startX, $startY));
        } else
            $thumb = $image;

        $thumb->interlace(ImageInterface::INTERLACE_PARTITION);

        return $thumb;
    }
}
