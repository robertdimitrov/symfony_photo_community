<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PhotoLikeRepository")
 */
class PhotoLike
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="photoLikes")
     */
    private $user_id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Photo", inversedBy="photoLikes")
     */
    private $photo_id;

    public function getId()
    {
        return $this->id;
    }

    public function getUserId(): ?User
    {
        return $this->user_id;
    }

    public function setUserId(?User $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getPhotoId(): ?Photo
    {
        return $this->photo_id;
    }

    public function setPhotoId(?Photo $photo_id): self
    {
        $this->photo_id = $photo_id;

        return $this;
    }
}
