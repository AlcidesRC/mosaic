<?php

namespace AlcidesRC\Mosaic\Test;

use AlcidesRC\Mosaic\Mosaic;

class MosaicTest extends \PHPUnit\Framework\TestCase
{
    /** @test */
    public function it_must_generate_image_200x200_with_2cols_2rows()
    {
        $source  = __DIR__ . '/test-image-200x200.jpg';
        $target  = __DIR__ . '/test-image-200x200-2x2.jpg';
        $control = __DIR__ . '/control/test-image-200x200-2x2.jpg';

        $mosaic = new Mosaic($source);
        $mosaic->create($target, 2, 2);

        // Instance
        $this->assertInstanceOf(Mosaic::class, $mosaic);
        // Result
        $this->assertTrue(file_exists($target));
        $this->assertEquals(
            file_get_contents($control),
            file_get_contents($target)
        );

        unlink($target);
    }

    /** @test */
    public function it_must_generate_image_200x200_with_20cols_20rows()
    {
        $source  = __DIR__ . '/test-image-200x200.jpg';
        $target  = __DIR__ . '/test-image-200x200-20x20.jpg';
        $control = __DIR__ . '/control/test-image-200x200-20x20.jpg';

        $mosaic = new Mosaic($source);
        $mosaic->create($target, 20, 20);

        // Instance
        $this->assertInstanceOf(Mosaic::class, $mosaic);
        // Result
        $this->assertTrue(file_exists($target));
        $this->assertEquals(
            file_get_contents($control),
            file_get_contents($target)
        );

        unlink($target);
    }

    /** @test */
    public function it_must_generate_image_200x200_with_100cols_100rows()
    {
        $source  = __DIR__ . '/test-image-200x200.jpg';
        $target  = __DIR__ . '/test-image-200x200-100x100.jpg';
        $control = __DIR__ . '/control/test-image-200x200-100x100.jpg';

        $mosaic = new Mosaic($source);
        $mosaic->create($target, 100, 100);

        // Instance
        $this->assertInstanceOf(Mosaic::class, $mosaic);
        // Result
        $this->assertTrue(file_exists($target));
        $this->assertEquals(
            file_get_contents($control),
            file_get_contents($target)
        );

        unlink($target);
    }

    /** @test */
    public function it_must_generate_image_800x600_with_2cols_2rows()
    {
        $source  = __DIR__ . '/test-image-800x600.jpg';
        $target  = __DIR__ . '/test-image-800x600-2x2.jpg';
        $control = __DIR__ . '/control/test-image-800x600-2x2.jpg';

        $mosaic = new Mosaic($source);
        $mosaic->create($target, 2, 2);

        // Instance
        $this->assertInstanceOf(Mosaic::class, $mosaic);
        // Result
        $this->assertTrue(file_exists($target));
        $this->assertEquals(
            file_get_contents($control),
            file_get_contents($target)
        );

        unlink($target);
    }

    /** @test */
    public function it_must_generate_image_800x600_with_20cols_20rows()
    {
        $source  = __DIR__ . '/test-image-800x600.jpg';
        $target  = __DIR__ . '/test-image-800x600-20x20.jpg';
        $control = __DIR__ . '/control/test-image-800x600-20x20.jpg';

        $mosaic = new Mosaic($source);
        $mosaic->create($target, 20, 20);

        // Instance
        $this->assertInstanceOf(Mosaic::class, $mosaic);
        // Result
        $this->assertTrue(file_exists($target));
        $this->assertEquals(
            file_get_contents($control),
            file_get_contents($target)
        );

        unlink($target);
    }

    /** @test */
    public function it_must_generate_image_800x600_with_100cols_100rows()
    {
        $source  = __DIR__ . '/test-image-800x600.jpg';
        $target  = __DIR__ . '/test-image-800x600-100x100.jpg';
        $control = __DIR__ . '/control/test-image-800x600-100x100.jpg';

        $mosaic = new Mosaic($source);
        $mosaic->create($target, 100, 100);

        // Instance
        $this->assertInstanceOf(Mosaic::class, $mosaic);
        // Result
        $this->assertTrue(file_exists($target));
        $this->assertEquals(
            file_get_contents($control),
            file_get_contents($target)
        );

        unlink($target);
    }
}
