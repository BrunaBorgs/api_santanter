<?php

namespace App\Controller;


use App\Dto\TransacaoRealizarDto;
use App\Entity\Conta;
use App\Entity\Transacao;
use App\Repository\ContaRepository;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
final class TransacoesController extends AbstractController
{

    #[Route('/transacoes', name: 'transacoes_realizar', methods:['POST'])]
    public function realizar(
        #[MapRequestPayload(acceptFormat: 'json')]
        TransacaoRealizarDto $entrada,
        ContaRepository $contaRepository,
        EntityManagerInterface $entityManager
    ): Response
    {
        $erros = [];
        if(!$entrada->getIdUsuarioOrigem()) {
            array_push($erros, ['message' => 'Conta de origem é obrigatória!']);
        }
        if(!$entrada->getIdUsuarioDestino()) {
            array_push($erros, ['message' => 'Conta de destino é obrigatória!']);
        }
        if(!$entrada->getValor()) {
            array_push($erros, ['message' => 'Valor é obrigatório!']);
        }
        if ((float) $entrada->getValor() <= 0) {
            array_push($erros, ['message' => 'Valor deve ser maior que zero!']);
        }
        if ($entrada->getIdUsuarioOrigem() === $entrada->getIdUsuarioDestino()) {
            array_push($erros, ['message' => 'As contas devem ser distintas!']);
        }

        if(count($erros) > 0) {
            return $this->json($erros, 422);
        }
        

        $contaOrigem = $contaRepository->findByUsuarioId($entrada->getIdUsuarioOrigem());
        if (!$contaOrigem) {
            return $this->json ([
                'message' => 'Conta de origem não encontrada'
            ], 404);
        }

        $contaDestino = $contaRepository->findByUsuarioId($entrada->getIdUsuarioDestino());
        if (!$contaDestino) {
            return $this->json([
                'message' => 'Conta de destino não entcontrada'
            ], 404);
        }

        if ((float) $entrada->getValor() > (float) $contaOrigem->getSaldo()) {
            return $this->json([ 
                'message' => 'Saldo insuficiente'
            ], 422);
        }
        $saldoDestino = (float) $contaDestino->getSaldo();
        $saldo0rigem = (float) $contaOrigem->getSaldo();
        $valorTransferencia = (float) $entrada->getValor();
        $saldo0rigem = $saldo0rigem - $valorTransferencia;
        $saldoDestino = $saldoDestino + $valorTransferencia;

        $contaOrigem->setSaldo($saldo0rigem);
        $contaDestino->setSaldo($saldoDestino);

        $entityManager->persist($contaOrigem);
        $entityManager->persist($contaDestino);

        $transacao = new Transacao();
        $transacao->setDataHora(new DateTime());
        $transacao->setValor($entrada->getValor());
        $transacao->setContaOrigem($contaOrigem);
        $transacao->setContaDestino($contaDestino);
        
        $entityManager->persist($transacao);

        $entityManager->flush();


        return new Response(status: 204);
    }
}
