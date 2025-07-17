<?php

namespace App\Controller;

use App\Dto\ContaDto;
use App\Dto\TransacaoExtratoDto;
use App\Dto\TransacaoRealizarDto;
use App\Entity\Transacao;
use App\Repository\ContaRepository;
use App\Repository\TransacaoRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
final class TransacoesController extends AbstractController
{
    #[Route('/transacoes', name: 'transacoes_realizar', methods: ['POST'])]
    public function realizar(
        #[MapRequestPayload(acceptFormat: 'json')]
        TransacaoRealizarDto $entrada,

        ContaRepository $contaRepository,
        EntityManagerInterface $entityManager
    ): Response | JsonResponse {
        $erros = [];
        // Validar o DTO de entrada
        if (!$entrada->getIdUsuarioOrigem()) {
            array_push($erros, [
                'message' => 'Conta de origem é obrigatória!'
            ]);
        }
        if (!$entrada->getIdUsuarioDestino()) {
            array_push($erros, [
                'message' => 'Conta de destino é obrigatória!'
            ]);
        }
        if (!$entrada->getValor()) {
            array_push($erros, [
                'message' => 'Valor é obrigatório!'
            ]);
        }
        if ((float) $entrada->getValor() <= 0) {
            array_push($erros, [
                'message' => 'Valor deve ser maior que zero!'
            ]);
        }
        if ($entrada->getIdUsuarioOrigem() === $entrada->getIdUsuarioDestino()) {
            array_push($erros, [
                'message' => 'As contas devem ser distintas!'
            ]);
        }

        if (count($erros) > 0) {
            return $this->json($erros, 422);
        }

        // Validações de regra de negócio

        // validar se as contas existem
        $contaOrigem = $contaRepository->findByUsuarioId($entrada->getIdUsuarioOrigem());
        if (!$contaOrigem) {
            return $this->json([
                'message' => 'Conta de origem não encontrada!'
            ], 404);
        }

        $contaDestino = $contaRepository->findByUsuarioId($entrada->getIdUsuarioDestino());
        if (!$contaDestino) {
            return $this->json([
                'message' => 'Conta de destino não encontrada!'
            ], 404);
        }

        // validar se a origem tem saldo suficiente
        if ((float) $contaOrigem->getSaldo() < (float) $entrada->getValor()) {
            return $this->json([
                'message' => 'Saldo insuficiente!'
            ]);
        }

        // realizar a transação e salvar no banco
        $saldo = (float) $contaOrigem->getSaldo();
        $valorT = (float) $entrada->getValor();
        $saldoDestino = (float) $contaDestino->getSaldo();

        $contaOrigem->setSaldo($saldo - $valorT);
        $entityManager->persist($contaOrigem);

        $contaDestino->setSaldo($valorT + $saldoDestino);
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

    #[Route('/transacoes/{idUsuario}/extrato', name: 'transacoes_extrato', methods: ['GET'])]
    public function gerarExtrato(
        int $idUsuario,
        ContaRepository $contaRepository,
        TransacaoRepository $transacaoRepository
    ): JsonResponse {
        $conta = $contaRepository->findByUsuarioId($idUsuario);
        if (!$conta) {
            return $this->json([
                'message' => 'Usuário não encontrado!'
            ], 404);
        }

        $transacoes = $transacaoRepository->findByContaOrigemOrContaDestino($conta->getId());

        $saida = [];
        foreach ($transacoes as $transacao) {
            $transacaoDto = new TransacaoExtratoDto();

            $transacaoDto->setId($transacao->getId());
            $transacaoDto->setValor($transacao->getValor());
            $transacaoDto->setDataHora($transacao->getDataHora());

            if ($conta->getId() === $transacao->getContaOrigem()->getId()) {
                $transacaoDto->setTipo('ENVIOU');
            } else if ($conta->getId() === $transacao->getContaDestino()->getId()) {
                $transacaoDto->setTipo('RECEBEU');
            }

            // origem
            $origem = $transacao->getContaOrigem();
            $contaOrigemDto = new ContaDto();
            $contaOrigemDto->setId($origem->getUsuario()->getId());
            $contaOrigemDto->setNome($origem->getUsuario()->getNome());
            $contaOrigemDto->setCpf($origem->getUsuario()->getCpf());
            $contaOrigemDto->setNumeroConta($origem->getNumero());

            $transacaoDto->setOrigem($contaOrigemDto);

            // destino
            $destino = $transacao->getContaDestino();
            $contaDestinoDto = new ContaDto();
            $contaDestinoDto->setId($destino->getUsuario()->getId());
            $contaDestinoDto->setNome($destino->getUsuario()->getNome());
            $contaDestinoDto->setCpf($destino->getUsuario()->getCpf());
            $contaDestinoDto->setNumeroConta($destino->getNumero());

            $transacaoDto->setDestino($contaDestinoDto);

            array_push($saida, $transacaoDto);
        }

        return $this->json($saida);
    }


}
