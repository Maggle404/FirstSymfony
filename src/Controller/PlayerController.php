<?php

namespace App\Controller;

use App\Entity\Players;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PlayerController extends AbstractController
{
    public function create(EntityManagerInterface $entityManager, Request $request)
    {

        $name = $request->request->get("name");
        $atk = $request->request->get("atk");
        $mag = $request->request->get("mag");
        $hp = $request->request->get("hp");
        $mana = $request->request->get("mana");

        $player = new Players();
        $player
            ->setName($name)
            ->setAtk($atk)
            ->setMag($mag)
            ->setHp($hp)
            ->setMana($mana);

        $entityManager->persist($player);
        $entityManager->flush();

        return $this->render('player/index.html.twig', ["player" => $player]);
    }


    #[Route('/player/show/{id}', name: 'app_player_show')]
    public function show(Players $player)
    {
        return $this->render("index.html.twig", ['player' => $player]);
    }

    #[Route('/players/all', name: 'app_player_all')]
    public function showAll(EntityManagerInterface $entityManager)
    {
        $players = $entityManager->getRepository(Players::class)->findAll();
        return $this->render('players/index.html.twig', ["players" => $players]);
    }

    #[Route('/player/delete/{id}', name: "app_player_delete")]
    public function delete(EntityManagerInterface $entityManager, Players $player)
    {
        $entityManager->remove($player);
        $entityManager->flush();
        return $this->redirectToRoute('app_player_all');
    }

    #[Route('/player/form', name: "app_player_form")]
    public function form(Request $request,EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        $player = new Players();
        $form = $this->createFormBuilder($player)
            ->add('name')
            ->add('atk')
            ->add('mag')
            ->add('hp')
            ->add('mana')
            ->add('save', SubmitType::class, ['label' => 'Create Player'])

            ->getForm();

        $form->handleRequest($request);



        if ($form->isSubmitted() && $form->isValid()) {
            $player=$form->getData();
            $entityManager->persist($player);
            $entityManager->flush();

            $players = $entityManager->getRepository(Players::class)->findAll();
            return $this->render('player/index.html.twig', ["player" => $player, "players" => $players]);

        }
        $players = $entityManager->getRepository(Players::class)->findAll();
        return $this->render('player/form.html.twig', [
            'form' => $form->createView(),
            "player" => $player,
            "players" => $players
        ]);
    }
}