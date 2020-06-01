<?php
// src/Controller/WildController.php
namespace App\Controller;

use App\Entity\Category;
use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use App\Form\CategoryType;
use App\Form\ProgramSearchType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/wild", name="wild_")
 */
class WildController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(Request $request): Response
    {
        $category = new Category();

        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();

        if (!$programs) {
            throw $this->createNotFoundException('No program found in program\'s table.');
        }

        //$form = $this->createForm(CategoryType::class, $category);
        $form = $this->createForm(ProgramSearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();
            $programs = $this->getDoctrine()
                ->getRepository(Program::class)
                ->findBy(['title' => $data['searchField']]);
        }

        return $this->render('wild/index.html.twig', [
            'website' => 'Wild Séries',
            'programs' => $programs,
            'form' => $form->createView()
        ]);
    }

    /**
     * Getting a program with a formatted slug for title
     *
     * @param string $slug The slugger
     * @Route("/show/{slug<^[a-z0-9-]+$>}", defaults={"slug" = null}, name="show")
     * @return Response
     */
    public function show(?string $slug): Response
    {
        if (!$slug) {
            throw $this
                ->createNotFoundException('No slug has been sent to find a program in program\'s table.');
        }
        $slug = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($slug)), "-")
        );
        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['title' => mb_strtolower($slug)]);
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with ' . $slug . ' title, found in program\'s table.'
            );
        }

        return $this->render('wild/show.html.twig', [
            'program' => $program,
            'slug' => $slug,
        ]);
    }

    /**
     * @param string $categoryName The category
     * @Route("/category/{categoryName<^[a-z-]+$>}", name="show_category")
     * @return Response
     */
    public function showByCategory(?string $categoryName): Response
    {
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findOneBy(['name' => $categoryName]);

        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findBy(['category' => ($category->getId())], ['id' => 'DESC'], 3);

        if (!$categoryName) {
            throw $this
                ->createNotFoundException('No category has been sent to find programs in program\'s table.');
        }

        return $this->render('wild/category.html.twig', [
            'programs' => $programs,
            'category' => $category->getName()
        ]);
    }

    /**
     * Getting a program with a formatted slug for title
     *
     * @param string $slug The slugger
     * @Route("/show_by_program/{slug<^[a-z0-9-]+$>}", defaults={"slug" = null}, name="show_by_program")
     * @return Response
     */
    public function showByProgram($slug): Response
    {
        if (!$slug) {
            throw $this
                ->createNotFoundException('No slug has been sent to find a program in program\'s table.');
        }
        $slug = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($slug)), "-")
        );
        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['title' => mb_strtolower($slug)]);
        $seasons = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findBy(['program' => $program->getId()], ['number' => 'ASC']);

        if (!$program) {
            throw $this->createNotFoundException(
                'No program with ' . $slug . ' title, found in program\'s table.'
            );
        }

        return $this->render('wild/showByProgram.html.twig', [
            'seasons' => $seasons,
            'program' => $program,
            'slug' => $slug,
        ]);
    }

    /**
     * @param int $id
     * @Route("/show_by_season/{id<[0-9]+>}", defaults={"id" = null}, name="show_by_season")
     * @return Response
     */
    public function showBySeason(int $id) {
        if (!$id) {
            throw $this
                ->createNotFoundException('No season id has been sent to find a season in season\'s table.');
        }
        $season = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findOneBy(['id' => $id]);
        $program = $season->getProgram();
        $episodes = $season->getEpisodes();

        if (!$episodes) {
            throw $this->createNotFoundException(
                'No episode with ' . $id . ' as season id, found in episode\'s table.'
            );
        }

        return $this->render('wild/showBySeason.html.twig', [
            'season' => $season,
            'program' => $program,
            'episodes' => $episodes
        ]);
    }

    /**
     * @param Episode $episode
     * @Route("/show_episode/{id}", name="show_episode")
     */
    public function showEpisode(Episode $episode)
    {
        $season = $episode->getSeason();
        $program = $season->getProgram();

        return $this->render('wild/showEpisode.html.twig', [
            'program' => $program,
            'season' => $season,
            'episode' => $episode
        ]);
    }
}