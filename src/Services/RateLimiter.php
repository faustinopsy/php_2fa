<?php

namespace App\Services;

class Ratelimiter
{
    private $ip;
    private $limite;
    private $tempoEspera;
    private $diretorio;

    public function __construct($ip, $limite = 5, $tempoEspera = 86400, $diretorio = 'requisicoes.json'){
        $this->ip = $ip;
        $this->limite = $limite;
        $this->tempoEspera = $tempoEspera;
        $this->diretorio = $diretorio;
    }

    public function permitido(){
        if (!file_exists($this->diretorio)) {
            file_put_contents($this->diretorio, json_encode([]));
        }

        $requisicoes = json_decode(file_get_contents($this->diretorio), true);
        if (!isset($requisicoes[$this->ip])) {
            $requisicoes[$this->ip] = [time()];
        } else {
            $requisicoes[$this->ip] = array_filter($requisicoes[$this->ip], function ($timestamp) {
                return $timestamp >= (time() - $this->tempoEspera);
            });

            // excedeu o limitee
            if (count($requisicoes[$this->ip]) >= $this->limite) {
                file_put_contents($this->diretorio, json_encode($requisicoes));
                return false;
            }
            // ainda nao excedeu
            $requisicoes[$this->ip][] = time();
        }
        
        file_put_contents($this->diretorio, json_encode($requisicoes));
        return true;
    }
}
