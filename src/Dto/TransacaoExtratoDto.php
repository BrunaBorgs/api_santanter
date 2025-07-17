<?php

namespace App\Dto;

use DateTime;

class TransacaoExtratoDto {
    private ?int $id;
    private ?string $valor;
    private ?DateTime $dataHora;
    private ?string $tipo;
    private ?ContaDto $origem;
    private ?ContaDto $destino;
    

    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */ 
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of valor
     */ 
    public function getValor()
    {
        return $this->valor;
    }

    /**
     * Set the value of valor
     *
     * @return  self
     */ 
    public function setValor($valor)
    {
        $this->valor = $valor;

        return $this;
    }

    /**
     * Get the value of dataHora
     */ 
    public function getDataHora()
    {
        return $this->dataHora;
    }

    /**
     * Set the value of dataHora
     *
     * @return  self
     */ 
    public function setDataHora($dataHora)
    {
        $this->dataHora = $dataHora;

        return $this;
    }

    /**
     * Get the value of tipo
     */ 
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set the value of tipo
     *
     * @return  self
     */ 
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get the value of origem
     */ 
    public function getOrigem()
    {
        return $this->origem;
    }

    /**
     * Set the value of origem
     *
     * @return  self
     */ 
    public function setOrigem($origem)
    {
        $this->origem = $origem;

        return $this;
    }

    /**
     * Get the value of destino
     */ 
    public function getDestino()
    {
        return $this->destino;
    }

    /**
     * Set the value of destino
     *
     * @return  self
     */ 
    public function setDestino($destino)
    {
        $this->destino = $destino;

        return $this;
    }
}

class ContaDto {
    private ?int $id;
    private ?string $cpf;
    private ?string $nome;
    private ?string $numeroConta;

    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */ 
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of cpf
     */ 
    public function getCpf()
    {
        return $this->cpf;
    }

    /**
     * Set the value of cpf
     *
     * @return  self
     */ 
    public function setCpf($cpf)
    {
        $this->cpf = $cpf;

        return $this;
    }

    /**
     * Get the value of nome
     */ 
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * Set the value of nome
     *
     * @return  self
     */ 
    public function setNome($nome)
    {
        $this->nome = $nome;

        return $this;
    }

    /**
     * Get the value of numeroConta
     */ 
    public function getNumeroConta()
    {
        return $this->numeroConta;
    }

    /**
     * Set the value of numeroConta
     *
     * @return  self
     */ 
    public function setNumeroConta($numeroConta)
    {
        $this->numeroConta = $numeroConta;

        return $this;
    }
}