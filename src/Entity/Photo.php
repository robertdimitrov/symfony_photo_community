<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PhotoRepository")
 * @Vich\Uploadable
 */
class Photo
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    public $filename;

    /**
     * @Vich\UploadableField(mapping="photos", fileNameProperty="filename")
     * @Assert\File(mimeTypes={ "application/pdf" })
     * 
     * @var File
     */
    public $imageFile;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="photos")
     */
    private $user_id;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PhotoLike", mappedBy="photo_id")
     */
    private $photoLikes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="photo_id")
     */
    private $comments;

    public function __construct()
    {
        $this->photoLikes = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
     */
    public function setImageFile(?File $image = null): void
    {
        $this->imageFile = $image;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
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

    /**
     * @return Collection|PhotoLike[]
     */
    public function getPhotoLikes(): Collection
    {
        return $this->photoLikes;
    }

    public function addPhotoLike(PhotoLike $photoLike): self
    {
        if (!$this->photoLikes->contains($photoLike)) {
            $this->photoLikes[] = $photoLike;
            $photoLike->setPhotoId($this);
        }

        return $this;
    }

    public function removePhotoLike(PhotoLike $photoLike): self
    {
        if ($this->photoLikes->contains($photoLike)) {
            $this->photoLikes->removeElement($photoLike);
            // set the owning side to null (unless already changed)
            if ($photoLike->getPhotoId() === $this) {
                $photoLike->setPhotoId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setPhotoId($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getPhotoId() === $this) {
                $comment->setPhotoId(null);
            }
        }

        return $this;
    }
}
