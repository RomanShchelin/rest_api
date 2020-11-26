<?php

namespace App\Controller;

use App\Entity\Street;
use App\Repository\StreetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class StreetController
 * @package App\Controller
 * @Route("/api", name="street_api")
 */
class StreetController extends AbstractController
{
    /**
     * @param StreetRepository $streetRepository
     * @return JsonResponse
     * @Route("/street", name="street", methods={"GET"})
     */
    public function getStreets(StreetRepository $streetRepository)
    {
        $data = $streetRepository->findAll();
        return $this->response($data);
    }

    /**
     * @param StreetRepository $streetRepository
     * @param $id
     * @return JsonResponse
     * @Route("/street/{id}", name="street_get", methods={"GET"})
     */
    public function getStreet(StreetRepository $streetRepository, $id)
    {
        $street = $streetRepository->find($id);

        if (!$street) {
            $data = [
                'status' => 404,
                'errors' => "Street not found",
            ];
            return $this->response($data, 404);
        }
        return $this->response($street);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param StreetRepository $streetRepository
     * @return JsonResponse
     * @throws \Exception
     * @Route("/street", name="street_add", methods={"POST"})
     */
    public function addStreet(Request $request, EntityManagerInterface $entityManager, StreetRepository $streetRepository)
    {
        try {
            $request = $this->transformJsonBody($request);

//            if (!$request || !$request->get('name') || !$request->request->get('description')) {
//                throw new \Exception();
//            }

            $street = new Street();
            $street->setValue($request->get('value'));
            $street->setRegionId($request->get('regionId'));
            $street->setCity($request->get('city'));

            $entityManager->persist($street);
            $entityManager->flush();

            $data = [
                'status' => 200,
                'success' => "Street added successfully",
            ];
            return $this->response($data);

        } catch (\Exception $e) {
            $data = [
                'status' => 422,
                'errors' => "Data no valid",
            ];
            return $this->response($data, 422);
        }

    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param StreetRepository $streetRepository
     * @param $id
     * @return JsonResponse
     * @Route("/street/{id}", name="street_put", methods={"PUT"})
     */
    public function updateStreet(Request $request, EntityManagerInterface $entityManager, StreetRepository $streetRepository, $id)
    {
        try {
            $street = $streetRepository->find($id);

            if (!$street) {
                $data = [
                    'status' => 404,
                    'errors' => "Street not found",
                ];
                return $this->response($data, 404);
            }

            $request = $this->transformJsonBody($request);

//            if (!$request || !$request->get('name') || !$request->request->get('description')) {
//                throw new \Exception();
//            }

            $street->setValue($request->get('value'));
            $street->setRegionId($request->get('regionId'));
            $street->setCity($request->get('city'));

            $entityManager->flush();

            $data = [
                'status' => 200,
                'errors' => "Street updated successfully",
            ];
            return $this->response($data);

        } catch (\Exception $e) {
            $data = [
                'status' => 422,
                'errors' => "Data no valid",
            ];
            return $this->response($data, 422);
        }
    }

    /**
     * @param StreetRepository $streetRepository
     * @param $id
     * @return JsonResponse
     * @Route("/street/{id}", name="street_delete", methods={"DELETE"})
     */
    public function deleteStreet(EntityManagerInterface $entityManager, StreetRepository $streetRepository, $id)
    {
        $street = $streetRepository->find($id);

        if (!$street) {
            $data = [
                'status' => 404,
                'errors' => "Street not found",
            ];
            return $this->response($data, 404);
        }

        $entityManager->remove($street);
        $entityManager->flush();
        $data = [
            'status' => 200,
            'errors' => "Street deleted successfully",
        ];
        return $this->response($data);
    }


    /**
     * Returns a JSON response
     *
     * @param array $data
     * @param $status
     * @param array $headers
     * @return JsonResponse
     */
    public function response($data, $status = 200, $headers = [])
    {
        return new JsonResponse($data, $status, $headers);
    }

    protected function transformJsonBody(\Symfony\Component\HttpFoundation\Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            return $request;
        }

        $request->request->replace($data);

        return $request;
    }

}