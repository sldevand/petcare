<?php

namespace App\Modules\Image\Service;

use Exception;

/**
 * Class ImageManager
 * @package App\Modules\Image\Service
 */
class ImageManager
{
    /** @var \Framework\Service\FileManager */
    protected $fileManager;

    /** @var array */
    protected $settings;

    /**
     * ImageManager constructor.
     * @param \Framework\Service\FileManager $fileManager
     * @param array $settings
     */
    public function __construct(
        \Framework\Service\FileManager $fileManager,
        array $settings
    ) {
        $this->fileManager = $fileManager;
        $this->settings = $settings;
    }

    /**
     * @param string $path
     * @return string
     */
    public function getImageFromPath(string $path): string
    {
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }

    /**
     * @param string $img
     * @param string $file
     * @return string
     * @throws Exception
     */
    public function generateImage(string $img, string $file): string
    {
        $imageDir = $this->fileManager->getDirName($file);
        $this->fileManager->makeDirectory($imageDir, true);

        $imageParts = explode(";base64,", $img);
        $imageTypeAux = explode("image/", $imageParts[0]);
        $extension = $imageTypeAux[1];

        if ($extension === 'jpeg') {
            $extension = 'jpg';
        }

        $base64Image = base64_decode($imageParts[1]);

        $fullPath = $file . '.' . $extension;

        return $this->fileManager->save($fullPath, $base64Image);
    }

    public function getImagesDirectory()
    {
        return $this->settings['settings']['assets']['images'];
    }
}
