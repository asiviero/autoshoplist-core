<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Entity\RecipeList;
use App\Form\RecipeListType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/api/recipeList")
 */
class RecipeListController extends Controller
{
    /**
     * @Route("/", name="recipe_list_new", methods="POST")
     */
    public function new(Request $request, SerializerInterface $serializer): Response
    {
        $em = $this->getDoctrine()->getManager();
        $content = json_decode($request->getContent(), true);
        $serializedRecipes = array_map(function($item) use ($serializer, $em) {
            $recipe = $em->find(Recipe::class, $item['id']);
            return $recipe;
        }, $content['recipes']);
        $recipeList = new RecipeList($serializedRecipes);
        $em->persist($recipeList);
        $em->flush();
        return JsonResponse::fromJsonString($serializer->serialize($recipeList, 'json'));
    }

    /**
     * @Route("/{id}", name="recipe_list_show", methods="GET")
     */
    public function show(RecipeList $recipeList, SerializerInterface $serializer): Response
    {
        return JsonResponse::fromJsonString($serializer->serialize($recipeList, 'json'));
    }

    /**
     * @Route("/{id}", name="recipe_list_edit", methods="PATCH|PUT")
     */
    public function edit(Request $request, RecipeList $recipeList, SerializerInterface $serializer): Response
    {
        $em = $this->getDoctrine()->getManager();
        $content = json_decode($request->getContent(), true);
        $serializedRecipes = array_map(function($item) use ($serializer, $em) {
            $recipe = $em->find(Recipe::class, $item['id']);
            return $recipe;
        }, $content['recipes']);
        $recipeList->setRecipes($serializedRecipes);
        $em->persist($recipeList);
        $em->flush();
        return JsonResponse::fromJsonString($serializer->serialize($recipeList, 'json'));
    }

    /**
     * @Route("/{id}", name="recipe_list_delete", methods="DELETE")
     */
    public function delete(Request $request, RecipeList $recipeList): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($recipeList);
        $em->flush();
        return new JsonResponse();
    }
}
