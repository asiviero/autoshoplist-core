<?php

namespace App\Controller;

use App\Entity\ConversionRule;
use App\Form\ConversionRuleType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Model;

/**
 * @Route("/api/conversionRule")
 */
class ConversionRuleController extends Controller
{
    /**
     * @Route("/", name="conversion_rule_new", methods="POST")
     * @SWG\Parameter(
     *     name="conversionRule",
     *     in="body",
     *     @Model(type=ConversionRule::class, groups={"request"})
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Successful response",
     *     @Model(type=ConversionRule::class)
     * )
     */
    public function new(Request $request, SerializerInterface $serializer): Response
    {
        $conversionRule = $serializer->deserialize($request->getContent(), ConversionRule::class, 'json');
        $this->getDoctrine()->getEntityManager()->persist($conversionRule);
        $this->getDoctrine()->getEntityManager()->merge($conversionRule);
        $this->getDoctrine()->getEntityManager()->flush();
        return new JsonResponse($conversionRule);
    }

    /**
     * @Route("/{id}", name="conversion_rule_show", methods="GET")
     * @SWG\Response(
     *     response=200,
     *     description="Successful response",
     *     @Model(type=ConversionRule::class)
     * )
     */
    public function show(ConversionRule $conversionRule): Response
    {
        return new JsonResponse($conversionRule);
    }

    /**
     * @Route("/{id}", name="conversion_rule_edit", methods="PATCH|PUT")
     * @SWG\Parameter(
     *     name="conversionRule",
     *     in="body",
     *     @Model(type=ConversionRule::class, groups={"request"})
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Successful response",
     *     @Model(type=ConversionRule::class)
     * )
     */
    public function edit(Request $request, ConversionRule $conversionRule, SerializerInterface $serializer): Response
    {
        $conversionRule = $serializer->deserialize($request->getContent(), ConversionRule::class, 'json', ['object_to_populate' => $conversionRule]);
        $this->getDoctrine()->getEntityManager()->merge($conversionRule);
        $this->getDoctrine()->getEntityManager()->flush();
        return new JsonResponse($conversionRule);
    }

    /**
     * @Route("/{id}", name="conversion_rule_delete", methods="DELETE")
     */
    public function delete(ConversionRule $conversionRule): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($conversionRule);
        $em->flush();
        return new JsonResponse(null);
    }
}
