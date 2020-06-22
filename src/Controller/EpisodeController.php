<?php

namespace App\Controller;

use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use App\Form\EpisodeType;
use App\Repository\EpisodeRepository;
use App\Repository\ProgramRepository;
use App\Services\Slugify;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("program/{program}/season/{number}/episode")
 */
class EpisodeController extends AbstractController
{
    /**
     * @Route("/", name="episode_index", methods={"GET"})
     */
    public function index(EpisodeRepository $episodeRepository): Response
    {
        return $this->render('episode/index.html.twig', [
            'episodes' => $episodeRepository->findAll(),
        ]);
    }

    /**
     * @param EpisodeRepository $episodeRepository
     * @Route("/all", name="episode_show_by_season", methods={"GET"})
     * @return Response
     */
    public function showBySeason(EpisodeRepository $episodeRepository, $number, $program): Response
    {
        $currentProgram = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['slug' => $program]);
        $currentSeason = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findOneBy(['number' => $number, 'program' => $currentProgram]);
        $episodes = $episodeRepository->findBy(['season'=> $currentSeason]);

        return $this->render('episode/index.html.twig', [
            'episodes' => $episodes,
            'season' => $currentSeason,
            'program' => $currentProgram,
        ]);
    }

    /**
     * @Route("/new", name="episode_new", methods={"GET","POST"})
     */
    public function new(Request $request, Slugify $slugify): Response
    {
        $episode = new Episode();
        $form = $this->createForm(EpisodeType::class, $episode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $episode->setSlug($slugify->generate($episode->getTitle()));
            $entityManager->persist($episode);
            $entityManager->flush();

            return $this->redirectToRoute('episode_index');
        }

        return $this->render('episode/new.html.twig', [
            'episode' => $episode,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}", name="episode_show", methods={"GET"})
     */
    public function show($slug): Response
    {
        $episode = $this->getDoctrine()
            ->getRepository(Episode::class)
            ->findOneBy(['slug' => $slug]);
        return $this->render('episode/show.html.twig', [
            'episode' => $episode,
        ]);
    }

    /**
     * @Route("/{slug}/edit", name="episode_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, $slug, Slugify $slugify): Response
    {
        $episode = $this->getDoctrine()
            ->getRepository(Episode::class)
            ->findOneBy(['slug' => $slug]);

        $form = $this->createForm(EpisodeType::class, $episode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $episode->setSlug($slugify->generate($episode->getTitle()));
            $this->getDoctrine()->getManager()->persist($episode);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('episode_index');
        }

        return $this->render('episode/edit.html.twig', [
            'episode' => $episode,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="episode_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Episode $episode): Response
    {
        if ($this->isCsrfTokenValid('delete'.$episode->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($episode);
            $entityManager->flush();
        }

        return $this->redirectToRoute('episode_index');
    }
}
