<?php

declare(strict_types=1);

namespace App\Service;

use RuntimeException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ImageService
{
    public function __construct(
        private ParameterBagInterface $parameterBag
    ) {
    }

    public function getRandomImageName(): string
    {
        $gifFound = false;

        $imgDir = $this->parameterBag->get('kernel.project_dir') . '/public/img';
        $images = scandir($imgDir);

        array_shift($images);
        array_shift($images);

        if (count($images) === 0) {
            throw new RuntimeException('No images found in the image folder. You first need to upload some images.');
        }

        while ($gifFound === false) {
            $randomKey = array_rand($images);
            $image = $images[$randomKey];

            if (str_ends_with($image, '.gif')) {
                $gifFound = true;
            }
        }

        return $image;
    }
}
