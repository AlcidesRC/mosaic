<?php

namespace AlcidesRC\Mosaic\Test;

use AlcidesRC\Histogram\Histogram;
use AlcidesRC\Mosaic\Mosaic;

class MosaicImagesAverageColorsTest extends \PHPUnit\Framework\TestCase
{
    /** @test */
    public function it_must_generate_image_200x200_with_2cols_2rows()
    {
        $source  = __DIR__ . '/test-image-200x200.jpg';
        $control = __DIR__ . '/control/test-image-200x200-2x2-avg.png';

        $mosaic = new Mosaic($source);

        $mosaic->loadImages(
            __DIR__ .'/dataset/*.jpg',
            __DIR__ .'/dataset/cache',
            false
        );

        [$pngFilename, $htmlFilename] = $mosaic->createWithImagesByAverageColors(2, 2);

        // Instance
        $this->assertInstanceOf(Mosaic::class, $mosaic);
        // Result
        $this->assertIsString($pngFilename);
        $this->assertTrue(file_exists($pngFilename));
        $this->assertEquals(
            file_get_contents($control),
            file_get_contents($pngFilename)
        );
        $this->assertIsString($htmlFilename);
        $this->assertTrue(file_exists($htmlFilename));

        @unlink($pngFilename);
        @unlink($htmlFilename);
    }

    /** @test */
    public function it_must_generate_image_200x200_with_20cols_20rows()
    {
        $source  = __DIR__ . '/test-image-200x200.jpg';
        $control = __DIR__ . '/control/test-image-200x200-20x20-avg.png';

        $mosaic = new Mosaic($source);

        $mosaic->loadImages(
            __DIR__ .'/dataset/*.jpg',
            __DIR__ .'/dataset/cache',
            false
        );

        [$pngFilename, $htmlFilename] = $mosaic->createWithImagesByAverageColors(20, 20);

        // Instance
        $this->assertInstanceOf(Mosaic::class, $mosaic);
        // Result
        $this->assertIsString($pngFilename);
        $this->assertTrue(file_exists($pngFilename));
        $this->assertEquals(
            file_get_contents($control),
            file_get_contents($pngFilename)
        );
        $this->assertIsString($htmlFilename);
        $this->assertTrue(file_exists($htmlFilename));

        @unlink($pngFilename);
        @unlink($htmlFilename);
    }

    /** @test */
    public function it_must_generate_image_200x200_with_100cols_100rows()
    {
        $source  = __DIR__ . '/test-image-200x200.jpg';
        $control = __DIR__ . '/control/test-image-200x200-100x100-avg.png';

        $mosaic = new Mosaic($source);

        $mosaic->loadImages(
            __DIR__ .'/dataset/*.jpg',
            __DIR__ .'/dataset/cache',
            false
        );

        [$pngFilename, $htmlFilename] = $mosaic->createWithImagesByAverageColors(100, 100);

        // Instance
        $this->assertInstanceOf(Mosaic::class, $mosaic);
        // Result
        $this->assertIsString($pngFilename);
        $this->assertTrue(file_exists($pngFilename));
        $this->assertEquals(
            file_get_contents($control),
            file_get_contents($pngFilename)
        );
        $this->assertIsString($htmlFilename);
        $this->assertTrue(file_exists($htmlFilename));

        @unlink($pngFilename);
        @unlink($htmlFilename);
    }

    /** @test */
    public function it_must_generate_image_800x600_with_2cols_2rows()
    {
        $source  = __DIR__ . '/test-image-800x600.jpg';
        $control = __DIR__ . '/control/test-image-800x600-2x2-avg.png';

        $mosaic = new Mosaic($source);

        $mosaic->loadImages(
            __DIR__ .'/dataset/*.jpg',
            __DIR__ .'/dataset/cache',
            false
        );

        [$pngFilename, $htmlFilename] = $mosaic->createWithImagesByAverageColors(2, 2);

        // Instance
        $this->assertInstanceOf(Mosaic::class, $mosaic);
        // Result
        $this->assertIsString($pngFilename);
        $this->assertTrue(file_exists($pngFilename));
        $this->assertEquals(
            file_get_contents($control),
            file_get_contents($pngFilename)
        );
        $this->assertIsString($htmlFilename);
        $this->assertTrue(file_exists($htmlFilename));

        @unlink($pngFilename);
        @unlink($htmlFilename);
    }

    /** @test */
    public function it_must_generate_image_800x600_with_20cols_20rows()
    {
        $source  = __DIR__ . '/test-image-800x600.jpg';
        $control = __DIR__ . '/control/test-image-800x600-20x20-avg.png';

        $mosaic = new Mosaic($source);

        $mosaic->loadImages(
            __DIR__ .'/dataset/*.jpg',
            __DIR__ .'/dataset/cache',
            false
        );

        [$pngFilename, $htmlFilename] = $mosaic->createWithImagesByAverageColors(20, 20);

        // Instance
        $this->assertInstanceOf(Mosaic::class, $mosaic);
        // Result
        $this->assertIsString($pngFilename);
        $this->assertTrue(file_exists($pngFilename));
        $this->assertEquals(
            file_get_contents($control),
            file_get_contents($pngFilename)
        );
        $this->assertIsString($htmlFilename);
        $this->assertTrue(file_exists($htmlFilename));

        @unlink($pngFilename);
        @unlink($htmlFilename);
    }

    /** @test */
    public function it_must_generate_image_800x600_with_100cols_100rows()
    {
        $source  = __DIR__ . '/test-image-800x600.jpg';
        $control = __DIR__ . '/control/test-image-800x600-100x100-avg.png';

        $mosaic = new Mosaic($source);

        $mosaic->loadImages(
            __DIR__ .'/dataset/*.jpg',
            __DIR__ .'/dataset/cache',
            false
        );

        [$pngFilename, $htmlFilename] = $mosaic->createWithImagesByAverageColors(100, 100);

        // Instance
        $this->assertInstanceOf(Mosaic::class, $mosaic);
        // Result
        $this->assertIsString($pngFilename);
        $this->assertTrue(file_exists($pngFilename));
        $this->assertEquals(
            file_get_contents($control),
            file_get_contents($pngFilename)
        );
        $this->assertIsString($htmlFilename);
        $this->assertTrue(file_exists($htmlFilename));

        @unlink($pngFilename);
        @unlink($htmlFilename);
    }
}
