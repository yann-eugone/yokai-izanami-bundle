<?php

namespace Yokai\IzanamiBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Yokai\IzanamiBundle\Entity\ViolationRepository;

/**
 * @author Yann Eugoné <yann.eugone@gmail.com>
 */
class ViolationController
{
    /**
     * @var ViolationRepository
     */
    private $repository;

    /**
     * @var NormalizerInterface
     */
    private $normalizer;

    /**
     * @param ViolationRepository $repository
     * @param NormalizerInterface $normalizer
     */
    public function __construct(ViolationRepository $repository, NormalizerInterface $normalizer)
    {
        $this->repository = $repository;
        $this->normalizer = $normalizer;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function get(Request $request)
    {
        $class = $request->query->get('object-class');
        $id = $request->query->get('object-id');

        if (!$class || !$id) {
            throw new BadRequestHttpException;//todo
        }

        $json = [];
        $violations = $this->repository->getForClassAndId($class, $id);
        foreach ($violations as $violation) {
            $json[] = $this->normalizer->normalize($violation);
        }

        return JsonResponse::create($json);
    }
}
