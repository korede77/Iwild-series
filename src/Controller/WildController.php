<?php
//src/Controller/WildController

namespace App\Controller;



use App\Entity\Program;
use App\Repository\CategoryRepository;
use Doctrine\ORM\Query\AST\OrderByItem;
use Doctrine\ORM\Query\Expr\OrderBy;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/wild", name="wild_")
 */
class WildController extends AbstractController
{
    /**
     * Show all rows from Program's entity
     *
     * @Route("/", name="index")
     * @return  Response
     */
    public function index() :Response
    {
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();
        if (!$programs){
            throw $this->createNotFoundException(
                'No Program found in program\'s table.'
            );
        }
        return $this->render('wild/index.html.twig', [
            'programs' => $programs,
        ]);
    }
    /**
     * Getting a program with a formatted slug for title
     *
     * @param string $slug The slugger
     * @Route("/show/{slug<^[a-z0-9-]+$>}",
     *      defaults={"slug" = null},
     *     name="show")
     * @return Response
     */
    public function show(?string $slug):Response
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
                'No program with '.$slug.' title, found in program\'s table.'
            );
        }
        return $this->render('wild/show.html.twig', [
            'program' => $program,
            'slug'  => $slug,
        ]);
    }
    /**
     * @param string $categoryName
     * @Route("/category/{categoryName}", name="category")
     * @return  Response
     */
    public  function  showByCategory(string  $categoryName, CategoryRepository $categoryRepository):Response
    {
        if (!$categoryName){
            throw $this->createNotFoundException('No slug has been sent to find a program in program\'s table.');
        }
        $categoryName = preg_replace(
            '/-/',
            ' ',ucwords(trim(strip_tags($categoryName)),"-")
        );
        $category = $categoryRepository->findOneBy(['name' => $categoryName]);
        $programs = $this->getDoctrine()
                        ->getRepository(Program::class)
                        ->findBy(['category'=>$category],['id'=>'DESC'], 3,0);
        if (!$programs){
            throw $this->createNotFoundException(
                'No program with '.$categoryName.' category, found in program\'s table.'
            );
        }
        return $this->render('wild/category.html.twig', [
            'programs' => $programs,
            'categoryName'  => $categoryName,
            ]);
    }
}