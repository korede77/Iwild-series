<?php
//src/Controller/WildController

namespace App\Controller;



use App\Entity\Category;
use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use App\Form\ProgramSearchType;
use App\Repository\CategoryRepository;
use App\Repository\SeasonRepository;
use Doctrine\ORM\Query\AST\OrderByItem;
use Doctrine\ORM\Query\Expr\OrderBy;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
        $form = $this->createForm(ProgramSearchType::class,
            null,
            ['method' => Request::METHOD_GET]
        );

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
            'form' => $form->createView(),
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
            ->findOneBy(['title' => $slug]);
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
    public  function  showByCategory(string  $categoryName):Response
    {
        if (!$categoryName){
            throw $this->createNotFoundException('No slug has been sent to find a program in program\'s table.');
        }
        $categoryName = preg_replace(
            '/-/',
            ' ',ucwords(trim(strip_tags($categoryName)),"-")
        );
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findOneBy(['name' => $categoryName]);
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
    /**
     * @param string $slug
     * @Route("/program/{slug}", name="program")
     * @return  Response
     */
    public function showByProgram(string $slug): Response
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
            ->findBy(['program'=> $program]);

        if (!$program) {
            throw $this->createNotFoundException(
                'No program with '.$slug.' title, found in program\'s table.'
            );
        }
        return $this->render('wild/program.html.twig', [
            'program' => $program,
            'seasons' => $seasons,
            'slug'  => $slug,
        ]);
    }
    /**
     * @param integer $id
     * @Route("/season/{id}", name="season")
     * @return  Response
     */
    public function showBySeason(int $id): Response
    {
        $season = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findOneBy(['id' => $id]);
        $program = $season->getProgram();
        $episodes = $season->getEpisodes();
        return $this->render('wild/season.html.twig', [
                'season' => $season,
                'program' => $program,
                'episodes' => $episodes,
                'id' => $id,
        ]);
    }
    /**
     * @param integer $id
     * @Route("/episode/{id}", name="episode")
     * @return Response
     */
    public function showEpisode( Episode $episode):Response
    {
        $season = $episode->getSeason();
        $program = $season->getProgram();
        return $this->render('wild/episode.html.twig', [
            'program'=> $program,
            'season' => $season,
            'episode' => $episode,
        ]);
    }


}
