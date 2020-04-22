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
     * Log container
     *
     * @var array<string>
     */
    private $log;

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
    public function __construct(string $filename, bool $enableLog = false)
    {
        $this->log = [
            'isEnabled' => $enableLog,
            'contents'  => [],
        ];

        $this->source = new Histogram();
        $this->source->loadFromFile($filename);

        $this->target = imagecreatetruecolor(
            $this->source->getDetails()['size']->width,
            $this->source->getDetails()['size']->height
        );
        imagealphablending($this->target, false);
    }

    //-----------------------------------------------------------------------------------------------------------------

    /**
     * @access public
     *
     * @param  string $filename
     * @param  int $cols
     * @param  int $rows
     *
     * @return void
     */
    public function create(string $filename, int $cols, int $rows) : void
    {
        $partialWidth  = floor($this->source->getDetails()['size']->width / $cols);
        $partialHeight = floor($this->source->getDetails()['size']->height / $rows);

        if ($this->log['isEnabled']) {
            $this->log['contents'][] = 'P1(X, Y) - P2(X, Y) - COLOR';
            $this->log['contents'][] = '----------------------------------------';
        }

        foreach (range(1, $rows) as $row) {
            foreach (range(1, $cols) as $col) {
                $p1x = (int) (($col - 1) * $partialWidth);
                $p1y = (int) (($row - 1) * $partialHeight);
                $p2x = (int) ($col * $partialWidth);
                $p2y = (int) ($row * $partialHeight);

                $this->source->setArea($p1x, $p1y, $p2x, $p2y);

                $hexColor = $this->source->getAverageColor();

                if ($this->log['isEnabled']) {
                    $this->log['contents'][] = sprintf('%04d, %04d - %04d, %04d - %s',
                        $p1x,
                        $p1y,
                        $p2x,
                        $p2y,
                        $hexColor
                    );
                }

                $rgbaColor = ColorHex::toRgba($hexColor);

                imagefilledrectangle(
                    $this->target,
                    $p1x,
                    $p1y,
                    $p2x,
                    $p2y,
                    imagecolorallocate(
                        $this->target,
                        (int) $rgbaColor['r'],
                        (int) $rgbaColor['g'],
                        (int) $rgbaColor['b']
                    )
                );
            }
        }

        imagepng($this->target, $filename);

        if ($this->log['isEnabled']) {
            file_put_contents('mosaic.log', implode(PHP_EOL, $this->log['contents']));
        }
    }

    //-----------------------------------------------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------------------------------------
}
