<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FertilizerRepository")
 * @ORM\Table(name="fertilizers", uniqueConstraints={@ORM\UniqueConstraint(name="unique_name_company_npk_target", columns={"name", "company", "n", "p", "k", "target"})},
 *     indexes={
 *     @Index(name="index_name", columns={"name"}),
 *     @Index(name="index_company", columns={"company"}),
 *     @Index(name="index_n", columns={"n"}),
 *     @Index(name="index_p", columns={"p"}),
 *     @Index(name="index_k", columns={"k"}),
 *     @Index(name="fulltext_name", columns={"name"}, flags={"fulltext"}),
 *     @Index(name="fulltext_name_company_dose_urls", columns={"name", "company", "dose", "url1", "url2", "url3"}, flags={"fulltext"}),
 *     })
 */
class Fertilizer
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"unsigned"=true})
     */
    private $id;

    /**
     * @ORM\Column(name="name", type="string", length=150)
     */
    private $name = '';

    /**
     * @ORM\Column(name="n", type="float", options={"default"="0"})
     */
    private $N = 0;

    /**
     * @ORM\Column(name="p", type="float", options={"default"="0"})
     */
    private $P = 0;

    /**
     * @ORM\Column(name="k", type="float", options={"default"="0"})
     */
    private $K = 0;

    /**
     * @ORM\Column(name="company", type="string", length=150, options={"default"=""})
     */
    private $company = '';

    /**
     * @ORM\Column(name="dose", type="string", length=255, options={"default"=""})
     */
    private $dose = '';

    /**
     * @ORM\Column(name="content", type="float", options={"default"="0"})
     */
    private $content = 0;

    /**
     * @ORM\Column(type="string", length=255, options={"default"=""})
     */
    private $url1 = '';

    /**
     * @ORM\Column(type="string", length=255, options={"default"=""})
     */
    private $url2 = '';

    /**
     * @ORM\Column(type="string", length=255, options={"default"=""})
     */
    private $url3 = '';

    /**
     * @ORM\Column(name="price", type="float", options={"default"="0"})
     */
    private $price = 0;

    /**
     * @ORM\Column(name="target", type="string", length=50, options={"default"="UNI"})
     */
    private $target = 'UNI';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getN(): float
    {
        return $this->N;
    }

    public function setN(float $N): self
    {
        $this->N = $N;

        return $this;
    }

    public function getP(): float
    {
        return $this->P;
    }

    public function setP(float $P): self
    {
        $this->P = $P;

        return $this;
    }

    public function getK(): float
    {
        return $this->K;
    }

    public function setK(float $K): self
    {
        $this->K = $K;

        return $this;
    }

    public function getCompany(): string
    {
        return $this->company;
    }

    public function setCompany(string $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getDose(): ?string
    {
        return $this->dose;
    }

    public function setDose(string $dose): self
    {
        $this->dose = $dose;

        return $this;
    }

    public function getNPK(): string
    {
        $delimiter = ':';
        return '' . $this->getN() . $delimiter . $this->getP() . $delimiter . $this->getK();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getId(). '-'. $this->getName() . '-' . $this->getCompany() . '-' . $this->getNPK() . '';
    }

    public function getContent(): float
    {
        return $this->content;
    }

    public function setContent(float $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getUrl1(): string
    {
        return $this->url1;
    }

    public function setUrl1(string $url1): self
    {
        $this->url1 = $url1;

        return $this;
    }

    public function getUrl2(): string
    {
        return $this->url2;
    }

    public function setUrl2(string $url2): self
    {
        $this->url2 = $url2;

        return $this;
    }

    public function getUrl3(): string
    {
        return $this->url3;
    }

    public function setUrl3(string $url3): self
    {
        $this->url3 = $url3;

        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getTarget(): string
    {
        return $this->target;
    }

    public function setTarget(string $target): self
    {
        $this->target = strtoupper(trim($target));

        return $this;
    }

}
