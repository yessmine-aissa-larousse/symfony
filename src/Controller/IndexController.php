<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use App\Entity\PriceSearch; 
use App\Form\PriceSearchType;
use App\Entity\CategorySearch;
use App\Form\CategorySearchType;
use App\Entity\PropertySearch;
use App\Form\PropertySearchType;
use App\Entity\Category;
use App\Form\CategoryType;
use App\Form\ArticleType;
use App\Entity\Article;
class IndexController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

   /**
 *@Route("/",name="article_list")
 */
 public function home(Request $request,EntityManagerInterface $entityManager)
 {
    $propertySearch = new PropertySearch();
    $form = $this->createForm(PropertySearchType::class,$propertySearch);
    $form->handleRequest($request);

    $articles= [];

    if($form->isSubmitted() && $form->isValid()) {
    $nom = $propertySearch->getnom();
    if ($nom!="")
    $articles= $entityManager->getRepository(Article::class)->findBy(['nom' => $nom] );
    else
    $articles= $entityManager->getRepository(Article::class)->findAll();
   
    }
    return $this->render('articles/index.html.twig',[ 'form' =>$form->createView(), 'articles' => $articles]);
 }

    
    /**
     * @Route("/article/save")
     */
    public function save(EntityManagerInterface $entityManager)
    {
        $article = new Article();
        $article->setnom('chocolat morgene');
        $article->setPrix(27000);

        $entityManager->persist($article);
        $entityManager->flush();

        return new Response('Article enregistré avec ID ' . $article->getId());
    }

   /**
 * @Route("/article/new", name="new_article")
 * Method({"GET", "POST"})
 */
 public function new(Request $request , EntityManagerInterface $entityManager) {
    $article = new Article();
    $form = $this->createForm(ArticleType::class,$article);
    $form->handleRequest($request);
    if($form->isSubmitted() && $form->isValid()) {
    $article = $form->getData();
    $entityManager->persist($article);
    $entityManager->flush();
    return $this->redirectToRoute('article_list');
    }
    return $this->render('articles/new.html.twig',['form' => $form->createView()]);
    }
    /**
     * @Route("/article/{id}", name="article_show")
     */
    public function show($id, EntityManagerInterface $entityManager) {
        // Utilisation de l'EntityManager pour récupérer l'article par ID
        $article = $entityManager->getRepository(Article::class)->find($id);
        
        return $this->render('articles/show.html.twig', [
            'article' => $article
        ]);
    }

        /**
     * @Route("/article/edit/{id}", name="edit_article")
     * Method({"GET", "POST"})
     */
    public function edit(Request $request, $id , EntityManagerInterface $entityManager) 
    {
        $article = new Article();
        $article = $entityManager->getRepository(Article::class)->find($id);
    
        $form = $this->createForm(ArticleType::class,$article);
    
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
    
        $entityManager->flush();
    
        return $this->redirectToRoute('article_list');
        }
    
        return $this->render('articles/edit.html.twig', ['form' =>
        $form->createView()]);
        }
        /**
     * @Route("/article/delete/{id}", name="delete_article")
     * @Method({"DELETE"})
     */
    public function delete(Request $request, $id ,EntityManagerInterface $entityManager) {
        $article = $entityManager->getRepository(Article::class)->find($id);
       
        
        $entityManager->remove($article);
        $entityManager->flush();
       
        $response = new Response();
        $response->send();
        return $this->redirectToRoute('article_list');
        }

    /**
 * @Route("/category/newCat", name="new_category")
 * Method({"GET", "POST"})
 */
 public function newCategory(Request $request,EntityManagerInterface $entityManager) {
    $category = new Category();
    $form = $this->createForm(CategoryType::class,$category);
    $form->handleRequest($request);
    if($form->isSubmitted() && $form->isValid()) {
    $article = $form->getData();
    
    $entityManager->persist($category);
    $entityManager->flush();
    }
   return $this->render('articles/newCategory.html.twig',['form'=>
   $form->createView()]);
    }

    /**
 * @Route("/art_cat/", name="article_par_cat")
 * Method({"GET", "POST"})
 */
 public function articlesParCategorie(Request $request,EntityManagerInterface $entityManager) {
    $categorySearch = new CategorySearch();
    $form = $this->createForm(CategorySearchType::class,$categorySearch);
    $form->handleRequest($request);
    $articles= [];
    if($form->isSubmitted() && $form->isValid()) {
        $category = $categorySearch->getCategory();
       
        if ($category!="")
       $articles= $category->getArticles();
        else
        $articles= $entityManager->getRepository(Article::class)->findAll();
        }
       
        return $this->render('articles/articlesParCategorie.html.twig',['form' => $form->createView(),'articles' => $articles]);
        }

        /**
     * @Route("/art_prix/", name="article_par_prix")
     * Method({"GET", "POST"})
     */
    public function articlesParPrix(Request $request,EntityManagerInterface $entityManager)
    {

    $priceSearch = new PriceSearch();
    $form = $this->createForm(PriceSearchType::class,$priceSearch);
    $form->handleRequest($request);
    $articles= [];
    if($form->isSubmitted() && $form->isValid()) {
    $minPrice = $priceSearch->getMinPrice();
    $maxPrice = $priceSearch->getMaxPrice();

    $articles= $entityManager->getRepository(Article::class)->findByPriceRange($minPrice,$maxPrice);
    }
    return $this->render('articles/articlesParPrix.html.twig',[ 'form' =>$form->createView(), 'articles' => $articles]);
    }

    
    

   
    
        

}