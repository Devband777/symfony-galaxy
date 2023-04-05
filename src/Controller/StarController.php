<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Galaxy;
use App\Entity\Star;
use App\Entity\Atom;

class StarController extends AbstractController
{
    /**
     * @Route("/stars", methods={"POST"})
     */
    public function addStar(Request $request, EntityManagerInterface $entityManager): Response
    {
      // Check if the request has the required authentication header
      $token = $request->headers->get('Authentication');
      if ($token !== 'token TheMainAnswer') {
        return $this->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
      }

      // Get the data from the request body
      $data = json_decode($request->getContent(), true);

      // Create a new star entity
      $star = new Star();
      $star->setName($data['name']);
      $star->setTemperature($data['temperature']);
      $star->setRadius($data['radius']);

      // Get the current user
      $user = $this->getUser();

      // Set the user as the owner of the star
      $star->setUser($user);

      // Add the star to the database
      $entityManager->persist($star);
      $entityManager->flush();

      // Return a success response
      return $this->json(['success' => true]);
    }


    /**
     * @Route("/stars", methods={"GET"})
     */
    public function getUniqueStars(Request $request, EntityManagerInterface $entityManager): Response
    {
      // Get the query parameters from the request
      $fromGalaxy = $request->query->get('from_galaxy');
      $negativeGalaxy = $request->query->get('negative_galaxy');
      $sortBy = $request->query->get('sort_by', 'radius');

      // Get the Galaxy and Atom entities based on the input parameters
      $galaxy = $entityManager->getRepository(Galaxy::class)->findOneBy(['name' => $fromGalaxy]);

      $negativeAtoms = $entityManager->getRepository(Atom::class)
        ->createQueryBuilder('a')
        ->where('a.galaxy = :galaxy')
        ->setParameter('galaxy', $negativeGalaxy)
        ->getQuery()
        ->getResult();

      // Build the query to select the unique stars
      $queryBuilder = $entityManager->createQueryBuilder()
        ->select('s.name, s.temperature, ((4/3)*3.14*(s.radius^3)) as volume')
        ->from(Star::class, 's')
        ->join('s.galaxy', 'g')
        ->where('g.name = :galaxy')
        ->setParameter('galaxy', $fromGalaxy);

      foreach ($negativeAtoms as $atom) {
        $queryBuilder->andWhere(':atom NOT MEMBER OF s.atoms')
          ->setParameter('atom', $atom);
      }

      if ($sortBy === 'size') {
        $queryBuilder->orderBy('volume', 'DESC');
      } else {
        $queryBuilder->orderBy('s.temperature', 'DESC');
      }

      // Execute the query and return the results as JSON
      $result = $queryBuilder->getQuery()->getResult();
      return $this->json(['stars' => $result]);
    }
}