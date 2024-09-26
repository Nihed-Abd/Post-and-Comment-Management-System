<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="comment", indexes={@ORM\Index(name="idPost", columns={"idPost"})})
 */
class Comment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $content;

    /**
     * @ORM\ManyToOne(targetEntity="Post")
     * @ORM\JoinColumn(name="idPost", referencedColumnName="id")
     */
    private ?Post $idPost;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getIdPost(): ?Post
    {
        return $this->idPost;
    }

    public function setIdPost(?Post $idPost): static
    {
        $this->idPost = $idPost;

        return $this;
    }
}
