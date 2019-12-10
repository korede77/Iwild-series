<?php


namespace App\Controller;


use App\Entity\Category;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/category", name="category_")
 */

class CategoryController extends AbstractController
{
    /**
     *
     * @Route("/", name="")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return  Response
     */
    public function add (Request $request, EntityManagerInterface $entityManager):Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();
            return $this->redirectToRoute('wild_index');
        }
        return $this->render('category/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
