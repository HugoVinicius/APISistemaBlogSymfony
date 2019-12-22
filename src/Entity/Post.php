<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
 final class Post {
    
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue()
     */
    private ?int $id = null;
     
     /**
      * @ORM\Column()
      * @Assert\NotBlank()
      * @Assert\Length(min="3", max="50") 
      */
    public ?string $title = null;

    /**
     * @ORM\Column()
     * @Assert\NotBlank()
     * @Assert\Length(min="3") 
     */
    public ?string $description = null;

    /**
     * @ORM\Column(type="datetime")
     */

     private \DateTime $createAt;

     public function getId()
     {
         return $this->id;
     }

     public function getCreatedAt()
     {
         return $this->createAt;
     }


     public function __construct(string $title = null, string $description = null){
         $this->title = $title;
         $this->description = $description;
         $this->createAt = new \DateTime();
     }
 }