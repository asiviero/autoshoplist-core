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
   */
  public function index()
  {
    return new Response('Hello World');
  }
}
