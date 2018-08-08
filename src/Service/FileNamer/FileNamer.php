<?php

namespace App\Service\FileNamer;

use \Vich\UploaderBundle\Naming\NamerInterface;

class FileNamer implements NamerInterface
{
    /**
     * Creates a name for the file being uploaded.
     *
     * @param object $obj The object the upload is attached to.
     * @param string $field The name of the uploadable field to generate a name for.
     * @return string The file name.
     */
    function name($object, \Vich\UploaderBundle\Mapping\PropertyMapping $mapping): string
    {
        $file = $object->imageFile;
        $extension = $file->guessExtension();

        return uniqid('img_', true).'.'.$extension;
    }
}