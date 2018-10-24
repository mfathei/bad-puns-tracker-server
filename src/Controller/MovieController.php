<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Repository\MovieRepository;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Doctrine\ORM\EntityManagerInterface;

class MovieController extends ApiController
{

    /**
     * @Route("/movies")
     * @Method("GET")
     */
    public function index(MovieRepository $movieRepository)
    {
        $movies = $movieRepository->transformAll();

        return $this->respond($movies);
    }

    /**
     * @Route("/movies/{id}")
     * @Method("GET")
     */
    public function show($id, MovieRepository $movieRepository)
    {
        $movie = $movieRepository->find($id);

        if (!$movie) {
            return $this->respondNotFound();
        }

        $movie = $movieRepository->transform($movie);

        return $this->respond($movie);
    }

    /**
     * @Route("/movies")
     * @Method("POST")
     *
     * @param Request $request
     * @param MovieRepository $movieRepository
     * @param EntityManagerInterface $em
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function create(Request $request, MovieRepository $movieRepository, EntityManagerInterface $em)
    {
        $request = $this->transformjsonbody($request);

        if (!$request) {
            return $this->respondValidationErrors('Please provide a valid request');
        }

        // Validate the title
        if (!$request->get('title')) {
            return $this->respondValidationErrors('Pleas provide a title');
        }

        $movie = new Movie();
        $movie->setTitle($request->get('title'));
        $movie->setCount(0);
        $em->persist($movie);
        $em->flush();

        return $this->respondCreated($movieRepository->transform($movie));
    }

    /**
     * @Route("/movies/{id}/count")
     * @Method("POST")
     *
     * @param int $id
     * @param MovieRepository $movieRepository
     * @param EntityManagerInterface $em
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function increaseCount($id, MovieRepository $movieRepository, EntityManagerInterface $em)
    {
        $movie = $movieRepository->find($id);

        if (!$movie) {
            return $this->respondNotFound();
        }

        $movie->setCount($movie->getCount() + 1);
        $em->persist($movie);
        $em->flush();

        return $this->respond([
            'count' => $movie->getCount()
        ]);
    }
}