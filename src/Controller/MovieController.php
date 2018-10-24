<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;


class MovieController extends ApiController
{
    /**
     * @Route("/movies")
     */
    public function moviesAction()
    {
        return $this->respond([
            [
                'title' => 'The princes bride',
                'count' => 0
            ]
        ]);
    }
}