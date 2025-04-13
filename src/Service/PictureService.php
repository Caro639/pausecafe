<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PictureService
{
    private $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function upload(
        UploadedFile $picture,
        ?string $folder = '',
        ?int $width = 250,
        ?int $height = 250
    ): string {
        $fichier = md5(uniqid(rand(), true)) . '.webp';

        $picture_infos = getimagesize($picture);

        if ($picture_infos === false) {
            throw new \Exception('Le fichier n\'est pas une image valide.');
        }

        switch ($picture_infos['mime']) {
            case 'image/jpeg':
                $picture_source = imagecreatefromjpeg($picture);
                break;
            case 'image/png':
                $picture_source = imagecreatefrompng($picture);
                break;
            case 'image/webp':
                $picture_source = imagecreatefromwebp($picture);
                break;
            default:
                throw new \Exception('Le fichier n\'est pas une image valide.');
        }

        $imageWidth = $picture_infos[0];
        $imageHeight = $picture_infos[1];

        switch ($imageWidth <=> $imageHeight) {
            case -1: //portrait
                $squareSize = $imageWidth;
                $src_x = 0;
                $src_y = ($imageHeight - $squareSize) / 2;
                break;
            case 0: //carre
                $squareSize = $imageWidth;
                $src_x = 0;
                $src_y = 0;
                break;
            case 1: //paysage
                $squareSize = $imageHeight;
                $src_x = ($imageWidth - $squareSize) / 2;
                $src_y = 0;
                break;
        }

        $resized_picture = imagecreatetruecolor($width, $height);

        imagecopyresampled(
            $resized_picture,
            $picture_source,
            0,
            0,
            $src_x,
            $src_y,
            $width,
            $height,
            $squareSize,
            $squareSize
        );
        $path = $this->params->get('images_directory') . $folder;

        if (!file_exists($path . '/mini/')) {
            mkdir($path . '/mini/', 0755, true);
        }

        imagewebp($resized_picture, $path . '/mini/' . $width . 'x' . $height . '-' . $fichier);

        $picture->move($path . '/', $fichier);

        return $fichier;
    }

    public function delete(
        string $fichier,
        ?string $folder = '',
        ?int $width = 250,
        ?int $height = 250
    ): bool {
        if ($fichier !== 'default.webp') {
            $success = false;
            $path = $this->params->get('images_directory') . $folder;

            $mini = $path . '/mini/' . $width . 'x' . $height . '-' . $fichier;

            if (file_exists($mini)) {
                unlink($mini);
                $success = true;
            }

            $original = $path . '/' . $fichier;
            if (file_exists($original)) {
                unlink($original);
                $success = true;
            }
            return $success;
        }
        return false;
    }

}