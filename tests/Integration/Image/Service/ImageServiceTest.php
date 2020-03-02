<?php

namespace Tests\Integration\Image\Service;

use App\Modules\Image\Service\ImageManager;
use Framework\Service\FileManager;
use PHPUnit\Framework\TestCase;

/**
 * Class ImageServiceTest
 * @package Tests\Integration\Image\Service
 */
class ImageServiceTest extends TestCase
{
    public function testGenerateImage()
    {
        $fileManager = new FileManager();
        $imageManager = new ImageManager($fileManager, []);

        $inputDir = __DIR__ . '/../input';
        $inputPath = $inputDir . '/cat.jpeg';
        $encodedImage = $imageManager->getImageFromPath($inputPath);

        $outputDir = __DIR__ . '/../output';
        $outputFile = $outputDir . '/cat1-generated';
        $outputPath = $imageManager->generateImage($encodedImage, $outputFile);

        $this->assertEquals($outputFile . '-image.jpg', $outputPath);

        $outputPath = $imageManager->generateImage($encodedImage, $outputFile, true);

        $this->assertEquals($outputFile . '-thumbnail.jpg', $outputPath);
    }
}
