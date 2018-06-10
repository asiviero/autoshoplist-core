<?php

namespace App\Controller;

use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Model;
use App\Entity\Recipe;
use App\Entity\Quantity;
use App\Form\RecipeType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/api/recipe")
 */
class RecipeController extends Controller
{
    /**
     * @Route("/", name="recipe_new", methods="POST")
     * @SWG\Parameter(
     *     name="recipe",
     *     in="body",
     *     @Model(type=Recipe::class, groups={"request"})
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Successful response",
     *     @Model(type=Recipe::class)
     * )
     */
    public function new(Request $request, SerializerInterface $serializer): Response
    {
        $content = json_decode($request->getContent(), true);
        $serializedQties = array_map(function($item) use ($serializer) {
            $qty = $serializer->deserialize(json_encode($item), Quantity::class, 'json');
            $this->getDoctrine()->getEntityManager()->persist($qty);
            $this->getDoctrine()->getEntityManager()->merge($qty);
            return $qty;
        }, $content['quantities']);
        $content['quantities'] = [];
        $recipe = $serializer->deserialize(json_encode($content), Recipe::class, 'json');
        $recipe->replaceQuantities($serializedQties);
        $this->getDoctrine()->getEntityManager()->persist($recipe);
        $this->getDoctrine()->getEntityManager()->merge($recipe);
        $this->getDoctrine()->getEntityManager()->flush();
        return JsonResponse::fromJsonString($serializer->serialize($recipe, 'json'));
    }

    /**
     * @Route("/{id}", name="recipe_show", methods="GET")
     */
    public function show(Recipe $recipe, SerializerInterface $serializer): Response
    {        
        return JsonResponse::fromJsonString($serializer->serialize($recipe, 'json'));
    }

    /**
     * @Route("/{id}", name="recipe_edit", methods="PATCH|PUT")
     */
    public function edit(Request $request, Recipe $recipe, SerializerInterface $serializer): Response
    {        
        $content = json_decode($request->getContent(), true);
        $serializedQties = array_map(function($item) use ($serializer) {
            $qty = $serializer->deserialize(json_encode($item), Quantity::class, 'json');
            $this->getDoctrine()->getEntityManager()->persist($qty);
            $this->getDoctrine()->getEntityManager()->merge($qty);
            return $qty;
        }, $content['quantities']);
        $content['quantities'] = [];
        $recipe = $serializer->deserialize(json_encode($content), Recipe::class, 'json', ['object_to_populate' => $recipe]);
        $recipe->replaceQuantities($serializedQties);
        $this->getDoctrine()->getEntityManager()->persist($recipe);
        $this->getDoctrine()->getEntityManager()->merge($recipe);
        $this->getDoctrine()->getEntityManager()->flush();
        return JsonResponse::fromJsonString($serializer->serialize($recipe, 'json'));
    }

    /**
     * @Route("/{id}", name="recipe_delete", methods="DELETE")
     */
    public function delete(Request $request, Recipe $recipe): Response
    {
        $this->getDoctrine()->getEntityManager()->remove($recipe);
        $this->getDoctrine()->getEntityManager()->flush();
        return new JsonResponse(null);
    }
}
