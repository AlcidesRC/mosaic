<?php

declare(strict_types=1);

namespace AlcidesRC\Mosaic;

use AlcidesRC\Colors\ColorHex;
use AlcidesRC\Histogram\Histogram;
use Jenssegers\ImageHash\Hash;
use Jenssegers\ImageHash\ImageHash;
use Jenssegers\ImageHash\Implementations\DifferenceHash;

class Mosaic
{
    //-----------------------------------------------------------------------------------------------------------------
    // PROPERTIES
    //-----------------------------------------------------------------------------------------------------------------

    /**
     * Source image resource
     *
     * @var Histogram
     */
    private $source;

    /**
     * Target image resource
     *
     * @var resource|false
     */
    private $target;

    /**
     * HTML table container
     *
     * @var array<string>
     */
    private $htmlTable;

    /**
     * Images container
     *
     * @var array
     */
    private $images;

    /**
     * Perceptual Hasher container
     *
     * @var ImageHash
     */
    private $hasher;

    //-----------------------------------------------------------------------------------------------------------------
    // PUBLIC METHODS
    //-----------------------------------------------------------------------------------------------------------------

    /**
     * @access public
     *
     * @param  string $filename
     * @param  bool $enableLog
     *
     * @return void
     */
    public function __construct(string $filename)
    {
        $this->htmlTable = [];
        $this->images    = [];

        $this->hasher = new ImageHash(new DifferenceHash());

        $this->source = new Histogram();
        $this->source->loadFromFile($filename);

        $this->target = \imagecreatetruecolor(
            $this->source->getDetails()['size']['width'],
            $this->source->getDetails()['size']['height']
        );
        \imagealphablending($this->target, false);
        \imagesavealpha($this->target, true);
    }

    //-----------------------------------------------------------------------------------------------------------------

    /**
     * @access public
     *
     * @param  string $pattern
     * @param  string $pathCache
     * @param  bool $reloadCache
     *
     * @return void
     */
    public function loadImages(string $pattern, string $pathCache, bool $reloadCache = false) : void
    {
        if (file_exists($pathCache) && ! $reloadCache) {
            $this->images = json_decode(file_get_contents($pathCache), true);
            return;
        }

        $this->images = Histogram::processImages($pattern, $pathCache, $reloadCache);

        // Attach the pHash as Integer
        $this->images = array_map(function ($image) {
            $filename = $image['details']['dirname'] .'/'. $image['details']['basename'];

            $image['hash'] = $this->hasher->hash($filename)->toInt();

            return $image;
        }, $this->images);

        file_put_contents($pathCache, json_encode($this->images));
    }

    //-----------------------------------------------------------------------------------------------------------------

    /**
     * @access public
     *
     * @param  int $cols
     * @param  int $rows
     *
     * @return array
     */
    public function createWithPlainColors(int $cols, int $rows) : array
    {
        $sectionWidth  = (int) floor($this->source->getDetails()['size']['width'] / $cols);
        $sectionHeight = (int) floor($this->source->getDetails()['size']['height'] / $rows);

        foreach (range(1, $rows) as $row) {
            foreach (range(1, $cols) as $col) {
                $this->source->setArea(
                    (int) (($col - 1) * $sectionWidth),
                    (int) (($row - 1) * $sectionHeight),
                    (int) ($col * $sectionWidth),
                    (int) ($row * $sectionHeight)
                );

                $hexColor = $this->source->getAverageColor();

                $this->htmlTable[$row][$col] = [
                    'p1x'   => $this->source->getArea()['p1']->x,
                    'p1y'   => $this->source->getArea()['p1']->y,
                    'p2x'   => $this->source->getArea()['p2']->x,
                    'p2y'   => $this->source->getArea()['p2']->y,
                    'color' => $hexColor,
                ];

                $this->fillAreaWithColor($hexColor, $sectionWidth, $sectionHeight);
            }
        }

        return [
            $this->saveAsPng($rows, $cols, $sectionWidth, $sectionHeight),
            $this->saveAsHtml($rows, $cols, $sectionWidth, $sectionHeight),
        ];
    }

    //-----------------------------------------------------------------------------------------------------------------

