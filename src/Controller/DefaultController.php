<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Model;
use App\Entity\Unit;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends AbstractController
{
  /**
   * @Route("/api/", methods={"GET"})
   * 
   * @SWG\Response(
   *     response=200,
   *     description="Successful response",
   *     @Model(type=Unit::class)
   * )
   */
  public function index()
  {
    return new Response('Hello World');
  }
}
