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
     * @param bool $thumbnail
     * @return string
     * @throws Exception
     */
    public function generateImage(string $img, string $file, bool $thumbnail = false): string
    {
        $imageDir = $this->fileManager->getDirName($file);
        $this->fileManager->makeDirectory($imageDir, true);

        $imageParts = explode(";base64,", $img);
        $imageTypeAux = explode("image/", $imageParts[0]);
        $extension = $imageTypeAux[1];

        if ($extension === 'jpeg') {
            $extension = 'jpg';
        }

        $width = 600;
        $height = 600;
        $suffix = '-image';
        if ($thumbnail) {
            $width = 200;
            $height = 200;
            $suffix = '-thumbnail';
        }

        $rawImage = base64_decode($imageParts[1]);
        $fullPath = $file . $suffix . '.' . $extension;
        $generatedImagePath = $this->fileManager->save($fullPath, $rawImage);

        $this->resizeImage($generatedImagePath, $fullPath, $width, $height);

        return $generatedImagePath;
    }

    /**
     * @param string $src
     * @param string $dst
     * @param int $width
     * @param int $height
     * @param int $crop
     * @return bool|string
     * @throws Exception
     */
    public function resizeImage(string $src, string $dst, int $width, int $height, int $crop = 0)
    {
        if (!list($w, $h) = getimagesize($src)) {
            throw new \Exception("Cant get picture size!");
        }

        $type = strtolower(substr(strrchr($src, "."), 1));
        if ($type == 'jpeg') {
            $type = 'jpg';
        }
        switch ($type) {
            case 'bmp':
                $img = imagecreatefromwbmp($src);
                break;
            case 'gif':
                $img = imagecreatefromgif($src);
                break;
            case 'jpg':
                $img = imagecreatefromjpeg($src);
                break;
            case 'png':
                $img = imagecreatefrompng($src);
                break;
            default:
                throw new \Exception("Unsupported picture type!");
        }

        $ratio = min($width / $w, $height / $h);
        if ($w < $width and $h < $height) {
            $ratio = 1;
        }

        $width = $w * $ratio;
        $height = $h * $ratio;
        $x = 0;

        $new = imagecreatetruecolor($width, $height);

        // preserve transparency
        if ($type == "gif" or $type == "png") {
            imagecolortransparent($new, imagecolorallocatealpha($new, 0, 0, 0, 127));
            imagealphablending($new, false);
            imagesavealpha($new, true);
        }

        imagecopyresampled($new, $img, 0, 0, $x, 0, $width, $height, $w, $h);

        switch ($type) {
            case 'bmp':
                imagewbmp($new, $dst);
                break;
            case 'gif':
                imagegif($new, $dst);
                break;
            case 'jpg':
                imagejpeg($new, $dst);
                break;
            case 'png':
                imagepng($new, $dst);
                break;
        }

        return $dst;
    }

    public function getImagesDirectory()
    {
        return $this->settings['settings']['assets']['images'];
    }
}