    /**
     * @access public
     *
     * @param  int $cols
     * @param  int $rows
     *
     * @return array
     */
    public function createWithImagesByAverageColors(int $cols, int $rows) : array
    {
        $sectionWidth  = (int) floor($this->source->getDetails()['size']['width'] / $cols);
        $sectionHeight = (int) floor($this->source->getDetails()['size']['height'] / $rows);

        foreach (range(1, $rows) as $row) {
            foreach (range(1, $cols) as $col) {
                $this->source->setArea(
                    (int) (($col - 1) * $sectionWidth),
                    (int) (($row - 1) * $sectionHeight),
                    (int) ($col * $sectionWidth),
                    (int) ($row * $sectionHeight)
                );

                $hexColor = $this->source->getAverageColor();

                $this->htmlTable[$row][$col] = [
                    'p1x'   => $this->source->getArea()['p1']->x,
                    'p1y'   => $this->source->getArea()['p1']->y,
                    'p2x'   => $this->source->getArea()['p2']->x,
                    'p2y'   => $this->source->getArea()['p2']->y,
                    'color' => $hexColor,
                ];

                $closestImage = $this->findClosestImageByColorDifference($hexColor);

                $this->fillAreaWithImage($closestImage, $sectionWidth, $sectionHeight);
            }
        }

        return [
            $this->saveAsPng($rows, $cols, $sectionWidth, $sectionHeight),
            $this->saveAsHtml($rows, $cols, $sectionWidth, $sectionHeight),
        ];
    }

    //-----------------------------------------------------------------------------------------------------------------

    /**
     * @access public
     *
     * @param  int $cols
     * @param  int $rows
     *
     * @return array
     */
    public function createWithImagesByPerceptualHashes(int $cols, int $rows) : array
    {
        $sectionWidth  = (int) floor($this->source->getDetails()['size']['width'] / $cols);
        $sectionHeight = (int) floor($this->source->getDetails()['size']['height'] / $rows);

        foreach (range(1, $rows) as $row) {
            foreach (range(1, $cols) as $col) {
                $this->source->setArea(
                    (int) (($col - 1) * $sectionWidth),
                    (int) (($row - 1) * $sectionHeight),
                    (int) ($col * $sectionWidth),
                    (int) ($row * $sectionHeight)
                );

                $area = $this->source->getArea();

                $hexColor = $this->source->getAverageColor();

                $this->htmlTable[$row][$col] = [
                    'p1x'   => $area['p1']->x,
                    'p1y'   => $area['p1']->y,
                    'p2x'   => $area['p2']->x,
                    'p2y'   => $area['p2']->y,
                    'color' => $hexColor,
                ];

                $closestImage = $this->findClosestImageByPerceptualHash(
                    $area['p1']->x,
                    $area['p1']->y,
                    $area['p2']->x,
                    $area['p2']->y
                );

                $this->fillAreaWithImage($closestImage, $sectionWidth, $sectionHeight);
            }
        }

        return [
            $this->saveAsPng($rows, $cols, $sectionWidth, $sectionHeight),
            $this->saveAsHtml($rows, $cols, $sectionWidth, $sectionHeight),
        ];
    }

    //-----------------------------------------------------------------------------------------------------------------
    // PRIVATED METHODS
    //-----------------------------------------------------------------------------------------------------------------

    /**
     * @access private
     *
     * @param  string $hexColor
     * @param  int $sectionWidth
     * @param  int $sectionHeight
     *
     * @return array
     */
    private function fillAreaWithColor(string $hexColor, int $sectionWidth, int $sectionHeight) : void
    {
        $rgbaColor = ColorHex::toRgba($hexColor);

        \imagefilledrectangle(
            $this->target,
            $this->source->getArea()['p1']->x,
            $this->source->getArea()['p1']->y,
            $this->source->getArea()['p2']->x,
            $this->source->getArea()['p2']->y,
            \imagecolorallocate(
                $this->target,
                (int) $rgbaColor['r'],
                (int) $rgbaColor['g'],
                (int) $rgbaColor['b']
            )
        );
    }

    //-----------------------------------------------------------------------------------------------------------------

    /**
     * @access private
     *
     * @param  array $closestImage
     * @param  int $sectionWidth
     * @param  int $sectionHeight
     *
     * @return array
     */
    private function fillAreaWithImage(array $closestImage, int $sectionWidth, int $sectionHeight) : void
    {
        $closest = \imagecreatefromstring(
            file_get_contents(
                $closestImage['details']['dirname'] .'/'. $closestImage['details']['basename']
            )
        );

        \imagecopyresampled(
            $this->target,
            $closest,
            $this->source->getArea()['p1']->x,
            $this->source->getArea()['p1']->y,
            0,
            0,
            $sectionWidth,
            $sectionHeight,
            $closestImage['details']['size']['width'],
            $closestImage['details']['size']['height']
        );

        \imagedestroy($closest);
    }

    //-----------------------------------------------------------------------------------------------------------------

