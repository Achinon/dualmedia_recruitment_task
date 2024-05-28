<?php

namespace App\Dto;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class CartDto
{
    /** @var Collection */
    private Collection $entries;

    public function __construct()
    {
        $this->entries = new ArrayCollection();
    }

    public function getEntries(): Collection
    {
        return $this->entries;
    }

    public function addEntry(CartEntryDto $entry_dto){
        $this->entries->add($entry_dto);
    }
}