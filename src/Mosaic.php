<?php

declare(strict_types=1);

namespace AlcidesRC\Mosaic;

use AlcidesRC\Colors\ColorHex;
use AlcidesRC\Histogram\Histogram;

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
     * Images information container
     *
     * @var array
     */
    private $images;

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
        $this->images = Histogram::processImages($pattern, $pathCache, $reloadCache);
    }

    //-----------------------------------------------------------------------------------------------------------------

    /**
     * @access public
     *
     * @param  int $cols
     * @param  int $rows
     * @param  bool $withImages
     *
     * @return array
     */
    public function create(int $cols, int $rows, bool $withImages = false) : array
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

                if (count($this->images) && $withImages) {
                    $this->fillAreaWithImage($hexColor, $sectionWidth, $sectionHeight);

                    continue;
                }

                $this->fillAreaWithColor($hexColor, $sectionWidth, $sectionHeight);
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
     *
     * @return array
     */
    private function findClosestImage(string $hexColor) : array
    {
        $currentDistance = PHP_INT_MAX;
        $image           = null;

        foreach ($this->images as $entry) {
            $distance = ColorHex::distanceCie76($hexColor, $entry['colors']['average']);

            if ($distance < $currentDistance) {
                $currentDistance = $distance;
                $image           = $entry;
            }
        }

        return $image;
    }

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
    private function fillAreaWithImage(string $hexColor, int $sectionWidth, int $sectionHeight) : void
    {
        $closest = $this->findClosestImage($hexColor);

        $source = \imagecreatefromstring(
            file_get_contents(
                $closest['details']['dirname'] .'/'. $closest['details']['basename']
            )
        );

        \imagecopyresampled(
            $this->target,
            $source,
            $this->source->getArea()['p1']->x,
            $this->source->getArea()['p1']->y,
            0,
            0,
            $sectionWidth,
            $sectionHeight,
            $closest['details']['size']['width'],
            $closest['details']['size']['height']
        );

        \imagedestroy($source);
    }

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
