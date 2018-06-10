<?php

namespace App\Controller;

use App\Entity\Ingredient;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * @Route("/api/ingredient")
 */
class IngredientController extends Controller
{        
    /**
     * @Route("/", name="ingredient_new", methods="POST")
     * @SWG\Parameter(
     *     name="ingredient",
     *     in="body",
     *     @Model(type=Ingredient::class, groups={"request"})
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Successful response",
     *     @Model(type=Ingredient::class)
     * )
     */
    public function new(Request $request, SerializerInterface $serializer): Response
    {
        $ingredient = $serializer->deserialize($request->getContent(), Ingredient::class, 'json');
        $this->getDoctrine()->getEntityManager()->persist($ingredient);
        $this->getDoctrine()->getEntityManager()->merge($ingredient);
        $this->getDoctrine()->getEntityManager()->flush();
        return new JsonResponse($ingredient);
        
    }

    /**
     * @Route("/{id}", name="ingredient_show", methods="GET")
     * @SWG\Response(
     *     response=200,
     *     description="Successful response",
     *     @Model(type=Ingredient::class)
     * )
     */
    public function show(Ingredient $ingredient): Response
    {
        return new JsonResponse($ingredient);
    }

    /**
     * @Route("/{id}", name="ingredient_edit", methods="PATCH|PUT")
     * @SWG\Parameter(
     *     name="ingredient",
     *     in="body",
     *     @Model(type=Ingredient::class, groups={"request"})
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Successful response",
     *     @Model(type=Ingredient::class)
     * )
     */
    public function edit(Request $request, Ingredient $ingredient, SerializerInterface $serializer): Response
    {
        $ingredient = $serializer->deserialize($request->getContent(), Ingredient::class, 'json', ['object_to_populate' => $ingredient]);
        $this->getDoctrine()->getEntityManager()->merge($ingredient);
        $this->getDoctrine()->getEntityManager()->flush();
        return new JsonResponse($ingredient);
    }

    /**
     * @Route("/{id}", name="ingredient_delete", methods="DELETE")
     */
    public function delete(Request $request, Ingredient $unit): Response
    {
        $this->getDoctrine()->getEntityManager()->remove($unit);
        $this->getDoctrine()->getEntityManager()->flush();
        return new JsonResponse(null);
    }
}
