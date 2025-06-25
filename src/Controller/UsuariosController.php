<?php

namespace App\Controller;

use App\Dto\UsuarioDto;
use App\Entity\Usuario;
use App\Repository\UsuarioRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
final class UsuariosController extends AbstractController
{
    #[Route('/usuarios', name: 'usuarios_criar', methods:['POST'])]
    public function criar(
        #[MapRequestPayload(acceptFormat: 'json')]
        UsuarioDto $usuarioDto,

        EntityManagerInterface $entityManager,
        UsuarioRepository $usuarioRepository
    ): JsonResponse
    {
        $erros = [];
        if (!$usuarioDto->getSenha()) {
            array_push($erros, ['message' => 'Senha é obrigatória!']);
        }
        if (!$usuarioDto->getCpf()) {
            array_push($erros, ['message' => 'CPF é orbigatório!']);
        }
        if (!$usuarioDto->getNome()) {
            array_push($erros, ['message' => 'Nome é orbigatório!']);
        }
        if (!$usuarioDto->getEmail()) {
            array_push($erros, ['message' => 'Email é orbigatório!']);
        }
        if (!$usuarioDto->getTelefone()) {
            array_push($erros, ['message' => 'Telefone é orbigatório!']);
        }
        if (count($erros) > 0) {
            return $this->json($erros, 422);
        }

        $usuarioExistente = $usuarioRepository->findByCpf($usuarioDto->getCpf());
        if ($usuarioExistente) {
            return $this->json([
                'message' => 'O cpf informado já está cadastrado!'
            ], 409);
        }

        $usuario = new Usuario();
        $usuario->setCpf($usuarioDto->getCpf());
        $usuario->setNome($usuarioDto->getNome());
        $usuario->setEmail($usuarioDto->getEmail());
        $usuario->setTelefone($usuarioDto->getTelefone());
        $usuario->setSenha($usuarioDto->getSenha());

        $entityManager->persist($usuario);
        $entityManager->flush();

        return $this->json([$usuario]);
    }
}
