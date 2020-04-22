# PHP7 + GD Image Mosaic Generator

This package allows to create an image mosaic or a photomosaic: a picture that has been divided into tiled sections, each of which is filled with the average color of the section.

## Example(s)

### Image 200 x 200

![Source Image](./tests/test-image-200x200.jpg)

#### Mosaic #1: 2 rows, 2 cols

![Mosaic #1](./tests/control/test-image-200x200-2x2.jpg)

#### Mosaic #2: 20 rows, 20 cols

![Mosaic #2](./tests/control/test-image-200x200-20x20.jpg)

#### Mosaic #3: 100 rows, 100 cols

![Mosaic #3](./tests/control/test-image-200x200-100x100.jpg)

### Image 800 x 600

![Source Image](./tests/test-image-800x600.jpg)

#### Mosaic #1: 2 rows, 2 cols

![Mosaic #1](./tests/control/test-image-800x600-2x2.jpg)

#### Mosaic #2: 20 rows, 20 cols

![Mosaic #2](./tests/control/test-image-800x600-20x20.jpg)

#### Mosaic #3: 100 rows, 100 cols

![Mosaic #3](./tests/control/test-image-800x600-100x100.jpg)

## Installation

You can install the package via composer:

``` bash
$ composer require alcidesrc/mosaic dev-master
```

### Dependencies

This package requires `histogram` to obtain the image (and image area) histogram:

```bash
$ composer require alcidesrc/histogram dev-master
```

## Usage

```php
$mosaic = new Mosaic(__DIR__ .'/source.jpg');
$mosaic->create(__DIR__ .'/target.jpg', 20, 30);		// Creates an image with 20x30 sections
```

### Debug

```php
$mosaic = new Mosaic(__DIR__ .'/source.jpg', true);     // Enable log to file
$mosaic->create(__DIR__ .'/target.png', 20, 30);        // Creates a PNG image with 20x30 sections
```

At the end of the process you will get:

- **target.png** : The image result.
- **mosaic.log** : The log file where you can check each section coordinates and applied color.

## Changelog

Please visit [CHANGELOG](CHANGELOG.md) for further information related with latest changes.

## Testing with PHPUnit

``` bash
$ composer test
```

## Check with PHPStan

```bash
$ composer check
```

## Credits

- [Alcides Ramos](https://github.com/alcidesrc)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

