<?php

namespace App\Filter;
class UsuarioContaFilter {
    private ?string $pesquisa;

    /**
     * Get the value of pesquisa
     */ 
    public function getPesquisa()
    {
        return $this->pesquisa;
    }

    /**
     * Set the value of pesquisa
     *
     * @return  self
     */ 
    public function setPesquisa($pesquisa)
    {
        $this->pesquisa = $pesquisa;

        return $this;
    }
}