<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use http\Header;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CategoryController
 * @package App\Controller
 * @Route("/category", name="category_")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route ("/add", name="add")
     */
    public function add(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();

        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findAll();
        $form = $this->createForm(CategoryType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $category = new Category();
            $category->setName($form->getData()->GetName());

            $em->persist($category);
            $em->flush();

            return $this->redirect('/category/add');
        }

        return $this->render('wild/addCategory.html.twig', [
            'website' => 'Wild Séries',
            'form' => $form->createView(),
            'categories' => $categories
        ]);
    }
}