    /**
     * @access private
     *
     * @param  string $hexColor
     *
     * @return array
     */
    private function findClosestImageByColorDifference(string $hexColor) : array
    {
        // Recalculate the distance
        $this->images = array_map(function ($image) use ($hexColor) {
            $image['color-distance'] = ColorHex::distanceCie76($hexColor, $image['colors']['average']);
            return $image;
        }, $this->images);

        // Sort the images by distance
        array_multisort(
            array_map(function($image) {
                return $image['color-distance'];
            }, $this->images
        ), SORT_ASC, $this->images);

        return $this->images[0];
    }

    //-----------------------------------------------------------------------------------------------------------------

    /**
     * @access private
     *
     * @param  int $p1x
     * @param  int $p1y
     * @param  int $p2x
     * @param  int $p2y
     *
     * @return array
     */
    private function findClosestImageByPerceptualHash(int $p1x, int $p1y, int $p2x, int $p2y) : array
    {
        $getSectionHash = function (int $p1x, int $p1y, int $p2x, int $p2y) : Hash {
            $filename = tempnam('/tmp', 'mosaic');

            // Create a section image
            $width  = $p2x - $p1x;
            $height = $p2y - $p1y;
            $partial = \imagecreatetruecolor($width, $height);
            \imagealphablending($partial, false);
            \imagesavealpha($partial, true);
            \imagecopy($partial, $this->source->getDetails(true)['resource'], 0, 0, $p1x, $p1y, $width, $height);
            \imagepng($partial, $filename);

            // Calculate the image hash
            $hash = $this->hasher->hash($filename);

            // Destroy unnecesary image
            \imagedestroy($partial);
            unlink($filename);

            return $hash;
        };

        $sourceHash = $getSectionHash($p1x, $p1y, $p2x, $p2y);

        // Recalculate the distance
        $this->images = array_map(function ($image) use ($sourceHash) {
            $image['hash-distance'] = $sourceHash->distance(
                Hash::fromInt($image['hash'])
            );

            return $image;
        }, $this->images);

        // Sort the images by distance
        array_multisort(
            array_map(function($image) {
                return $image['hash-distance'];
            }, $this->images
        ), SORT_ASC, $this->images);

        return $this->images[0];
    }

    //-----------------------------------------------------------------------------------------------------------------

    /**
     * Save as PNG
     *
     * @access private
     *
     * @param  int $rows
     * @param  int $cols
     * @param  int $sectionWidth
     * @param  int $sectionHeight
     *
     * @return string
     */
    private function saveAsPng(int $rows, int $cols, int $sectionWidth, int $sectionHeight) : string
    {
        $filename = sprintf(
            '%s/%s-%dx%d.png',
            $this->source->getDetails()['dirname'],
            $this->source->getDetails()['filename'],
            $rows,
            $cols
        );

        \imagepng($this->target, $filename);

        return $filename;
    }

    //-----------------------------------------------------------------------------------------------------------------

    /**
     * Save as HTML
     *
     * @access private
     *
     * @param  int $rows
     * @param  int $cols
     * @param  int $sectionWidth
     * @param  int $sectionHeight
     *
     * @return string
     */
    private function saveAsHtml(int $rows, int $cols, int $sectionWidth, int $sectionHeight) : string
    {
        $filename = sprintf(
            '%s/%s-%dx%d.html',
            $this->source->getDetails()['dirname'],
            $this->source->getDetails()['filename'],
            $rows,
            $cols
        );

        $getTableBody = function () : string {
            $tbody = [];
            foreach ($this->htmlTable as $row => $aux) {
                $tr = [];

                $tr[] = '<tr>';
                foreach ($aux as $col => $data) {
                    $tr[] = sprintf(
                        '<td data-section="(%d,%d) - (%d,%d)" style="background-color: %s"></td>',
                        $data['p1x'],
                        $data['p1y'],
                        $data['p2x'],
                        $data['p2y'],
                        $data['color']
                    );
                }
                $tr[] = '</tr>';

                $tbody[] = implode(PHP_EOL, $tr);
            }

            return implode(PHP_EOL, $tbody);
        };

        $template = file_get_contents(__DIR__ .'/Mosaic.tpl.php');

        file_put_contents($filename, strtr($template, [
            '__TITLE__'   => sprintf(
                'Source [ %s ] - Mosaic [ %d x %d ]',
                $this->source->getDetails()['basename'],
                $rows,
                $cols
            ),
            '__CAPTION__' => sprintf(
                'Source [ %s ] - Mosaic [ %d x %d ]',
                $this->source->getDetails()['basename'],
                $rows,
                $cols
            ),
            '__TBODY__'       => $getTableBody(),
            '__CELL_WIDTH__'  => $sectionWidth,
            '__CELL_HEIGHT__' => $sectionHeight,
        ]));

        return $filename;
    }

    //-----------------------------------------------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------------------------------------
}
