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

        // Считаем насколько и больше ли новые размеры
        $resize = min($width / $image->getSize()->getWidth(), $height / $image->getSize()->getHeight());
        if ($resize < 1) $resize = 1;

        // Не увеличиваем, если размер маленький
        $resizeWidth = $width / $resize;
        $resizeHeight = $height / $resize;

        // если пропорции равны - делаем просто ресайз
        if ($k == $resizeWidth / $resizeHeight) $crop = self::IMAGE_RESIZE;

        switch ($crop):
            case self::IMAGE_BOX:
            case self::IMAGE_RESIZE:
                $smaller = max($image->getSize()->getWidth() / $resizeWidth, $image->getSize()->getHeight() / $resizeHeight);
                break;
            case self::IMAGE_CROP:
                $smaller = min($image->getSize()->getWidth() / $resizeWidth, $image->getSize()->getHeight() / $resizeHeight);
                break;
            default:
                $smaller = 1;
        endswitch;

        list($resizeWidth, $resizeHeight) = [$image->getSize()->getWidth() / $smaller, $image->getSize()->getHeight() / $smaller];

        $image->resize(new Box($resizeWidth, $resizeHeight), ImageInterface::FILTER_LANCZOS);

        if ($crop) {
            $box = new Box($width, $height);

            if($crop == self::IMAGE_CROP){
                // Обрезаем лишнее
                $startX = 0;
                $startY = 0;
                if ($image->getSize()->getWidth() > $width) {
                    $startX = ceil($image->getSize()->getWidth() - $width) / 2;
                }
                if ($image->getSize()->getHeight() > $height) {
                    $startY = ceil($image->getSize()->getHeight() - $height) / 2;
                }
                $image->crop(new Point($startX, $startY), $box);
            }


            // Делаем белый фон
            if (($image->getSize()->getWidth() < round($width) || $image->getSize()->getHeight() < round($height))) {
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
