<?php
/**
 * Date: 15/05/19
 * Time: 14:25
 */

namespace App\Model;


interface EventInterface
{
    /**
     * @return string
     */
    public function getName(): string ;
    public function getId(): ?int;
    public function getValue();


}