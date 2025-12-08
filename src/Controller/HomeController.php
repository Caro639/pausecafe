<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Form\SearchFormType;
use App\Repository\ProductsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    /**
     * Summary of index
     * @param \App\Repository\ProductsRepository $repository
     * @param \App\Entity\Products $product
     * @return Response
     */
    public function index(ProductsRepository $repository, Request $request): Response
    {

        $data = new SearchData();
        $data->page = $request->query->getInt('page', 1);
        $form = $this->createForm(SearchFormType::class, $data);
        $form->handleRequest($request);

        $searchProducts = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $searchProducts = $repository->findSearch($data);
        }

        dump($data, $searchProducts);

        return $this->render(
            'home/index.html.twig',
            [
                'products' => $repository->findProduct(8),
                'slug' => $repository->findBy([], ['slug' => 'ASC']),
                'searchProducts' => $searchProducts,
                'searchDataForm' => $form,
            ]
        );
    }
}
