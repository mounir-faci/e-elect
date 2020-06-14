<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    public const DEFAULT_AVATAR_FILE = 'avatar-default.png';
    public const AVATAR_DIRECTORY_PARAM_NAME = 'avatars_dir';

    /**
     * @var ParameterBagInterface $parameterBag
     */
    private $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    private function upload(UploadedFile $file, string $fileNamePrefix, string $saveDirectory)
    {
        $fileName = sprintf('%s-%s.%s', $fileNamePrefix, uniqid(), $file->guessExtension());
        try {
            $file->move($saveDirectory, $fileName);
        } catch (FileException $e) {
            throw $e;
        }

        return $fileName;
    }

    public function getAvatar(string $fileName): string
    {
        return sprintf('%s/%s',
            $this->parameterBag->get(self::AVATAR_DIRECTORY_PARAM_NAME),
            $fileName
        );
    }

    public function uploadAvatar(UploadedFile $file)
    {
       return  $this->upload($file, 'avatar', $this->parameterBag->get(self::AVATAR_DIRECTORY_PARAM_NAME));
    }
}