<?php

namespace App\Controller;

use App\Entity\Program;
use App\Entity\Season;
use App\Form\SeasonType;
use App\Repository\SeasonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("program/{program}/season")
 */
class SeasonController extends AbstractController
{
    /**
     * @Route("/", name="season_index", methods={"GET"})
     */
    public function index(SeasonRepository $seasonRepository): Response
    {
        return $this->render('season/index.html.twig', [
            'seasons' => $seasonRepository->findAll(),
        ]);
    }

    /**
     * @param $program
     * @Route("/all", name="season_show_by_program", methods={"GET"})
     */
    public function showByProgram(SeasonRepository $seasonRepository, $program): Response
    {
        $program = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($program)), "-")
        );

        $currentProgram = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['title' => mb_strtolower($program)]);
        $seasons = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findBy(['program' => $currentProgram->getId()], ['number' => 'ASC']);

        return $this->render('season/index.html.twig', [
            'program' => $currentProgram,
            'seasons' => $seasons,
        ]);
    }

    /**
     * @Route("/new", name="season_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $season = new Season();
        $form = $this->createForm(SeasonType::class, $season);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($season);
            $entityManager->flush();

            return $this->redirectToRoute('season_index');
        }

        return $this->render('season/new.html.twig', [
            'season' => $season,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return Response
     * @Route("/{number}", name="season_show", methods={"GET"})
     */
    public function show(int $number, string $program): Response
    {
        $currentProgram = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['slug' => $program]);
        $season = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findOneBy(['number' => $number, 'program' => $currentProgram]);

        return $this->render('season/show.html.twig', [
            'season' => $season,
            'program' => $currentProgram,
        ]);
    }

    /**
     * @Route("/{number}/edit", name="season_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Season $season
     * @return Response
     */
    public function edit(Request $request, Season $season): Response
    {
        $form = $this->createForm(SeasonType::class, $season);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('season_index');
        }

        return $this->render('season/edit.html.twig', [
            'season' => $season,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{number}", name="season_delete", methods={"DELETE"})
     * @param Request $request
     * @param Season $season
     * @return Response
     */
    public function delete(Request $request, Season $season): Response
    {
        if ($this->isCsrfTokenValid('delete'.$season->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($season);
            $entityManager->flush();
        }

        return $this->redirectToRoute('season_index');
    }
}
