<?php

namespace App\Controller;

use App\Entity\Address;
use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AddressController
 * @package App\Controller
 * @Route("/api", name="address_api")
 */
class AddressController extends AbstractController
{
    /**
     * @param AddressRepository $addressRepository
     * @return JsonResponse
     * @Route("/address", name="address", methods={"GET"})
     */
    public function getAddresses(AddressRepository $addressRepository)
    {
        $data = $addressRepository->findAll();
        return $this->response($data);
    }

    /**
     * @param AddressRepository $addressRepository
     * @param $id
     * @return JsonResponse
     * @Route("/address/{id}", name="address_get", methods={"GET"})
     */
    public function getAddress(AddressRepository $addressRepository, $id)
    {
        $address = $addressRepository->find($id);

        if (!$address) {
            $data = [
                'status' => 404,
                'errors' => "Address not found",
            ];
            return $this->response($data, 404);
        }
        return $this->response($address);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param AddressRepository $addressRepository
     * @return JsonResponse
     * @throws \Exception
     * @Route("/address", name="address_add", methods={"POST"})
     */
    public function addAddress(Request $request, EntityManagerInterface $entityManager, AddressRepository $addressRepository)
    {
        try {
            $request = $this->transformJsonBody($request);

            $address = new Address();
            $address->setClient_id($request->get('client_id'));
            $address->setValue($request->get('value'));
            $address->setLat($request->get('lat'));
            $address->setLon($request->get('lon'));
            $address->setPorch($request->get('porch'));
            $address->setFloor($request->get('floor'));
            $address->setRegionId($request->get('regionId'));
            $address->setIntercom($request->get('intercom'));
            $address->setApartment($request->get('apartment'));
            $address->setTitle($request->get('title'));
            $address->setCity($request->get('city'));

            $entityManager->persist($address);
            $entityManager->flush();

            $data = [
                'status' => 200,
                'success' => "Address added successfully",
            ];
            return $this->response($data);

        } catch (\Exception $e) {
            $data = [
                'status' => 422,
                'errors' => "Data no valid",
                'e' => $e->getMessage()
            ];
            return $this->response($data, 422);
        }

    }

    /**
     * @param AddressRepository $addressRepository
     * @param $client_id
     * @return JsonResponse
     * @Route("/client-address/{client_id}", name="client-address_get", methods={"GET"})
     */
    public function getClientAddresses(AddressRepository $addressRepository, $client_id)
    {
        $address = $addressRepository->findBy(['client_id' => $client_id]);

        if (!$address) {
            $data = [
                'status' => 404,
                'errors' => "Address not found",
            ];
            return $this->response($data, 404);
        }
        return $this->response($address);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param AddressRepository $addressRepository
     * @param $id
     * @return JsonResponse
     * @Route("/address/{id}", name="address_put", methods={"PUT"})
     */
    public function updateAddress(Request $request, EntityManagerInterface $entityManager, AddressRepository $addressRepository, $id)
    {
        try {
            $address = $addressRepository->find($id);

            if (!$address) {
                $data = [
                    'status' => 404,
                    'errors' => "Address not found",
                ];
                return $this->response($data, 404);
            }

            $request = $this->transformJsonBody($request);

            $address->setClient_id($request->get('client_id'));
            $address->setValue($request->get('value'));
            $address->setLat($request->get('lat'));
            $address->setLon($request->get('lon'));
            $address->setPorch($request->get('porch'));
            $address->setFloor($request->get('floor'));
            $address->setRegionId($request->get('regionId'));
            $address->setIntercom($request->get('intercom'));
            $address->setApartment($request->get('apartment'));
            $address->setTitle($request->get('title'));
            $address->setCity($request->get('city'));

            $entityManager->flush();

            $data = [
                'status' => 200,
                'errors' => "Address updated successfully",
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
     * @param AddressRepository $addressRepository
     * @param $id
     * @return JsonResponse
     * @Route("/address/{id}", name="address_delete", methods={"DELETE"})
     */
    public function deleteAddress(EntityManagerInterface $entityManager, AddressRepository $addressRepository, $id)
    {
        $address = $addressRepository->find($id);

        if (!$address) {
            $data = [
                'status' => 404,
                'errors' => "Address not found",
            ];
            return $this->response($data, 404);
        }

        $entityManager->remove($address);
        $entityManager->flush();
        $data = [
            'status' => 200,
            'errors' => "Address deleted successfully",
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