<?php

namespace App\Controller;

use App\Entity\Unit;
use App\Form\UnitType;
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
 * @Route("/api/unit")
 */
class UnitController extends Controller
{
    /**
     * @Route("/", name="unit_index", methods="GET")
     */
    public function index(): Response
    {
        $units = $this->getDoctrine()
            ->getRepository(Unit::class)
            ->findAll();

        return new JsonResponse($units);
    }

    /**
     * @Route("/", name="unit_new", methods="POST")
     * @SWG\Response(
     *     response=200,
     *     description="Successful response",
     *     @Model(type=Unit::class)
     * )
     */
    public function new(Request $request, SerializerInterface $serializer): Response
    {
        $unit = $serializer->deserialize($request->getContent(), Unit::class, 'json');
        $this->getDoctrine()->getEntityManager()->persist($unit);
        $this->getDoctrine()->getEntityManager()->flush();
        return new JsonResponse($unit);
        
    }

    /**
     * @Route("/{id}", name="unit_show", methods="GET")
     * @SWG\Response(
     *     response=200,
     *     description="Successful response",
     *     @Model(type=Unit::class)
     * )
     */
    public function show(Unit $unit): Response
    {
        return new JsonResponse($unit);
    }

    /**
     * @Route("/{id}", name="unit_edit", methods="PATCH|PUT")
     */
    public function edit(Request $request, Unit $unit, SerializerInterface $serializer): Response
    {
        $unit = $serializer->deserialize($request->getContent(), Unit::class, 'json', ['object_to_populate' => $unit]);
        $this->getDoctrine()->getEntityManager()->flush();
        return new JsonResponse($unit);
    }

    /**
     * @Route("/{id}", name="unit_delete", methods="DELETE")
     */
    public function delete(Request $request, Unit $unit): Response
    {
        $this->getDoctrine()->getEntityManager()->remove($unit);
        $this->getDoctrine()->getEntityManager()->flush();
        return new JsonResponse(null);
    }
}
