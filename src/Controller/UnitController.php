<?php

namespace App\Controller;

use App\Entity\Unit;
use App\Form\UnitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Model;

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
    public function new(Request $request): Response
    {
        // @todo implement        
        $unit = new Unit($request->request->get('name'), $request->request->get('symbol'));
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
    public function edit(Request $request, Unit $unit): Response
    {
        if($name = $request->request->get('name')) {
            $unit->setName($name);
        }
        if($symbol = $request->request->get('symbol')) {
            $unit->setSymbol($symbol);
        }
        $this->getDoctrine()->getEntityManager()->persist($unit);
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
